<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::all();
        return response()->json([
            'success' => true,
            'data' => $sections,
        ], 200);
    }
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:habilitado,deshabilitado',
            'campus_id' => 'required|exists:campuses,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $section = Section::create([
            'name' => $request->name,
            'status' => $request->status,
            'campus_id' => $request->campus_id,
        ]);
        return response()->json([
            'message' => "Seccion registrada correctamente",
            'section' => $section,
        ]);
    }
    public function edit(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:habilitado,deshabilitado',
            'campus_id' => 'required|exists:campuses,id'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $section = Section::find($id);
        if (!$section) {
            return response()->json(['message' => 'Seccion no encontrada'], 404);
        }
        $section->name = $request->name;
        $section->status = $request->status;
        $section->campus_id = $request->campus_id;
        $section->save();
        return response()->json([
            'message' => 'Seccion actualizada correctamente',
            'section' => $section,
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
        $section = Section::find($id);
        if (!$section) {
            return response()->json(['message' => 'Seccion no encontrada'], 404);
        }
        $section->status = $request->status;
        $section->save();
        return response()->json([
            'message' => 'Seccion actualizada correctamente',
            'section' => $section,
        ]);
    }
}
