<?php

namespace App\Http\Controllers;

use App\Models\Incident;
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
            'machines.name as machine_name',  // Nombre de la máquina
            'failuretypes.name as failure_type_name'  // Nombre del tipo de fallo
        )
            ->join('machines', 'incidents.machines_id', '=', 'machines.id')
            ->join('failuretypes', 'incidents.failuretypes_id', '=', 'failuretypes.id') // Unimos con la tabla failuretypes
            ->orderByRaw("
            CASE
                WHEN incidents.status = 'nuevo' THEN 1
                WHEN incidents.status = 'proceso' THEN 2
                WHEN incidents.status = 'terminado' THEN 3
                ELSE 4
            END
        ")  // Ordenamos primero por status (nuevo > proceso > terminado)
            ->orderByRaw("
            CASE
                WHEN incidents.importance = 'parada' THEN 1
                WHEN incidents.importance = 'averia' THEN 2
                WHEN incidents.importance = 'aviso' THEN 3
                ELSE 5
            END
        ")  // Luego por importancia (parada > averia > aviso > mantenimiento)
            ->orderBy('machines.priority', 'asc')  // Después por la prioridad de la máquina
            ->orderByRaw("
            CASE
                WHEN incidents.importance = 'mantenimiento' THEN 1
                ELSE 2
            END
        ")  // Finalmente, aseguramos que 'mantenimiento' sea priorizado en caso de igualdad de otros campos
            ->paginate(5);  // Paginación

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
}
