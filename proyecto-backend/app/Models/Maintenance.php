<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
        'regularity','name'
    ];
    public function machines()
    {
        return $this->belongsToMany('App\Models\Machine', 'machinesmaintenances');
    }
}
