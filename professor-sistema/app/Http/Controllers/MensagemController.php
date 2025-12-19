<?php

namespace App\Http\Controllers;

use App\Models\Mensagem;
use App\Models\Aluno;
use Illuminate\Http\Request;

class MensagemController extends Controller
{
    /**
     * Lista todas as mensagens entre o professor e um aluno específico
     */
    public function index($alunoId)
    {
        $professor = auth()->user();

        // Verificar se o aluno pertence ao professor
        $aluno = Aluno::where('id', $alunoId)
            ->where('user_id', $professor->id)
            ->firstOrFail();

        // Buscar mensagens
        $mensagens = Mensagem::where('professor_id', $professor->id)
            ->where('aluno_id', $alunoId)
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
                    'data_formatada' => $msg->created_at->format('d/m/Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'aluno' => [
                    'id' => $aluno->id,
                    'nome' => $aluno->nome,
                    'email' => $aluno->email,
                ],
                'mensagens' => $mensagens,
            ],
        ]);
    }

    /**
     * Envia uma nova mensagem para o aluno
     */
    public function store(Request $request)
    {
        $request->validate([
            'aluno_id' => 'required|exists:alunos,id',
            'mensagem' => 'required|string|max:5000',
        ]);

        $professor = auth()->user();

        // Verificar se o aluno pertence ao professor
        $aluno = Aluno::where('id', $request->aluno_id)
            ->where('user_id', $professor->id)
            ->firstOrFail();

        $mensagem = Mensagem::create([
            'professor_id' => $professor->id,
            'aluno_id' => $aluno->id,
            'remetente' => 'professor',
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
    public function markAsRead($alunoId)
    {
        $professor = auth()->user();

        // Marcar como lidas todas as mensagens do aluno que o professor ainda não leu
        Mensagem::where('professor_id', $professor->id)
            ->where('aluno_id', $alunoId)
            ->where('remetente', 'aluno')
            ->where('lida', false)
            ->update(['lida' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Mensagens marcadas como lidas',
        ]);
    }

    /**
     * Retorna contagem de mensagens não lidas por aluno
     */
    public function unreadCount()
    {
        $professor = auth()->user();

        $naoLidas = Mensagem::where('professor_id', $professor->id)
            ->where('remetente', 'aluno')
            ->where('lida', false)
            ->selectRaw('aluno_id, COUNT(*) as total')
            ->groupBy('aluno_id')
            ->get()
            ->keyBy('aluno_id');

        return response()->json([
            'success' => true,
            'data' => $naoLidas,
        ]);
    }
}
