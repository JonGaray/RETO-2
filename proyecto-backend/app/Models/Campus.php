<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'name'
    ];
    public function sections()
    {
        return $this->hasMany('App\Models\Section');
    }
}
