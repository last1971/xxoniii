<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKinoEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kino_events', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->unsignedInteger('article_id');
            $table->unsignedInteger('place_id');
            $table->unsignedInteger('lector_id');
            $table->unsignedInteger('seat_count')->default(1000);
            $table->unsignedInteger('seat_free')->default(1000);
            $table->string('web')->nullable();
            $table->timestamps();
            $table->foreign('article_id')->references('id')->on('articles');
            $table->foreign('place_id')->references('id')->on('places');
            $table->foreign('lector_id')->references('id')->on('lectors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kino_events');
    }
}
