<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BolshoiSchedule extends Model
{
    //
    public function bolshoi_film()
    {
        return $this->belongsTo('App\BolshoiFilm');
    }
}
