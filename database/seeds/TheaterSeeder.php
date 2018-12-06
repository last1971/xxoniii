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
        \App\Theater::updateOrCreate(['id' => '6440'], ['name' => 'Горизонт', 'kinoplan' => true]);
        \App\Theater::updateOrCreate(['id' => '6665'], ['name' => 'Кинополис Парк', 'kinoplan' => true]);
        \App\Theater::updateOrCreate(['id' => '3963'], ['name' => 'Кинополис Орбита', 'kinoplan' => true]);
        \App\Theater::updateOrCreate(['id' => '321'], ['name' => 'Дом Кино', 'kinoplan' => true]);
        \App\Theater::updateOrCreate(
            ['id' => 'https://kinomax.ru/rostov-imax/'],
            ['name' => 'Киномакс-IMAX Ростов-На-Дону', 'kinoplan' => false]
        );
        \App\Theater::updateOrCreate(
            ['id' => 'https://kinomax.ru/plaza/'],
            ['name' => 'Киномакс-Плаза Ростов-На-Дону', 'kinoplan' => false]
        );
        \App\Theater::updateOrCreate(
            ['id' => 'https://kinomax.ru/don/'],
            ['name' => 'Киномакс-Дон Ростов-На-Дону', 'kinoplan' => false]
        );
        \App\Schedule::where('theater_id', null)->update(['theater_id' => '6440']);
    }
}
