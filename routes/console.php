<?php

use Illuminate\Foundation\Inspiring;
use Carbon\Carbon;

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
    $url = 'https://kinomax.ru/rostov-imax';
    $api = new \App\Library\KinoApi();
    $doc = phpQuery::newDocumentHTML($api->bolshoi($url));
   // dd(pq('.film .w-70 a')->text());
})->describe('test');

