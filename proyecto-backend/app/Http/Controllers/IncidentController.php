<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{
    public function index()
    {
        $incident = Incident::all();
        return response()->json($incident);
    }
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'start_date' => 'string|max:255',
            'end_date' => 'string|max:255',
            'importance' => 'required|in:parada,averia,aviso,mantenimiento',
            'status' => 'required|in:habilitado,deshabilitado',
            'machines_id' => 'required|exists:machines,id',
            'failuretypes_id' => 'required|exists:failuretypes,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $incident = Incident::create([
            'title' => $request->title,
            'description' => $request->description,
            'register_date' => $request->register_date,
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null,
            'importance' => $request->importance,
            'status' => $request->status,
            'machines_id' => $request->machines_id,
            'failuretypes_id' => $request->failuretypes_id
        ]);
        return response()->json([
            'message' => "Incidencia registrada correctamente",
            'incidencia' => $incident,
        ]);
    }
}
