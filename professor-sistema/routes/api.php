<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Aluno\AuthController;
use App\Http\Controllers\Aluno\DashboardController;
use App\Http\Controllers\Aluno\AulaController;
use App\Http\Controllers\Aluno\PagamentoController;
use App\Http\Controllers\Aluno\MensagemController as AlunoMensagemController;
use App\Http\Controllers\MensagemController;

/*
|--------------------------------------------------------------------------
| API Routes - Portal do Aluno
|--------------------------------------------------------------------------
*/

// Rotas públicas (sem autenticação)
Route::prefix('aluno')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Rotas protegidas (requer autenticação JWT)
Route::prefix('aluno')->middleware('auth:aluno')->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Aulas
    Route::get('/aulas', [AulaController::class, 'index']);
    Route::get('/aulas/{id}', [AulaController::class, 'show']);

    // Pagamentos
    Route::get('/pagamentos/plano', [PagamentoController::class, 'plano']);
    Route::get('/pagamentos/parcelas', [PagamentoController::class, 'parcelas']);
    Route::get('/pagamentos/resumo', [PagamentoController::class, 'resumo']);
    
    // Mensagens
    Route::get('/mensagens', [AlunoMensagemController::class, 'index']);
    Route::post('/mensagens', [AlunoMensagemController::class, 'store']);
    Route::patch('/mensagens/marcar-lidas', [AlunoMensagemController::class, 'markAsRead']);
    Route::get('/mensagens/nao-lidas', [AlunoMensagemController::class, 'unreadCount']);
});
