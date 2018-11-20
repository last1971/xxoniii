<?php

namespace App\Console\Commands;

use App\BolshoiSchedule;
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
        $start = Carbon::now()->subMinutes(10)->timestamp;
        $end = Carbon::now()->addMinutes(30)->timestamp;
        $schedules = BolshoiSchedule::where('closed', false)
            ->whereBetween('start_timestamp', [$start, $end])
            ->orWhere('parse_state', true)->get();
        foreach ($schedules as $schedule) {
            $film_time =  Carbon::createFromTimestamp($schedule->start_timestamp);
            $now = Carbon::now()->addMinutes(3);
            if ($schedule->parse_state == 0 || ($schedule->parse_state == 1 && $now >= $film_time)) {
                $html = $this->api->bolshoi('https://api.bolshoikino.ru' . $schedule->href);
                $start = strpos($html, '{"apiKey"');
                $end = strpos($html, '"}');
                $a = json_decode(substr($html, $start, $end - $start + 2));
                $start = strpos($html, '/api');
                $end = strpos($html, '=22');
                $h = substr($html, $start, $end - $start + 3);
                $html = $this->api->bolshoi('https://api.bolshoikino.ru' . $h);
                $start = strpos($html, '{"hallid');
                $h = json_decode(substr($html, $start, strlen($html) - $start - 2));
                $schedule->seat_count = count($h->seats);
                foreach ($h->seats as $seat) {
                    if ($schedule->min_price == 0 || $schedule->min_price > $seat->price)
                        $schedule->min_price = $seat->price;
                    if ($schedule->max_price == 0 || $schedule->max_price < $seat->price)
                        $schedule->max_price = $seat->price;
                }
                $fields = array(
                    'apikey' => $a->apiKey,
                    'showId' => $a->showId,
                    'marketId' => 22,
                    'full' => 0
                );
                $b = json_decode($this->api->bolshoi_post('https://api.bolshoikino.ru/getBusySeats', $fields));
                if (isset($b->data->seats))
                    $schedule->seat_free = $schedule->seat_count - count($b->data->seats);
                $schedule->parse_state++;
                if ($schedule->parse_state == 2)
                    $schedule->closed = true;
                $schedule->save();
            }
        }
        $start = Carbon::now()->subMinutes(10)->timestamp;
        $end = Carbon::now()->addMinutes(30)->timestamp;
        $schedules = Schedule::where('closed', false)
            ->whereBetween('start_timestamp', [$start, $end])
            ->orWhere('parse_state', true)->get();
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
            $referer = 'https://kinowidget.kinoplan.ru/' . $shedule->theater_id . '/' . $shedule->film_id . '/' . $shedule->id;
            $s = json_decode($this->api->get($url, $referer));
            if (isset($s->seats)) {
                $shedule->seat_count = count($s->seats);
                $shedule->seat_free = count($s->seats);;
                foreach ($s->seats as $seat) {
                    if (!$seat->is_available) $shedule->seat_free--;
                }
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
