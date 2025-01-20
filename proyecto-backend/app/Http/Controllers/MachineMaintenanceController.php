<?php

namespace App\Http\Controllers;

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

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'machines_id' => 'required|exists:machines,id',
            'maintenances_id' => 'required|exists:maintenances,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $machinemaintenance = MachineMaintenance::create([
            'machines_id' => $request->machines_id,
            'maintenances_id' => $request->maintenances_id
        ]);
        return response()->json([
            'message' => "Asociacion entre maquina y mantenimiento realizado correctamente",
            'asociacion' => $machinemaintenance,
        ]);
    }
}
