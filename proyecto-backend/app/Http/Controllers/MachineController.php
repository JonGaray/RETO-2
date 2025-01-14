<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MachineController extends Controller
{
    public function index(){
        $machine = Machine::all();
        return response()->json($machine);
    }
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'priority' => 'required|integer',
            'last_maintenance' => 'required|date',
            'status' => 'required|in:habilitado,deshabilitado',
            'sections_id' => 'required|exists:sections,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $machine = Machine::create([
            'name' => $request->name,
            'priority' => $request->priority,
            'last_maintenance' => $request->last_maintenance,
            'status' => $request->status,
            'sections_id' => $request->sections_id,
        ]);
        return response()->json([
            'message' => "Maquina registrada correctamente",
            'maquina' => $machine,
        ]);
    }
}
