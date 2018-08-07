<?php

use Illuminate\Database\Seeder;

class LectorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('lectors')->insert([
            'name' => 'Первый',
        ]);
        DB::table('lectors')->insert([
            'name' => 'Второй',
        ]);
        DB::table('lectors')->insert([
            'name' => 'Третий',
        ]);
    }
}
