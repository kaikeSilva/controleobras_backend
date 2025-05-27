<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/test-form', [TestController::class, 'showTestForm'])->name('test.form');
// Route::post('/test-submit', [TestController::class, 'submitTestForm'])->name('test.submit');
// Route::get('/test-status', [TestController::class, 'getStatus'])->name('test.status');

// // WebSocket Test Routes
// Route::get('/websocket-test', [TestController::class, 'showWebSocketTest'])->name('websocket.test.show');

// Swagger UI
Route::get('/swagger-ui', function () {
    return view('swagger');
});
// Route::post('/send-websocket-test', [TestController::class, 'sendWebSocketTest'])->name('websocket.test.send');
// Route::get('/websocket-status', [TestController::class, 'getWebSocketStatus'])->name('websocket.status.get');
// Route::post('/broadcast-test-message', [TestController::class, 'broadcastTestMessage'])->name('websocket.test.message');
// Route::post('/broadcast-test-notification', [TestController::class, 'broadcastTestNotification'])->name('websocket.test.notification');

// Relatórios PDF
Route::get('relatorios/gastos', [DashboardController::class, 'relatorio'])->name('relatorio');
