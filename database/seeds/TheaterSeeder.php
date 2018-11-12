<?php

use Illuminate\Database\Seeder;

class TheaterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \App\Theater::updateOrCreate(['id' => '6440', 'name' => 'Горизонт']);
        \App\Theater::updateOrCreate(['id' => '6665', 'name' => 'Кинополис Парк']);
        \App\Theater::updateOrCreate(['id' => '3963', 'name' => 'Кинополис Орбита']);
        \App\Schedule::where('theater_id', null)->update(['theater_id' => '6440']);
    }
}
