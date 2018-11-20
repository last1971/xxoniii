<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBolshoiFilmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bolshoi_films', function (Blueprint $table) {
            $table->string('id');
            $table->string('title_ru');
            $table->text('html')->nullable();
            $table->timestamps();
            $table->primary('id');
            $table->index('title_ru');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bolshoi_films');
    }
}
