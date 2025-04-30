<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/hello', function () {
    return [
        'message' => 'Hello from Laravel API!',
        'timestamp' => now(),
        'database_connected' => DB::connection()->getPdo() ? true : false,
        'redis_connected' => Redis::connection()->ping() ? true : false,
    ];
});

// Rotas para Obras
Route::get('/obras', 'App\Http\Controllers\ObraController@index');
Route::get('/obras/totals', 'App\Http\Controllers\ObraController@getTotals');
Route::get('/obras/{id}', 'App\Http\Controllers\ObraController@show');
Route::post('/obras', 'App\Http\Controllers\ObraController@store');
Route::put('/obras/{id}', 'App\Http\Controllers\ObraController@update');
Route::delete('/obras/{id}', 'App\Http\Controllers\ObraController@destroy');

// Rotas para o CRUD de categorias de gasto
Route::apiResource('categorias-gasto', \App\Http\Controllers\CategoriaGastoController::class);
