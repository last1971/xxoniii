<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    //
    protected $fillable = [
        'id',
    ];

    public $incrementing = false;


    public function film()
    {
        return $this->belongsTo('App\Film');
    }

    public function theater()
    {
        return $this->belongsTo('App\Theater');
    }
}
