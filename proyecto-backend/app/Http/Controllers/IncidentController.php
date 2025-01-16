<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\UserIncident;
use Carbon\Carbon;
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
            'status' => 'required|in:nuevo,proceso,terminado',
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
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'importance' => 'required|in:parada,averia,aviso,mantenimiento',
            'machines_id' => 'required|exists:machines,id',
            'failuretypes_id' => 'required|exists:failuretypes,id', // Validar que el tipo de falla exista
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 400);
        }

        try {
            // Crear la nueva incidencia
            $incident = Incident::create([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'importance' => $request->input('importance'),
                'machines_id' => $request->input('machines_id'),
                'failuretypes_id' => $request->input('failuretypes_id'),
                'status' => 'nuevo',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Incidencia creada correctamente',
                'data' => $incident,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la incidencia',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateStatus(Request $request, $id){
        $validator = Validator::make($request->all(),['status' => 'required|in:nuevo,proceso,terminado']);
        if ($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $incident = Incident::find($id);
        if (!$incident){
            return response()->json(['message' => 'Incidencia inexistente'], 404);
        }
        $incident->status = $request->status;
        $incident->save();
        return response()->json([
            'message' => 'Estado actualizado',
            'incidencia' => $incident,
        ]);
    }
    public function getAllIncidents()
    {
        $incidents = Incident::select(
            'incidents.*',
            'machines.name as machine_name',
            'failuretypes.name as failure_type_name'
        )
            ->join('machines', 'incidents.machines_id', '=', 'machines.id')
            ->join('failuretypes', 'incidents.failuretypes_id', '=', 'failuretypes.id')
            ->orderByRaw("
            CASE
                WHEN incidents.status = 'nuevo' THEN 1
                WHEN incidents.status = 'proceso' THEN 2
                WHEN incidents.status = 'terminado' THEN 3
                ELSE 4
            END
        ")
            ->orderByRaw("
            CASE
                WHEN incidents.importance = 'parada' THEN 1
                WHEN incidents.importance = 'averia' THEN 2
                WHEN incidents.importance = 'aviso' THEN 3
                ELSE 5
            END
        ")
            ->orderBy('machines.priority', 'asc')
            ->orderByRaw("
            CASE
                WHEN incidents.importance = 'mantenimiento' THEN 1
                ELSE 2
            END
        ")
            ->paginate(3);  // Paginación de 3 por página

        // Devuelves la paginación completa con los datos
        return response()->json($incidents);
    }
    public function countAllIncidents()
    {
        $incidentCount = Incident::count();
        return response()->json(['count' => $incidentCount], 200);
    }
    public function getActiveIncidents() {
        $incidentCount = Incident::where('status', 'proceso')->count();
        return response()->json(['count' => $incidentCount]);
    }
    public function getSolvedToday() {
        $incidentCount = Incident::whereDate('end_date', Carbon::today())->count();
        return response()->json(['count' => $incidentCount]);
    }
    public function acceptIncident($id)
    {
        $incident = Incident::findOrFail($id);
        if ($incident->status == 'nuevo') {
            $incident->status = 'proceso';
            $incident->start_date = now();
            $incident->save();
            return response()->json(['message' => 'Incidencia aceptada']);
        }
        return response()->json(['message' => 'Incidencia no se puede aceptar'], 400);
    }
    // Apuntarse a la incidencia (insertar en la tabla usersincidents)
    public function joinIncident(Request $request, $id)
    {
        $user_id = $request->input('users_id');
        // Comprobar si ya está apuntado
        $exists = UserIncident::where('users_id', $user_id)
            ->where('incidents_id', $id)
            ->exists();
        if (!$exists) {
            UserIncident::create([
                'users_id' => $user_id,
                'incidents_id' => $id,
            ]);
            return response()->json(['message' => 'Te has apuntado a la incidencia']);
        }
        return response()->json(['message' => 'Ya estás apuntado a esta incidencia'], 400);
    }
    // Cambia el estado de la incidencia a 'terminado'
    public function finishIncident($id)
    {
        $incident = Incident::findOrFail($id);
        if ($incident->status == 'proceso') {
            $incident->status = 'terminado';
            $incident->end_date = now();
            $incident->save();
            return response()->json(['message' => 'Incidencia finalizada']);
        }
        return response()->json(['message' => 'Incidencia no se puede finalizar'], 400);
    }
}
