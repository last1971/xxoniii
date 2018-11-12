<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('test', function () {
    \App\Theater::updateOrCreate(['id' => '6440', 'name' => 'Горизонт']);
    \App\Theater::updateOrCreate(['id' => '6665', 'name' => 'Кинополис Парк']);
    \App\Theater::updateOrCreate(['id' => '3963', 'name' => 'Кинополис Орбита']);
    \App\Schedule::where('theater_id', null)->update(['theater_id' => '6440']);
})->describe('test');

