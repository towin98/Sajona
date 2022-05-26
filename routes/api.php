<?php

use App\Http\Controllers\AlertaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlantaMadre\PlantaMadreController;
use App\Http\Controllers\Propagacion\PropagacionController;
use App\Http\Controllers\Trasplante\TrasplanteController;
use App\Http\Controllers\Baja\BajaController;
use App\Http\Controllers\Cosecha\CosechaController;
use App\Http\Controllers\Parametro\ParametroController;
use App\Http\Controllers\PostCosecha\PostCosechaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*Rutas de autenticacacion*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/password/change', [AuthController::class, 'passwordChange'])->middleware('auth:sanctum');

/*Rutas Controlador PermissionController*/
Route::get('/busca-nombre-rol-user/', [PermissionController::class, 'buscaNombreRolUser'])->middleware('auth:sanctum');
Route::get('/permisos-usuario/', [PermissionController::class, 'buscaPermisosUsuario'])->middleware('auth:sanctum');
Artisan::call('cache:clear');

Route::post('/password/email', [AuthController::class, 'resetPassword']);

/*Rutas Sojana (MENU)*/
Route::group(['prefix' => 'propagacion', 'middleware' => 'auth:sanctum'] , function(){
    Route::get('/listar', [PropagacionController::class, 'listar']);
    Route::get('/id-lote', [PropagacionController::class, 'buscarUltimoIdLote']);
    Route::resource('/', PropagacionController::class)->only(['store']);
    Route::put('/actualizar/{id}', [PropagacionController::class, 'update']);
    Route::get('/mostrar/{id}', [PropagacionController::class, 'show']);
    Route::post('/delete', [PropagacionController::class, 'delete']);
});

/*Planta Madre (MENU)*/
Route::group(['prefix' => 'planta-madre', 'middleware' => 'auth:sanctum'] , function(){
    Route::resource('/', PlantaMadreController::class)->only(['store']);
    Route::get('/buscar-lotes', [PlantaMadreController::class, 'buscarLotes']);
    Route::get('/{Propagacion}', [PlantaMadreController::class, 'show']);
});

Route::group(['prefix' => 'trasplante-bolsa', 'middleware' => 'auth:sanctum'] , function(){
    Route::post('/',  [TrasplanteController::class,'storeTrasplanteBolsa']);
    Route::get('/buscar', [TrasplanteController::class, 'buscarTrasplanteBolsa']);
    Route::get('/{id}', [TrasplanteController::class, 'showTrasplanteBolsa']);
});

Route::group(['prefix' => 'trasplante-campo', 'middleware' => 'auth:sanctum'] , function(){
    Route::post('/',  [TrasplanteController::class, 'storeTrasplanteCampo']);
    Route::get('/buscar', [TrasplanteController::class, 'buscarTrasplanteCampo']);
    Route::get('/{id}', [TrasplanteController::class, 'showTrasplanteCampo']);
});

Route::group(['prefix' => 'cosecha', 'middleware' => 'auth:sanctum'] , function(){
    Route::post('/',  [CosechaController::class, 'storeCosecha']);
    Route::get('/buscar', [CosechaController::class, 'buscarCosechas']);
    Route::get('/{id_trasplante}', [CosechaController::class, 'showCosecha']);
    Route::post('/delete', [CosechaController::class, 'deleteCosecha']);
});

Route::group(['prefix' => 'post-cosecha' , 'middleware' => 'auth:sanctum'] , function(){
    Route::post('/',  [PostCosechaController::class, 'storePostCosecha']);
    Route::get('/buscar', [PostCosechaController::class, 'buscarPostCosechas']);
    Route::get('/{id_cosecha}', [PostCosechaController::class, 'showPosCosecha']);
    Route::post('/delete', [PostCosechaController::class, 'deletePostCosecha']);
});

Route::group(['prefix' => 'baja', 'middleware' => 'auth:sanctum'] , function(){
    Route::resource('/',  BajaController::class)->only(['store']);
    Route::get('/buscar', [BajaController::class, 'buscarLotes']);
    Route::get('/{id_lote}', [BajaController::class, 'show']);
});

//SISTEMA
Route::group(['prefix' => 'parametro', 'middleware' => 'auth:sanctum'] , function(){
    Route::resource('/',  ParametroController::class)->only(['store']);
    Route::put('/{id}', [ParametroController::class, 'update']);
    Route::get('/buscar', [ParametroController::class, 'buscar']);
    Route::get('/{parametrica}/{id}', [ParametroController::class, 'show']);
});

Route::group(['prefix' => 'sistema', 'middleware' => 'auth:sanctum'] , function(){
    Route::post('alerta',  [AlertaController::class, 'store']);
    Route::put('alerta/{id}', [AlertaController::class, 'update']);
    Route::get('alerta', [AlertaController::class, 'show']);
});
