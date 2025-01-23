<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Machine;
use App\Models\MachineMaintenance;
use App\Models\UserIncident;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'importance' => 'required|in:parada,averia,aviso,mantenimiento',
            'machines_id' => 'required|exists:machines,id',
            'failuretypes_id' => 'required|exists:failuretypes,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 400);
        }
        try {
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
            ->paginate(4);
        return response()->json($incidents);
    }
    public function getActiveIncidents() {
        $incidentCount = Incident::where('status', 'proceso')->count();
        return response()->json(['count' => $incidentCount]);
    }
    public function getSolvedToday() {
        $incidentCount = Incident::whereDate('end_date', Carbon::today())->count();
        return response()->json(['count' => $incidentCount]);
    }
    public function getImportance($importance)
    {
        $incidents = Incident::select(
            'incidents.*',
            'machines.name as machine_name',
            'failuretypes.name as failure_type_name'
        )
            ->join('machines', 'incidents.machines_id', '=', 'machines.id')
            ->join('failuretypes', 'incidents.failuretypes_id', '=', 'failuretypes.id')
            ->where('incidents.importance', $importance)
            ->orderByRaw("
            CASE
                WHEN incidents.status = 'nuevo' THEN 1
                WHEN incidents.status = 'proceso' THEN 2
                WHEN incidents.status = 'terminado' THEN 3
                ELSE 4
            END
        ")
            ->orderBy('machines.priority', 'asc')
            ->paginate(3);

        return response()->json($incidents);
    }
    public function getFailureType($failureType)
    {
        $incidents = Incident::select(
            'incidents.*',
            'machines.name as machine_name',
            'failuretypes.name as failure_type_name'
        )
            ->join('machines', 'incidents.machines_id', '=', 'machines.id')
            ->join('failuretypes', 'incidents.failuretypes_id', '=', 'failuretypes.id')
            ->where('incidents.failuretypes_id', $failureType)
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
            ->paginate(3);

        return response()->json($incidents);
    }
    public function getSection($section)
    {
        $incidents = Incident::select(
            'incidents.*',
            'machines.name as machine_name',
            'failuretypes.name as failure_type_name'
        )
            ->join('machines', 'incidents.machines_id', '=', 'machines.id')
            ->join('failuretypes', 'incidents.failuretypes_id', '=', 'failuretypes.id')
            ->join('sections', 'machines.sections_id', '=', 'sections.id')
            ->where('sections.id', $section)
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
            ->paginate(3);

        return response()->json($incidents);
    }
    public function getCampus($campus)
    {
        $incidents = Incident::select(
            'incidents.*',
            'machines.name as machine_name',
            'failuretypes.name as failure_type_name'
        )
            ->join('machines', 'incidents.machines_id', '=', 'machines.id')
            ->join('failuretypes', 'incidents.failuretypes_id', '=', 'failuretypes.id')
            ->join('sections', 'machines.sections_id', '=', 'sections.id')
            ->where('sections.campus_id', $campus)
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
            ->paginate(3);
        return response()->json($incidents);
    }
    public function getStatus($status)
    {
        $incidents = Incident::select(
            'incidents.*',
            'machines.name as machine_name',
            'failuretypes.name as failure_type_name'
        )
            ->join('machines', 'incidents.machines_id', '=', 'machines.id')
            ->join('failuretypes', 'incidents.failuretypes_id', '=', 'failuretypes.id')
            ->where('incidents.status', $status)
            ->orderByRaw("
            CASE
                WHEN incidents.status = 'nuevo' THEN 1
                WHEN incidents.status = 'proceso' THEN 2
                WHEN incidents.status = 'terminado' THEN 3
                ELSE 4
            END
        ")
            ->orderBy('machines.priority', 'asc')
            ->paginate(3);

        return response()->json($incidents);
    }
    public function getMachines()
    {
        $machines = Machine::select('id', 'name')
            ->orderBy('priority', 'asc')
            ->get();

        return response()->json($machines);
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
    public function joinIncident(Request $request, $id)
    {
        $user_id = $request->input('users_id');
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
    public function searchByName(Request $request)
    {
    $query = $request->input('query');
    $incidents = Incident::select(
        'incidents.*',
        'machines.name as machine_name',
        'failuretypes.name as failure_type_name'
    )
        ->join('machines', 'incidents.machines_id', '=', 'machines.id')
        ->join('failuretypes', 'incidents.failuretypes_id', '=', 'failuretypes.id')
        ->where('title', 'like', "%{$query}%") // Filtro de búsqueda dinámico
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
        ->paginate(4); // Paginación de 4 por página
    return response()->json($incidents);
    }
    public function generateIncident($machinesMaintenanceId)
    {
        $machinesMaintenance = MachineMaintenance::find($machinesMaintenanceId);
        if (!$machinesMaintenance) {
            return response()->json(['error' => 'La relación máquina-mantenimiento no existe.'], 404);
        }
        $machine = $machinesMaintenance->machine;
        $maintenance = $machinesMaintenance->maintenance;
        $incident = new Incident();
        $incident->title = "{$maintenance->name} para la máquina {$machine->name}";
        $incident->description = "{$maintenance->name} para la máquina {$machine->name}";
        $incident->status = 'nuevo';
        $incident->severity = 'mantenimiento';
        $incident->machine_id = $machine->id;
        $incident->created_at = Carbon::now();
        $incident->next_due_date = Carbon::now()->addDays($maintenance->regularity);
        $incident->save();
        $machinesMaintenance->next_due_date = Carbon::now()->addDays($maintenance->regularity);
        $machinesMaintenance->save();
    }
}
