<?php

namespace App\Console\Commands;

use App\KinomaxSchedule;
use App\SimpleFilm;
use App\Theater;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class KinomaxScheduleParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kinomax_parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kinomax Schedule Parse';

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
        $theaters = Theater::where('kinoplan', false)->get();
        $api = new \App\Library\KinoApi();
        foreach ($theaters as $theater) {
            $doc = \phpQuery::newDocumentHTML($api->bolshoi($theater->id));
            $films = pq('.film');
            foreach ($films as $film) {
                $simple_film = SimpleFilm::firstOrCreate(['name' => pq($film)->find('.w-70 a')->text()]);
                $schedules = pq($film)->find('.session');
                foreach ($schedules as $schedule) {
                    $a = pq($schedule)->find('a');
                    $href = pq($a)->attr('href');
                    if (!empty($href)) {
                        $s = KinomaxSchedule::where('href', $href)->first();
                        if ($s == null) {
                            $hm = mb_split(':', substr(pq($a)->text(), -5));
                            $price = preg_replace( '~[^0-9]~', '', pq($schedule)->find('.text-main')->text());
                            $s = new KinomaxSchedule();
                            $s->href = $href;
                            $s->simple_film_id = $simple_film->id;
                            $s->date = Carbon::now()->toDateString();
                            $s->start_time = $hm[0] . ':' . $hm[1];
                            $s->min_price = $price;
                            $s->max_price = $price;
                            $s->theater_id = $theater->id;
                            $t = Carbon::createFromTime($hm[0], $hm[1])->subHours(3);
                            if ($hm[0] == 0)
                                $t->addDay();
                            $s->start_timestamp = $t->timestamp;
                            $s->save();
                        }
                    }
                }
                $day = Carbon::now()->subDay()->toDateString();
                $itogo = KinomaxSchedule::where('date', $day)->where('theater_id', $theater->id)
                    ->select(DB::raw('sum(seat_count) as seat_counts, sum(seat_count - seat_free) as seat_used,
                    sum(min_price * (seat_count - seat_free)) as itogo'))->first();
                Mail::raw('В ' . $theater->name .' из ' . $itogo->seat_counts . ' мест, было продано ' . $itogo->seat_used .
                    ' на ' . $itogo->itogo . ' руб.', function ($message) use ($day) {
                    $message->to(['elcopro@gmail.com', 'me@xinet.ru'])->subject('Данные кинопарсера за ' . $day);
                });
            }
        }
    }
}
