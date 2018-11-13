<?php

use Illuminate\Foundation\Inspiring;
use Carbon\Carbon;
require('vendor/electrolinux/phpquery/phpQuery/phpQuery.php');

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
    $url = 'https://api.bolshoikino.ru/api/getFilms?cityId=19&marketId=192';
    $api = new \App\Library\KinoApi();
    $doc = phpQuery::newDocumentHTML($api->bolshoi($url));
    dd(pq('.film_list_item')[0]->attr('id'));
})->describe('test');

