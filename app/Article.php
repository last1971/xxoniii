<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
    public function kinoEvent()
    {
        return $this->hasMany('App\KinoEvent');
    }
}
