<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\FontePagadoraController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\UserAuthController;

Route::apiResource('clients', ClientController::class)->middleware('auth:sanctum');
Route::apiResource('fonte-pagadoras', FontePagadoraController::class)->middleware('auth:sanctum');
Route::apiResource('users', UserController::class)->middleware('auth:sanctum');
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('register',[UserAuthController::class,'register']);
Route::post('logout',[UserAuthController::class,'logout'])
  ->middleware('auth:sanctum');