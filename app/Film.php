<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    //
    protected $fillable = [
        'id',
    ];

    protected $casts = [
        'json' => 'array',
    ];

    public $incrementing = false;

    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }

}
