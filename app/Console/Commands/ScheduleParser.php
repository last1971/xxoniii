<?php

namespace App\Console\Commands;

use App\Film;
use App\Library\KinoApi;
use App\Schedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ScheduleParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule_parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule parse';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        \Log::info('ScheduleParser start');
        $api = new KinoApi();
        $now = Carbon::now()->toDateString();
        $url = 'https://kinokassa.kinoplan24.ru/api/web/v1/6440/releases/' . $now;
        $referer = 'https://kinowidget.kinoplan.ru/6440/';
        $data = json_decode($api->get($url, $referer));
        foreach ($data->releases as $release) {
            $film = Film::firstOrNew(['id' => $release->id]);
            if (!isset($film->json)) {
                $url = 'https://kinokassa.kinoplan24.ru/api/web/v1/6440/release/' . $film->id;
                $referer = 'https://kinowidget.kinoplan.ru/6440/' . $film->id;
                $film->json = $api->get($url, $referer);
                $film_data = json_decode($film->json);
                $film->title_ru = $film_data->release->title->ru;
                $film->duration = $film_data->release->duration;
                $film->save();
            }
            foreach ($release->schedule as $sch) {
                $schedule = Schedule::firstOrNew(['id' => $sch->id]);
                if (!isset($schedule->film_id)) {
                    $schedule->film_id = $film->id;
                    $schedule->date = $sch->date;
                    $schedule->hall_title = $sch->hall_title;
                    $schedule->min_price = $sch->min_price;
                    $schedule->max_price = $sch->max_price;
                    $schedule->start_time = $sch->start_time;
                    $schedule->start_timestamp = $sch->start_timestamp;
                    $schedule->utc_offset = $sch->utc_offset;
                    $schedule->save();
                }
            }
        }
        $day = $now = Carbon::now()->subDay()->toDateString();
        $itogo = Schedule::where('date',$day)
            ->select(DB::raw('sum(min_price * (seat_count - seat_free)) as itogo'))->first()->itogo;
        Mail::raw('Итого '  . $itogo . ' руб.', function($message) use ($day) {
            $message->to(['elcopro@gmail.com','me@xinet.ru'])->subject('Данные кинопарсера за ' . $day);
        });
        \Log::info('ScheduleParser stop');
    }
}
