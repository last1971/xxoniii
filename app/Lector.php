<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lector extends Model
{
    //
    public function kinoEvent()
    {
        return $this->hasMany('App\KinoEvent');
    }
}
