<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Theater extends Model
{
    //
    protected $fillable = [
        'id', 'name'
    ];

    public $incrementing = false;

    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }
}
