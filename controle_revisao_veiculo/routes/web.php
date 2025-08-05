<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\RevisaoController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/login', function () {
    return inertia('Login');
})->name('login')->middleware('guest');

Route::post('/login', [LoginController::class, 'authenticate'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return inertia('Home');
    });

    Route::get('/', [HomeController::class, 'index']);
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/revisoes', [RevisaoController::class, 'index'])->name('revisoes.index');
    Route::post('/revisoes', [RevisaoController::class, 'store']);
    Route::post('/revisoes/{id}/finalizar', [RevisaoController::class, 'finalizar']);
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/pessoas-por-sexo', [RelatorioController::class, 'pessoasPorSexoComMediaIdade']);
    Route::get('/relatorios/veiculos-por-pessoa', [RelatorioController::class, 'porPessoa']);
    Route::get('/relatorios/mais-veiculos-por-sexo', [RelatorioController::class, 'maisVeiculosPorSexo']);
    Route::get('/relatorios/marcas-ordenadas', [RelatorioController::class, 'marcasOrdenadas']);
    Route::get('/relatorios/marcas-por-sexo', [RelatorioController::class, 'marcasPorSexo']);
    Route::get('/relatorios/revisoes-por-periodo', [RelatorioController::class, 'revisoesPorPeriodo']);
    Route::get('/relatorios/marcas-mais-revisoes', [RelatorioController::class, 'marcasMaisRevisoes']);
    Route::get('/relatorios/pessoas-mais-revisoes', [RelatorioController::class, 'pessoasMaisRevisoes']);
    Route::get('/relatorios/media-tempo-entre-revisoes/{idCliente}', [RelatorioController::class, 'mediaTempoEntreRevisoesPorPessoa']);
    Route::get('/relatorios/proxima-revisao/{idCliente}', [RelatorioController::class, 'proximaRevisaoPorPessoa']);

});
