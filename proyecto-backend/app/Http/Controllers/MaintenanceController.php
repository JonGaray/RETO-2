<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaintenanceController extends Controller
{
    public function index(){
        $maintenances = Maintenance::all();
        return response()->json($maintenances);
    }
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'regularity' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $maintenance = Maintenance::create([
            'name' => $request->name,
            'regularity' => $request->regularity,
        ]);
        return response()->json([
            'message' => "Mantenimiento registrado correctamente",
            'maintenance' => $maintenance,
        ]);
    }
    public function edit(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'regularity' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $maintenance = Maintenance::find($id);
        if (!$maintenance) {
            return response()->json(['message' => 'mantenimiento no encontrado'], 404);
        }
        $maintenance->name = $request->name;
        $maintenance->regularity = $request->regularity;
        $maintenance->save();
        return response()->json([
            'message' => 'mantenimiento actualizado correctamente',
            'maintenance' => $maintenance,
        ]);
    }
}
