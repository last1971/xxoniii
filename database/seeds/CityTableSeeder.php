<?php

use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('cities')->insert([
            'name' => 'Ростов-на-Дону',
        ]);
        DB::table('cities')->insert([
            'name' => 'Москва',
        ]);
        DB::table('cities')->insert([
            'name' => 'Азов',
        ]);


    }
}
