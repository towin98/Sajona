<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
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
Route::resource('propagacion', PropagacionController::class);
