<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlantaMadre\PlantaMadreController;
use App\Http\Controllers\Propagacion\PropagacionController;
use App\Http\Controllers\prueba;
use App\Http\Controllers\Transplante\TransplanteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*Rutas de autenticacacion*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

/*Rutas Controlador PermissionController*/
Route::get('/busca-nombre-rol-user/', [PermissionController::class, 'buscaNombreRolUser'])->middleware('auth:sanctum');

/*Rutas Sojana (MENU)*/
Route::group(['prefix' => 'propagacion', 'middleware' => 'auth:sanctum'] , function(){
    Route::get('/listar', [PropagacionController::class, 'listar']);
    Route::get('/id-lote', [PropagacionController::class, 'buscarUltimoIdLote']);
    Route::resource('/', PropagacionController::class)->only(['store']);
});

/*Planta Madre (MENU)*/
Route::group(['prefix' => 'planta-madre', /* 'middleware' => 'auth:sanctum' */] , function(){
    Route::resource('/', PlantaMadreController::class)->only(['store']);
    Route::get('/buscar-lotes', [PlantaMadreController::class, 'buscarLotes']);
    Route::get('/{Propagacion}', [PlantaMadreController::class, 'show']);
});

Route::group(['prefix' => 'transplante-bolsa'/* , 'middleware' => 'auth:sanctum' */] , function(){
    Route::resource('/',  TransplanteController::class)->only(['store']);
    Route::get('/buscar', [TransplanteController::class, 'buscar']);
    Route::get('/{id}', [TransplanteController::class, 'show']);
});

Route::group(['prefix' => 'transplante-campo'/* , 'middleware' => 'auth:sanctum' */] , function(){
    // Route::resource('/',  TransplanteController::class)->only(['store']);
});
