<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->string('id');
            $table->date('date');
            $table->string('hall_title');
            $table->unsignedInteger('max_price');
            $table->unsignedInteger('min_price');
            $table->time('start_time');
            $table->unsignedInteger('utc_offset');
            $table->string('film_id');
            $table->unsignedInteger('seat_count')->default(0);
            $table->unsignedInteger('seat_free')->default(0);
            $table->boolean('closed')->default(false);
            $table->timestamps();
            $table->primary('id');
            $table->foreign('film_id')->references('id')->on('films');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
