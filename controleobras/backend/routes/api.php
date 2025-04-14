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

// Rotas para o CRUD de obras
Route::apiResource('obras', \App\Http\Controllers\ObraController::class);
