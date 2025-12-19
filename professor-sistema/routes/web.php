<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlunoController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\MensagemController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MeetingChatController;
use App\Http\Controllers\ConteudoController;
use App\Http\Controllers\AlunoConteudoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rotas de Alunos
    Route::resource('alunos', AlunoController::class);
    Route::patch('/alunos/{aluno}/toggle-status', [AlunoController::class, 'toggleStatus'])->name('alunos.toggle-status');
    
    // Rotas de Tags
    Route::resource('tags', TagController::class)->except(['show']);
    
    // Rotas de Aulas
    Route::get('/aulas', [AulaController::class, 'index'])->name('aulas.index');
    Route::get('/aulas/{aula}', [AulaController::class, 'show'])->name('aulas.show');
    Route::get('/aulas/{aula}/edit', [AulaController::class, 'edit'])->name('aulas.edit');
    Route::put('/aulas/{aula}', [AulaController::class, 'update'])->name('aulas.update');
    Route::patch('/aulas/{aula}/status', [AulaController::class, 'updateStatus'])->name('aulas.updateStatus');
    Route::patch('/aulas/{aula}/reschedule', [AulaController::class, 'reschedule'])->name('aulas.reschedule');
    
    // Rotas de Relatórios
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    
    // Rotas de Calendário
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    Route::post('/calendario/reuniao', [CalendarioController::class, 'storeReuniao'])->name('calendario.storeReuniao');
    Route::patch('/reunioes/{reuniao}/status', [CalendarioController::class, 'updateReuniaoStatus'])->name('reunioes.updateStatus');
    Route::patch('/reunioes/{reuniao}/reschedule', [CalendarioController::class, 'rescheduleReuniao'])->name('reunioes.reschedule');
    Route::delete('/reunioes/{reuniao}', [CalendarioController::class, 'destroyReuniao'])->name('reunioes.destroy');
    
    // Rotas de Mensagens
    Route::get('/mensagens/{aluno}', function ($alunoId) {
        return view('mensagens.chat', ['alunoId' => $alunoId]);
    })->name('mensagens.chat');
    
    // API de Mensagens (Professor)
    Route::get('/api/mensagens/{aluno}', [MensagemController::class, 'index']);
    Route::post('/api/mensagens', [MensagemController::class, 'store']);
    Route::patch('/api/mensagens/{aluno}/marcar-lidas', [MensagemController::class, 'markAsRead']);
    Route::get('/api/mensagens/nao-lidas', [MensagemController::class, 'unreadCount']);
    
    // Rotas de Reuniões Online (Meetings)
    Route::resource('meetings', MeetingController::class);
    Route::get('/meetings/room/{roomId}', [MeetingController::class, 'room'])->name('meetings.room');
    Route::post('/meetings/{roomId}/join', [MeetingController::class, 'join'])->name('meetings.join');
    Route::post('/meetings/{roomId}/leave', [MeetingController::class, 'leave'])->name('meetings.leave');
    Route::post('/meetings/{roomId}/end', [MeetingController::class, 'end'])->name('meetings.end');
    Route::post('/meetings/{meeting}/cancel', [MeetingController::class, 'cancel'])->name('meetings.cancel');
    
    // WebRTC Signaling
    Route::post('/meetings/{roomId}/signal/offer', [MeetingController::class, 'sendOffer']);
    Route::post('/meetings/{roomId}/signal/answer', [MeetingController::class, 'sendAnswer']);
    Route::post('/meetings/{roomId}/signal/ice-candidate', [MeetingController::class, 'sendIceCandidate']);
    
    // Chat da Reunião
    Route::get('/meetings/{roomId}/chat', [MeetingChatController::class, 'index']);
    Route::post('/meetings/{roomId}/chat', [MeetingChatController::class, 'store']);
    
    // Conteúdos Gravados (Professor)
    Route::resource('conteudos', ConteudoController::class);
});

// Rotas do Portal do Aluno
Route::prefix('aluno')->name('aluno.')->middleware(['auth'])->group(function () {
    Route::get('/conteudos', [AlunoConteudoController::class, 'index'])->name('conteudos.index');
    Route::get('/conteudos/{conteudo}', [AlunoConteudoController::class, 'show'])->name('conteudos.show');
    Route::post('/conteudos/{conteudo}/progresso', [AlunoConteudoController::class, 'atualizarProgresso'])->name('conteudos.progresso');
});

// Rotas do Portal do Aluno
Route::prefix('aluno')->name('aluno.')->group(function () {
    Route::get('/login', function () {
        return view('aluno.login');
    })->name('login');
    
    Route::get('/dashboard', function () {
        return view('aluno.dashboard');
    })->name('dashboard');
    
    Route::get('/aulas', function () {
        return view('aluno.aulas');
    })->name('aulas');
    
    Route::get('/aulas/{id}', function ($id) {
        return view('aluno.aula-detalhes');
    })->name('aulas.detalhes');
    
    Route::get('/pagamentos', function () {
        return view('aluno.pagamentos');
    })->name('pagamentos');
    
    Route::get('/mensagens', function () {
        return view('aluno.mensagens');
    })->name('mensagens');
});

require __DIR__.'/auth.php';
