<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $fillable = [
        'name','campus_id'
    ];
    public function sections()
    {
        return $this->hasMany('App\Models\Section');
    }

    public function campus()
    {
        return $this->belongsTo('App\Models\Campus');
    }
}
