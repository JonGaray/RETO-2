<?php

namespace App\Http\Controllers;

use App\Models\Failuretype;
use App\Models\Incident;
use App\Models\Machine;
use App\Models\MachineMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MachineMaintenanceController extends Controller
{
    public function index(){
        $machinemaintenance =
            MachineMaintenance::select(
            'machinesmaintenances.*',
            'machines.name as machine_name',
            'maintenances.name as maintenance_name',
            'maintenances.regularity as regularity',
        )
            ->join('machines', 'machinesmaintenances.machines_id', '=', 'machines.id')
            ->join('maintenances', 'machinesmaintenances.maintenances_id', '=', 'maintenances.id')
            ->get();
        ;
        //dd($machinemaintenance);
        return response()->json($machinemaintenance);
    }

    public function create(Request $request)
    {
        // Valida los datos de la asociación
        $validator = Validator::make($request->all(), [
            'machines_id' => 'required|exists:machines,id',
            'maintenances_id' => 'required|exists:maintenances,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Crea la asociación
        $machineMaintenance = MachineMaintenance::create([
            'machines_id' => $request->machines_id,
            'maintenances_id' => $request->maintenances_id,
        ]);

        // Obtén el nombre de la máquina
        $machine = Machine::find($request->machines_id);

        // Crea la incidencia automáticamente
        Incident::create([
            'title' => 'Mantenimiento de máquina ' . $machine->name,  // Título dinámico
            'description' => 'Mantenimiento preventivo',
            'importance' => 'mantenimiento',
            'status' => 'nuevo',
            'machines_id' => $request->machines_id,
            'failuretypes_id' => Failuretype::where('name', 'mantenimiento')->first()->id ?? 1,  // Ajusta según el failuretype de "mantenimiento"
        ]);

        return response()->json([
            'message' => 'Asociación y mantenimiento creados correctamente',
            'data' => $machineMaintenance,
        ]);
    }
}
