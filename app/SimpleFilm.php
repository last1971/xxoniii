<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SimpleFilm extends Model
{
    //
    protected $fillable = [
        'name',
    ];

    public function kinomax_schedules()
    {
        return $this->hasMany('App\BolshoiSchedule');
    }
}
