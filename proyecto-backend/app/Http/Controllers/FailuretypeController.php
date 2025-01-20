<?php

namespace App\Http\Controllers;

use App\Models\Failuretype;
use Illuminate\Http\Request;

class FailuretypeController extends Controller
{
    public function index()
    {
        $failuretypes = Failuretype::all();
        return response()->json([
            'success' => true,
            'data' => $failuretypes,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $failuretype = new Failuretype();
        $failuretype->name = $request->name;
        $failuretype->save();

        return response()->json([
            'success' => true,
            'failuretype' => $failuretype,
        ], 201);
    }

    public function edit(Request $request, $id)
    {
        $failuretype = Failuretype::find($id);

        if (!$failuretype) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de fallo no encontrado',
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $failuretype->name = $request->name;
        $failuretype->save();

        return response()->json([
            'success' => true,
            'failuretype' => $failuretype,
        ]);
    }
}
