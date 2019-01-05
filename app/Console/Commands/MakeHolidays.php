<?php

namespace App\Console\Commands;

use App\Holiday;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MakeHolidays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'makeHolidays {year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make Holidays';

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
        $year = $this->argument('year');
        $day = new Carbon($year . '-1-1');
        $sms = ['sunday', 'holiday', 'holiday', 'holiday', 'holiday', 'holiday', 'saturday' ];
        while ($day->year == $year) {
            $type = file_get_contents('https://isdayoff.ru/' . str_replace('-', '', $day->toDateString()));
            if ($type == 1) {
                Holiday::firstOrCreate(['day' =>  $day->toDateString(), 'type' => $sms[$day->dayOfWeek] ]);
            }
            $this->info($day->toDateString());
            $day->addDay();
        }
    }
}
