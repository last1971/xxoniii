<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KinomaxSchedule extends Model
{
    //
    public function simple_film()
    {
        return $this->belongsTo('App\SimpleFilm');
    }

    public function theater()
    {
        return $this->belongsTo('App\Theater');
    }
}
