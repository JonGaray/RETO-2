<?php

namespace App\Http\Controllers;

use App\Models\Failuretype;
use Illuminate\Http\Request;

class FailuretypeController extends Controller
{
    public function index()
    {
        $failuretypes = FailureType::all();
        return response()->json([
            'success' => true,
            'data' => $failuretypes,
        ], 200);
    }
}
