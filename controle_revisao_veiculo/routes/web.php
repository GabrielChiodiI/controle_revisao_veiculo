<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController, ClienteController, RelatorioController,
    RevisaoController, VeiculoController
};
use App\Http\Controllers\Auth\LoginController;

Route::get('/login', fn () => inertia('Login'))->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    // SOMENTE UMA RAIZ
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // CLIENTES
    Route::get('/clientes',                 [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/{cliente}',       [ClienteController::class,'show']);
    Route::post('/clientes',                [ClienteController::class, 'store'])->name('clientes.store');
    Route::put('/clientes/{cliente}',       [ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}',    [ClienteController::class, 'destroy'])->name('clientes.destroy');

    // VEÍCULOS DO CLIENTE
    Route::prefix('clientes/{cliente}')->group(function () {
        Route::get('veiculos',                          [VeiculoController::class,'indexByCliente'])->name('clientes.veiculos.index');
        Route::post('veiculos',                         [VeiculoController::class,'storeForCliente'])->name('clientes.veiculos.store');
        Route::match(['put','patch'],'veiculos/{veiculo}', [VeiculoController::class,'update'])->name('clientes.veiculos.update');
        Route::delete('veiculos/{veiculo}',             [VeiculoController::class,'destroy'])->name('clientes.veiculos.destroy');
    });
    
    // REVISÕES / RELATÓRIOS (mantidos)
    Route::get('/revisoes/veiculos-do-cliente/{id}', [RevisaoController::class, 'veiculosDoCliente']);
    Route::get('/revisoes',                          [RevisaoController::class, 'index']);
    Route::get('/revisoes/lista',                    [RevisaoController::class,'lista']);
    Route::get('/revisoes/clientes',                 [RevisaoController::class,'clientes']);
    Route::get('/revisoes/todas-revisoes',           [RevisaoController::class, 'todasRevisoes']);
    Route::post('/revisoes',                         [RevisaoController::class, 'store']);
    Route::put('/revisoes/{revisao}/finalizar',      [RevisaoController::class, 'finalizar']);
    Route::get('/revisoes/servicos',                 [RevisaoController::class, 'searchServicos']);
    Route::get('/revisoes/pecas',                    [RevisaoController::class, 'searchPecas']);
    Route::get('/revisoes/servicos/{id}/pecas-sugeridas', [RevisaoController::class, 'pecasDoServico']);
    Route::get('/revisoes/{id}/detalhes',            [RevisaoController::class, 'detalhes']);

    Route::get('/relatorios',                        [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/todos-veiculos',         [RelatorioController::class, 'todosVeiculos']);
    Route::get('/relatorios/veiculos-por-pessoa',    [RelatorioController::class, 'todosVeiculosPorPessoa']);
    Route::get('/relatorios/mais-veiculos-por-sexo', [RelatorioController::class, 'maisVeiculosPorSexo']);
    Route::get('/relatorios/marcas-ordenadas',       [RelatorioController::class, 'marcasPorNumeroVeiculos']);
    Route::get('/relatorios/marcas-quantidade-por-sexo', [RelatorioController::class, 'marcasQuantidadePorSexo']);
    Route::get('/relatorios/todas-pessoas',          [RelatorioController::class, 'todasPessoas']);
    Route::get('/relatorios/pessoas-por-sexo',       [RelatorioController::class, 'pessoasPorSexoComMediaIdade']);
    Route::get('/relatorios/revisoes-por-periodo',   [RelatorioController::class, 'revisoesPorPeriodo']);
    Route::get('/relatorios/marcas-mais-revisoes',   [RelatorioController::class, 'marcasComMaisRevisoes']);
    Route::get('/relatorios/pessoas-mais-revisoes',  [RelatorioController::class, 'pessoasComMaisRevisoes']);
    Route::get('/relatorios/media-tempo-entre-revisoes', [RelatorioController::class, 'mediaTempoEntreRevisoesPorPessoa']);
    Route::get('/relatorios/proxima-revisao',        [RelatorioController::class, 'proximaRevisaoPorPessoa']);
});
