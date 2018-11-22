<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKinomaxSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kinomax_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('href');
            $table->unsignedInteger('simple_film_id');
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
            $table->foreign('simple_film_id')->references('id')->on('simple_films');
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
        Schema::dropIfExists('kinomax_schedules');
    }
}
