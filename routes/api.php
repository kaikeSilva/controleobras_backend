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
use App\Http\Controllers\Api\EntradaRecursoController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExemploController;

Route::apiResource('categorias-gastos', CategoriaGastoController::class)->middleware('auth:sanctum');
Route::apiResource('gastos', GastoController::class)->middleware('auth:sanctum');
Route::apiResource('clients', ClientController::class)->middleware('auth:sanctum');
Route::apiResource('fonte-pagadoras', FontePagadoraController::class)->middleware('auth:sanctum');
Route::apiResource('exemplos', ExemploController::class)->middleware('auth:sanctum');
Route::get('autocomplete/fontes-pagadoras', [AutocompleteController::class, 'fontesPagadoras'])->middleware('auth:sanctum');

Route::apiResource('obras', ObraController::class)->middleware('auth:sanctum');
Route::get('autocomplete/obras', [AutocompleteController::class, 'obras'])->middleware('auth:sanctum');
Route::get('autocomplete/clientes', [AutocompleteController::class, 'clientes'])->middleware('auth:sanctum');
Route::get('autocomplete/categorias-gastos', [AutocompleteController::class, 'categoriasGastos'])->middleware('auth:sanctum');
Route::get('autocomplete/gastos', [AutocompleteController::class, 'gastos'])->middleware('auth:sanctum');
Route::get('autocomplete/entradas-recursos', [AutocompleteController::class, 'entradasRecursos'])->middleware('auth:sanctum');
Route::get('autocomplete/exemplos', [AutocompleteController::class, 'exemplos'])->middleware('auth:sanctum');
Route::apiResource('entradas-recursos', EntradaRecursoController::class)->middleware('auth:sanctum');
Route::apiResource('users', UserController::class)->middleware('auth:sanctum');
Route::post('/login', [UserAuthController::class, 'login']);
Route::post('register',[UserAuthController::class,'register']);
Route::post('logout',[UserAuthController::class,'logout'])
  ->middleware('auth:sanctum');

// Rotas do Dashboard
Route::prefix('dashboard')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/resumo', [DashboardController::class, 'getResumo']);
    Route::get('/evolucao', [DashboardController::class, 'getEvolucao']);
    Route::get('/debug', [DashboardController::class, 'debug']); // Apenas para desenvolvimento
});

// Relatórios PDF
Route::get('relatorios/gastos', [DashboardController::class, 'relatorio'])->name('relatorio')->middleware('auth:sanctum');
Route::get('relatorios/status/{filename}', [DashboardController::class, 'verificarRelatorio'])->name('dashboard.verificar.relatorio')->middleware('auth:sanctum');
Route::get('relatorios/download/{filename}', [DashboardController::class, 'downloadRelatorio'])->name('dashboard.download.relatorio');
