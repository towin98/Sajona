<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlantaMadre\PlantaMadreController;
use App\Http\Controllers\Propagacion\PropagacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*Rutas de autenticacacion*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

/*Rutas Controlador PermissionController*/
Route::get('/busca-nombre-rol-user/', [PermissionController::class, 'buscaNombreRolUser'])->middleware('auth:sanctum');

/*Rutas Sojana (MENU)*/
Route::get('/propagacion/listar', [PropagacionController::class, 'listar'])->middleware('auth:sanctum');
Route::get('/propagacion/id-lote', [PropagacionController::class, 'buscarUltimoIdLote'])->middleware('auth:sanctum');
Route::resource('/propagacion', PropagacionController::class)->only(['store'])->middleware('auth:sanctum');

/*Planta Madre (MENU)*/
Route::resource('/planta-madre', PlantaMadreController::class)->only(['store'])->middleware('auth:sanctum');
Route::get('/planta-madre/buscar-lotes', [PlantaMadreController::class, 'buscarLotes']);
Route::get('/planta-madre/{Propagacion}', [PlantaMadreController::class, 'show'])->middleware('auth:sanctum');
