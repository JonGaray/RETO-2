<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineMaintenance extends Model
{
    protected $table = 'machinesmaintenances';
    protected $fillable = [
        'machines_id','maintenances_id'
    ];
    public function machine()
    {
        return $this->belongsTo('App\Models\Machine');
    }

    public function maintenance()
    {
        return $this->belongsTo('App\Models\Maintenance');
    }
}
