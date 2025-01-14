<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function index(){
        $machine = Campus::all();
        return response()->json($machine);
    }
}
