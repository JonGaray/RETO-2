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
    public function edit(Request $request, $id): \Illuminate\Http\JsonResponse
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
        $machine = Machine::find($id);
        if (!$machine) {
            return response()->json(['message' => 'Máquina no encontrada'], 404);
        }
        $machine->name = $request->name;
        $machine->priority = $request->priority;
        $machine->last_maintenance = $request->last_maintenance;
        $machine->status = $request->status;
        $machine->sections_id = $request->sections_id;
        $machine->save();
        return response()->json([
            'message' => 'Máquina actualizada correctamente',
            'maquina' => $machine,
        ]);
    }
    public function updateStatus(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:habilitado,deshabilitado',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $machine = Machine::find($id);
        if (!$machine) {
            return response()->json(['message' => 'Máquina no encontrada'], 404);
        }
        $machine->status = $request->status;
        $machine->save();
        return response()->json([
            'message' => 'Estado de la máquina actualizado correctamente',
            'maquina' => $machine,
        ]);
    }
    public function getBySection(Request $request)
    {
        try {
            // Obtener el parámetro 'section_id' de la solicitud
            $sectionId = $request->input('sections_id');
            // Verificar si section_id es válido
            if (!$sectionId) {
                return response()->json(['error' => 'sections_id es requerido'], 400);
            }
            // Buscar las máquinas asociadas a la sección
            $machines = Machine::where('sections_id', $sectionId)->get();
            // Verificar si se encontraron máquinas
            if ($machines->isEmpty()) {
                return response()->json(['error' => 'No se encontraron máquinas para esta sección'], 404);
            }
            // Retornar las máquinas en formato JSON
            return response()->json(['data' => $machines], 200);
        } catch (\Exception $e) {
            // En caso de error, capturar la excepción y retornar un error interno del servidor
            return response()->json(['error' => 'Error al obtener las máquinas', 'message' => $e->getMessage()], 500);
        }
    }
    public function searchByName(Request $request){
        $query = $request->input('query');

        $machines = Machine::where('name', 'like', "%{$query}%")->get();

        return response()->json($machines);
    }
}
