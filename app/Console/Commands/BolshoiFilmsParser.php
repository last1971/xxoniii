<?php

namespace App\Console\Commands;

use App\BolshoiFilm;
use Carbon\Carbon;
use Illuminate\Console\Command;


class BolshoiFilmsParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bolshoi_films';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bolshoi Films Parser';

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
        $url = 'https://api.bolshoikino.ru/api/getFilms?cityId=19&marketId=192';
        $api = new \App\Library\KinoApi();
        $doc = \phpQuery::newDocumentHTML($api->bolshoi($url));
        foreach (pq('.film_list_item') as $film) {

            $id = str_replace('film_', '', pq($film)->attr('id'));
            $title_ru = pq($film)->find('.film_name')->text();
            $bf = BolshoiFilm::firstOrNew([ 'id' => $id ]);
            $bf->title_ru = $title_ru;
            $bf->updated_at = Carbon::now();
            $bf->save();

        }

    }
}
