<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserIncident extends Model
{
    protected $fillable = [
        'users_id','incidents_id'
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function incident()
    {
        return $this->belongsTo('App\Models\Incident');
    }
}
