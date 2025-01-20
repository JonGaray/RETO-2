<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampusController extends Controller
{
    public function index(){
        $machine = Campus::all();
        return response()->json($machine);
    }
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $campus = new Campus();
        $campus->name = $request->name;
        $campus->status = 'habilitado';
        $campus->save();

        return response()->json(['campus' => $campus], 201);
    }
    public function edit(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $campus = Campus::find($id);
        if (!$campus) {
            return response()->json(['message' => 'Campus no encontrado'], 404);
        }

        $campus->name = $request->name;
        $campus->save();

        return response()->json(['campus' => $campus]);
    }
    public function updateStatus(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:habilitado,deshabilitado',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $campus = Campus::find($id);
        if (!$campus) {
            return response()->json(['message' => 'Campus no encontrado'], 404);
        }
        $campus->status = $request->status;
        $campus->save();
        return response()->json([
            'message' => 'Campus actualizado correctamente',
            'campus' => $campus,
        ]);
    }
}
