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
    Route::get('/revisoes/todas-revisoes', [RevisaoController::class, 'todasRevisoes']);
    Route::post('/revisoes', [RevisaoController::class, 'store']);
    Route::post('/revisoes/{id}/finalizar', [RevisaoController::class, 'finalizar']);
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/todos-veiculos', [RelatorioController::class, 'todosVeiculos']);
    Route::get('/relatorios/veiculos-por-pessoa', [RelatorioController::class, 'todosVeiculosPorPessoa']);
    Route::get('/relatorios/mais-veiculos-por-sexo', [RelatorioController::class, 'maisVeiculosPorSexo']);
    Route::get('/relatorios/marcas-ordenadas', [RelatorioController::class, 'marcasPorNumeroVeiculos']);
    Route::get('/relatorios/marcas-quantidade-por-sexo', [RelatorioController::class, 'marcasQuantidadePorSexo']);
    Route::get('/relatorios/todas-pessoas', [RelatorioController::class, 'todasPessoas']);
    Route::get('/relatorios/pessoas-por-sexo', [RelatorioController::class, 'pessoasPorSexoComMediaIdade']);
    Route::get('/relatorios/revisoes-por-periodo', [RelatorioController::class, 'revisoesPorPeriodo']);
    Route::get('/relatorios/marcas-mais-revisoes', [RelatorioController::class, 'marcasComMaisRevisoes']);
    Route::get('/relatorios/pessoas-mais-revisoes', [RelatorioController::class, 'pessoasComMaisRevisoes']);
    Route::get('/relatorios/media-tempo-entre-revisoes', [RelatorioController::class, 'mediaTempoEntreRevisoesPorPessoa']);
    Route::get('/relatorios/proxima-revisao', [RelatorioController::class, 'proximaRevisaoPorPessoa']);

});
