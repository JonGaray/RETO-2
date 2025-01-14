<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'priority','name','status','last_maintenance','sections_id'
    ];
    public function section()
    {
        return $this->belongsTo('App\Models\Section');
    }

    public function incidents()
    {
        return $this->hasMany('App\Models\Incident');
    }
    public function maintenances()
    {
        return $this->belongsToMany('App\Models\Maintenance', 'machinesmaintenances');
    }
}
