<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FontePagadoraController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ObraController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\Api\AutocompleteController;
use App\Http\Controllers\Api\CategoriaGastoController;
use App\Http\Controllers\Api\GastoController;

Route::apiResource('categorias-gastos', CategoriaGastoController::class)->middleware('auth:sanctum');
Route::apiResource('gastos', GastoController::class)->middleware('auth:sanctum');
Route::apiResource('clients', ClientController::class)->middleware('auth:sanctum');
Route::apiResource('fonte-pagadoras', FontePagadoraController::class)->middleware('auth:sanctum');
Route::get('autocomplete/fontes-pagadoras', [AutocompleteController::class, 'fontesPagadoras'])->middleware('auth:sanctum');

Route::apiResource('obras', ObraController::class)->middleware('auth:sanctum');
Route::get('autocomplete/obras', [AutocompleteController::class, 'obras'])->middleware('auth:sanctum');
Route::get('autocomplete/clientes', [AutocompleteController::class, 'clientes'])->middleware('auth:sanctum');
Route::get('autocomplete/categorias-gastos', [AutocompleteController::class, 'categoriasGastos'])->middleware('auth:sanctum');
Route::get('autocomplete/gastos', [AutocompleteController::class, 'gastos'])->middleware('auth:sanctum');
Route::apiResource('users', UserController::class)->middleware('auth:sanctum');
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('register',[UserAuthController::class,'register']);
Route::post('logout',[UserAuthController::class,'logout'])
  ->middleware('auth:sanctum');