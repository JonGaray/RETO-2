<?php

namespace App\Models;

use App\Http\Controllers\IncidentController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserIncident extends Model
{
    protected $table = 'usersincidents';
    protected $fillable = [
        'users_id','incidents_id'
    ];
    public function user()
    {
        return $this->belongsTo('App\Models\User','users_id');
    }

    public function incident()
    {
        return $this->belongsTo('App\Models\Incident', 'incidents_id');
    }

}
