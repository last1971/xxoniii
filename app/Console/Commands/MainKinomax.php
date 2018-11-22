<?php

namespace App\Console\Commands;

use App\KinomaxSchedule;
use App\Library\KinoApi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MainKinomax extends Command
{
    protected $api;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'main_kinomax';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Main Kinomax';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->api = new KinoApi();
        parent::__construct();
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
        $start = Carbon::now()->addMinutes(21)->timestamp;
        $end = Carbon::now()->addMinutes(61)->timestamp;
        $schedules = KinomaxSchedule::with('simple_film', 'theater')->where('closed', false)
            ->whereBetween('start_timestamp', [$start, $end])
            ->orWhere('parse_state', true)->get();
        foreach ($schedules as $schedule) {
            $film_time =  Carbon::createFromTimestamp($schedule->start_timestamp)->subMinutes(22);
            $now = Carbon::now();
            if ($schedule->parse_state == 0 || ($schedule->parse_state == 1 && $now >= $film_time)) {
                $html = $this->api->bolshoi('https://kinomax.ru' . $schedule->href);
                $doc = \phpQuery::newDocumentHTML($html);
                $schedule->hall_title = pq(pq('dl dd')->get(6))->text();
                $seq = pq('#container-indicator')->attr('data-seq');
                $z = $this->api->get_chunck_https('kinomax.ru','/order/checkstatus?seq=' . $seq);
                $z = str_replace('\t', '', $z);
                $doc = \phpQuery::newDocumentHTML(json_decode($z)->html);
                $free = count(pq('.status-1'));
                $full = count(pq('.status-2')) - 2 + count(pq('.status-3'));
                if ($full >= 0) {
                    $schedule->seat_count = $free + $full;
                    $schedule->seat_free = $free;
                }
                $schedule->parse_state++;
                if ($schedule->parse_state == 2)
                    $schedule->closed = true;
                $schedule->save();
                \Log::info('MainParser, parsed ' . $schedule->id . ' ' . $schedule->theater->name . ' ' .
                    $schedule->simple_film->name .
                    ' в ' . $schedule->hall_title . ' начaло в ' . $schedule->start_time);
            }
        }
    }
}
