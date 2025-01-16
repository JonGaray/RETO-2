<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
    public function getAvailableTecnicos()
    {
        $techniciansCount = User::where('role', 'Técnico')
            ->where('status', 'habilitado') 
            ->count();

        return response()->json(['techniciansCount' => $techniciansCount]);
    }
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->status = $request->status;
        $user->save();

        return response()->json(['message' => 'Estado actualizado correctamente']);
    }
}
