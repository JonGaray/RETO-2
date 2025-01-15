<?php

namespace App\Http\Controllers;

use App\Models\UserIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserIncidentController extends Controller
{
    public function index(){
        $userincidents = UserIncident::all();
        return response()->json($userincidents);
    }
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'users_id' => 'required|exists:users,id',
            'incidents_id' => 'required|exists:incidents,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $userincidents = UserIncident::create([
            'users_id' => $request->users_id,
            'incidents_id' => $request->incidents_id
        ]);
        return response()->json([
            'message' => "Asociacion entre usuario y incidencia realizada correctamente",
            'asociacion' => $userincidents,
        ]);
    }
    public function getIncidentCountByUser($userId) {
        $incidentCount = UserIncident::where('users_id', $userId)->count();
        return response()->json(['count' => $incidentCount]);
    }
}
