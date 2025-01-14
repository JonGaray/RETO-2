<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'title','description','status','register_date','start_date','end_date','importance','machines_id'
    ];
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'usersincidents');
    }
    public function machine()
    {
        return $this->belongsTo('App\Models\Machine');
    }
    public function failuretype()
    {
        return $this->belongsTo('App\Models\Failuretype');
    }
}
