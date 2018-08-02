<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    //
    public function places()
    {
        return $this->hasMany('App\Place');
    }
}
