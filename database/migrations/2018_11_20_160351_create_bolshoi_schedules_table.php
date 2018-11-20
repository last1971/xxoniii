<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBolshoiSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolshoi_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('href');
            $table->string('bolshoi_film_id');
            $table->date('date');
            $table->time('start_time');
            $table->unsignedInteger('utc_offset')->default(3);
            $table->unsignedBigInteger('start_timestamp');
            $table->string('hall_title')->nullable();
            $table->unsignedInteger('max_price')->default(0);
            $table->unsignedInteger('min_price')->default(0);
            $table->unsignedInteger('seat_count')->default(0);
            $table->unsignedInteger('seat_free')->default(0);
            $table->unsignedInteger('parse_state')->default(0);
            $table->boolean('closed')->default(false);
            $table->timestamps();
            $table->primary('id');
            $table->foreign('bolshoi_film_id')->references('id')->on('bolshoi_films');
            $table->index('start_timestamp');
            $table->unique('href');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bolshoi_schedules');
    }
}
