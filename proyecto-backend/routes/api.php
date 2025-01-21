<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampusController;
use App\Http\Controllers\FailuretypeController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MachineMaintenanceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserIncidentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





Route::controller(AuthController::class)->prefix('auth')->group(function()
{
    Route::post('login', 'login');
    Route::post('register', 'register')->middleware('auth:api', 'admin');
    Route::post('logout', 'logout')->middleware('auth:api');
    Route::post('refresh', 'refresh')->middleware('auth:api');
    Route::get('me', 'me')->middleware('auth:api');
    Route::get('/users', 'index')->middleware('auth:api');
    Route::put('/users/{id}/status', 'updateStatus')->middleware('auth:api');
    Route::get('/technicians', 'getAvailableTecnicos')->middleware('auth:api');
    Route::put('/users/create', 'create')->middleware('auth:api');
    Route::put('/users/{id}/save', 'save')->middleware('auth:api');
    Route::get('/users/search', 'searchByName')->middleware('auth:api');
});
Route::controller(IncidentController::class)->prefix('auth')->group(function()
{
    Route::post('incidents', 'index')->middleware('auth:api');
    Route::post('incidents/create', 'create')->middleware('auth:api'); // Creación de la incidencia
    Route::put('incidents/{id}/status', 'updateStatus')->middleware('auth:api');
    Route::get('incidents/getall', 'getAllIncidents')->middleware('auth:api');
    Route::get('incidents/count','countAllIncidents')->middleware('auth:api');
    Route::get('incidents/activeincidents','getActiveIncidents')->middleware('auth:api');
    Route::get('incidents/solvedtoday','getSolvedToday')->middleware('auth:api');
    Route::post('/incidents/{id}/accept', [IncidentController::class, 'acceptIncident'])->middleware('auth:api');
    Route::post('/incidents/{id}/join', [IncidentController::class, 'joinIncident'])->middleware('auth:api');
    Route::post('/incidents/{id}/finish', [IncidentController::class, 'finishIncident'])->middleware('auth:api');
    Route::post('/incidents/store', [IncidentController::class, 'store'])->middleware('auth:api');
});

Route::controller(MachineController::class)->prefix('auth')->group(function (){
    Route::get('machines','index')->middleware('auth:api');
    Route::get('machines/getsections', 'getBySection')->middleware('auth:api');
    Route::get('machines/search', 'searchByName')->middleware('auth:api');
    Route::post('machines/create','create')->middleware('auth:api');
    Route::put('machines/{id}/edit','edit')->middleware('auth:api');
    Route::put('machines/{id}/status','updateStatus')->middleware('auth:api');
});
Route::controller(CampusController::class)->prefix('auth')->group(function (){
    Route::get('campuses','index')->middleware('auth:api');
    Route::post('campuses/create','create')->middleware('auth:api');
    Route::put('campuses/{id}/edit','edit')->middleware('auth:api');
    Route::put('campuses/{id}/status','updateStatus')->middleware('auth:api');
});
Route::controller(SectionController::class)->prefix('auth')->group(function (){
    Route::get('sections','index')->middleware('auth:api');
    Route::get('sections/search', 'searchByName')->middleware('auth:api');
    Route::get('sections/getsections','getSections')->middleware('auth:api');
    Route::post('sections/create','create')->middleware('auth:api');
    Route::put('sections/{id}/edit','edit')->middleware('auth:api');
    Route::put('sections/{id}/status','updateStatus')->middleware('auth:api');
    Route::get('sections/getbycampus/{campus_id}','getSectionsByCampus')->middleware('auth:api');
});
Route::controller(MaintenanceController::class)->prefix('auth')->group(function (){
    Route::post('maintenances','index')->middleware('auth:api');
    Route::post('maintenances/create','create')->middleware('auth:api');
    Route::put('maintenances/{id}/edit','edit')->middleware('auth:api');
});
Route::controller(MachineMaintenanceController::class)->prefix('auth')->group(function (){
    Route::post('machinemaintenances','index')->middleware('auth:api');
    Route::post('machinemaintenances/create','create')->middleware('auth:api');
});
Route::controller(UserIncidentController::class)->prefix('auth')->group(function (){
    Route::post('userincidents','index')->middleware('auth:api');
    Route::post('userincidents/create','create')->middleware('auth:api');
    Route::get('userincidents/{id}/count', 'getIncidentCountByUser')->middleware('auth:api');
});
Route::controller(FailuretypeController::class)->prefix('auth')->group(function (){
    Route::get('failuretypes', 'index')->middleware('auth:api');
    Route::post('failuretypes/create', 'create')->middleware('auth:api');
    Route::put('failuretypes/{id}/edit', 'edit')->middleware('auth:api');
});

