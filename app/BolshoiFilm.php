<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BolshoiFilm extends Model
{
    //
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;

    public function bolshoi_schedules()
    {
        return $this->hasMany('App\BolshoiSchedule');
    }

}
