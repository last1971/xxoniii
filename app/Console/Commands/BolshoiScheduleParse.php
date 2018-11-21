<?php

namespace App\Console\Commands;

use App\BolshoiFilm;
use App\BolshoiSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BolshoiScheduleParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bolshoi_schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bolshoi Schedule Parse';

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
        \Log::info('BolshoiSchedule start');
        $films = BolshoiFilm::whereDate('updated_at', Carbon::today())->get();
        $url = 'https://api.bolshoikino.ru/api/getSchedule?cityId=19&marketId=192&filmId=';
        $api = new \App\Library\KinoApi();
        foreach ($films as $film) {
            $doc = \phpQuery::newDocumentHTML($api->bolshoi($url . $film->id));
            if ($film->html == null) {
                $film->html = pq('.step1__film')->htmlOuter();
                $film->save();
            }
            if (pq('[data-date="' . Carbon::now()->format('d.m.Y') . '"]')->html() != ''){
                $times = pq('.step1__seans_info_list[data-date="' . Carbon::now()->format('d.m.Y') . '"] .step1__seans_info .step1__seans_info_list_time a');
                foreach ($times as $time) {
                    $href = pq($time)->attr('href');
                    $hm = mb_split(':', substr(pq($time)->text(), -5));
                    $s = BolshoiSchedule::where('href', $href)->first();
                    if ($s == null) {
                        $s = new BolshoiSchedule();
                        $s->bolshoi_film_id = $film->id;
                        $s->href = $href;
                        $s->date = Carbon::now()->toDateString();
                        $s->start_time = $hm[0] . ':' . $hm[1];
                        $t = Carbon::createFromTime($hm[0], $hm[1])->subHours(3);
                        if ($hm[0] == 0)
                            $t->addDay();
                        $s->start_timestamp = $t->timestamp;
                        $s->save();
                    }
                }
            }
        }
        $day = Carbon::now()->subDay()->toDateString();
        $itogo = BolshoiSchedule::where('date', $day)
            ->select(DB::raw('sum(seat_count) as seat_counts, sum(seat_count - seat_free) as seat_used,
            sum(min_price * (seat_count - seat_free)) as itogo'))->first();
        Mail::raw('В Большом из ' . $itogo->seat_counts . ' мест, было продано ' . $itogo->seat_used .
            ' на ' . $itogo->itogo . ' руб.', function ($message) use ($day) {
            $message->to(['elcopro@gmail.com', 'me@xinet.ru'])->subject('Данные кинопарсера за ' . $day);
        });
        \Log::info('BolshoiSchedule stop');
    }
}
