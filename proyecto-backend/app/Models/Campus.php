<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = [
        'name'
    ];
    public function workshops()
    {
        return $this->hasMany('App\Models\Workshop');
    }
}
