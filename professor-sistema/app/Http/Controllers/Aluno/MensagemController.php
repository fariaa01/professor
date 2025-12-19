<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Models\Mensagem;
use Illuminate\Http\Request;

class MensagemController extends Controller
{
    /**
     * Lista todas as mensagens entre o aluno e seu professor
     */
    public function index()
    {
        $aluno = auth('aluno')->user();

        // Buscar mensagens
        $mensagens = Mensagem::where('aluno_id', $aluno->id)
            ->where('professor_id', $aluno->user_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'remetente' => $msg->remetente,
                    'mensagem' => $msg->mensagem,
                    'lida' => $msg->lida,
                    'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                    'horario' => $msg->created_at->format('H:i'),
                    'data_formatada' => $msg->created_at->translatedFormat('d/m/Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'professor' => [
                    'id' => $aluno->user_id,
                    'nome' => $aluno->professor->name ?? 'Professor',
                ],
                'mensagens' => $mensagens,
            ],
        ]);
    }

    /**
     * Envia uma nova mensagem para o professor
     */
    public function store(Request $request)
    {
        $request->validate([
            'mensagem' => 'required|string|max:5000',
        ]);

        $aluno = auth('aluno')->user();

        $mensagem = Mensagem::create([
            'professor_id' => $aluno->user_id,
            'aluno_id' => $aluno->id,
            'remetente' => 'aluno',
            'mensagem' => $request->mensagem,
            'lida' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $mensagem->id,
                'remetente' => $mensagem->remetente,
                'mensagem' => $mensagem->mensagem,
                'lida' => $mensagem->lida,
                'created_at' => $mensagem->created_at->format('Y-m-d H:i:s'),
                'horario' => $mensagem->created_at->format('H:i'),
                'data_formatada' => $mensagem->created_at->translatedFormat('d/m/Y'),
            ],
        ], 201);
    }

    /**
     * Marca mensagens como lidas
     */
    public function markAsRead()
    {
        $aluno = auth('aluno')->user();

        // Marcar como lidas todas as mensagens do professor que o aluno ainda não leu
        Mensagem::where('aluno_id', $aluno->id)
            ->where('professor_id', $aluno->user_id)
            ->where('remetente', 'professor')
            ->where('lida', false)
            ->update(['lida' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Mensagens marcadas como lidas',
        ]);
    }

    /**
     * Retorna contagem de mensagens não lidas
     */
    public function unreadCount()
    {
        $aluno = auth('aluno')->user();

        $total = Mensagem::where('aluno_id', $aluno->id)
            ->where('professor_id', $aluno->user_id)
            ->where('remetente', 'professor')
            ->where('lida', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => ['total' => $total],
        ]);
    }
}
