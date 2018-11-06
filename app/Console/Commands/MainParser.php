<?php

namespace App\Console\Commands;

use App\Library\KinoApi;
use App\Schedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MainParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $api;

    protected $signature = 'main_parser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Main Parser';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->api = new KinoApi();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        /*DB::listen(function($query) {
            var_dump($query->sql, $query->bindings);
        });*/
        $start = Carbon::now()->addHour(3)->subMinutes(10);
        $end = Carbon::now()->addHour(3)->addMinutes(30);
        $schedules = Schedule::where('closed', false)
            ->where('date', $start->toDateString())
            ->whereBetween('start_time', [$start->hour . ':' . $start->minute, $end->hour . ':' . $end->minute])
            ->get();
        foreach ($schedules as $schedule) {
            if ($schedule->parse_state == 0) {
                $this->parse($schedule);
            } else {
                $film_time =  Carbon::createFromTimestamp($schedule->start_timestamp);
                $now = Carbon::now()->addHour(3);
                if ($now >= $film_time) {
                    $this->parse($schedule);
                }
            }

        }
    }

    public function parse(Schedule $shedule)
    {
        try {
            $url = 'https://kinokassa.kinoplan24.ru/api/v2/seance/' . $shedule->id;
            $referer = 'https://kinowidget.kinoplan.ru/6440/' . $shedule->film_id . '/' . $shedule->id;
            $seats = json_decode($this->api->get($url, $referer))->seats;
            $shedule->seat_count = count($seats);
            $shedule->seat_free = count($seats);;
            foreach ($seats as $seat) {
                if (!$seat->is_available) $shedule->seat_free--;
            }
            $shedule->parse_state++;
            $shedule->closed = $shedule->parse_state > 1;
            $shedule->save();
            \Log::info('MainParser, parsed ' . $shedule->id);
            return true;
        } catch (\Exception $e) {
            \Log::info('MainParser error - ' . $e->getMessage());
            return false;
        }
    }
}
