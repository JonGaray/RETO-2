<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function getIncidents()
    {
        $incident = Incident::all();
        return response()->json($incident);
    }
}
