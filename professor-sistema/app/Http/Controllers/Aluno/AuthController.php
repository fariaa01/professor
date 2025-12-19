<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Login do aluno
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Buscar aluno pelo email
        $aluno = Aluno::where('email', $credentials['email'])->first();

        if (!$aluno || !Hash::check($credentials['password'], $aluno->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas. Verifique seu email e senha.',
            ], 401);
        }

        if (!$aluno->ativo) {
            return response()->json([
                'success' => false,
                'message' => 'Sua conta está inativa. Entre em contato com seu professor.',
            ], 403);
        }

        // Gerar token JWT
        $token = JWTAuth::fromUser($aluno);

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso!',
            'data' => [
                'aluno' => [
                    'id' => $aluno->id,
                    'nome' => $aluno->nome,
                    'email' => $aluno->email,
                    'telefone' => $aluno->telefone,
                    'professor_id' => $aluno->user_id,
                ],
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('aluno')->factory()->getTTL() * 60, // em segundos
            ],
        ]);
    }

    /**
     * Retorna dados do aluno autenticado
     */
    public function me()
    {
        $aluno = auth('aluno')->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $aluno->id,
                'nome' => $aluno->nome,
                'email' => $aluno->email,
                'telefone' => $aluno->telefone,
                'endereco' => $aluno->endereco,
                'responsavel' => $aluno->responsavel,
                'telefone_responsavel' => $aluno->telefone_responsavel,
                'data_inicio' => $aluno->data_inicio?->format('d/m/Y'),
                'ativo' => $aluno->ativo,
            ],
        ]);
    }

    /**
     * Logout do aluno
     */
    public function logout()
    {
        auth('aluno')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso!',
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        $token = auth('aluno')->refresh();

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('aluno')->factory()->getTTL() * 60,
            ],
        ]);
    }

    /**
     * Alterar senha do aluno
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $aluno = auth('aluno')->user();

        if (!Hash::check($validated['current_password'], $aluno->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha atual incorreta.',
            ], 400);
        }

        $aluno->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso!',
        ]);
    }
}
