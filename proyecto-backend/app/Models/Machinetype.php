<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machinetype extends Model
{
    protected $fillable = [
        'name','sections_id'
    ];
    public function section()
    {
        return $this->belongsTo('App\Models\Section');
    }

    public function machines()
    {
        return $this->hasMany('App\Models\Machine');
    }
}
