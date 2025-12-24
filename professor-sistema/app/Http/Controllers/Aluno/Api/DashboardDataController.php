<?php

namespace App\Http\Controllers\Aluno\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardDataController extends Controller
{
    public function dados(Request $request): JsonResponse
    {
        $aluno = auth('aluno_web')->user();

        if (! $aluno) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $payload = [
            'professor_id' => $aluno->professor_id,
            'aluno' => [
                'id' => $aluno->id,
                'name' => $aluno->name ?? $aluno->nome ?? null,
                'email' => $aluno->email,
            ],
            // Estatísticas padrão para evitar 'undefined' no frontend
            'estatisticas' => [
                'total_aulas' => 0,
                'aulas_realizadas' => 0,
                'faltas' => 0,
                'carga_horaria_horas' => 0,
            ],
            'proximas_aulas' => [],
            'aulas_recentes' => [],
            'plano' => null,
        ];

        return response()->json(['success' => true, 'data' => $payload], 200);
    }
}
