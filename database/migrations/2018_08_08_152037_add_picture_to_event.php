<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPictureToEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kino_events', function (Blueprint $table) {
            //
            $table->string('dtp_small')->nullable();
            $table->string('dtp_big')->nullable();
            $table->string('mob_small')->nullable();
            $table->string('mob_big')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kino_events', function (Blueprint $table) {
            //
        });
    }
}
