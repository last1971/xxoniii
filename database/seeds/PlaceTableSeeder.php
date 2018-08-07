<?php

use Illuminate\Database\Seeder;

class PlaceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('places')->insert([
            'name' => 'Большой',
            'city_id' => 1
        ]);
        DB::table('places')->insert([
            'name' => 'Горизонт',
            'city_id' => 1
        ]);
        DB::table('places')->insert([
            'name' => 'Кантина',
            'city_id' => 3
        ]);
    }
}
