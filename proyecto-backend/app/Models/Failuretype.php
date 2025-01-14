<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Failuretype extends Model
{
    public function incidents()
    {
        return $this->hasMany('App\Models\Incident');
    }
}
