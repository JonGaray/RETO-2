<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'name','workshops_id'
    ];
    public function workshop()
    {
        return $this->belongsTo('App\Models\Workshop');
    }

    public function machinetypes()
    {
        return $this->hasMany('App\Models\Machinetype');
    }
}
