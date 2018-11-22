<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('schedule_parse')->
            dailyAt('1:00');
        $schedule->command('bolshoi_films')->
            dailyAt('1:10');
        $schedule->command('kinomax_parse')->
            dailyAt('1:20');
        $schedule->command('bolshoi_schedule')->
            dailyAt('1:30');
        $schedule->command('main_parse')->withoutOverlapping();
        $schedule->command('main_kinomax')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
