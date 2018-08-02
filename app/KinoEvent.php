<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KinoEvent extends Model
{
    //
    public function article()
    {
        return $this->belongsTo('App\Article');
    }
    public function lector()
    {
        return $this->belongsTo('App\Lector');
    }
    public function place()
    {
        return $this->belongsTo('App\Place');
    }
}
