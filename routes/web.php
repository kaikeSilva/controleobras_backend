<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-form', [TestController::class, 'showTestForm'])->name('test.form');
Route::post('/test-submit', [TestController::class, 'submitTestForm'])->name('test.submit');
Route::get('/test-status', [TestController::class, 'getStatus'])->name('test.status');

// WebSocket Test Routes
Route::get('/websocket-test', [TestController::class, 'showWebSocketTest'])->name('websocket.test.show');
Route::post('/send-websocket-test', [TestController::class, 'sendWebSocketTest'])->name('websocket.test.send');
Route::get('/websocket-status', [TestController::class, 'getWebSocketStatus'])->name('websocket.status.get');
Route::post('/broadcast-test-message', [TestController::class, 'broadcastTestMessage'])->name('websocket.test.message');
Route::post('/broadcast-test-notification', [TestController::class, 'broadcastTestNotification'])->name('websocket.test.notification');
