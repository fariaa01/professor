<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    /**
     * Exibir formulário de login
     */
    public function showLogin()
    {
        return view('aluno.login');
    }

    /**
     * Processar login (apenas retorna view para uso com API via JavaScript)
     */
    public function login(Request $request)
    {
        // Esta rota é usada apenas via JavaScript na página de login
        // O login real acontece via API JWT no frontend
        return redirect()->route('aluno.login');
    }

    /**
     * Exibir dashboard do aluno
     */
    public function dashboard()
    {
        return view('aluno.dashboard');
    }

    /**
     * Logout
     */
    public function logout()
    {
        return redirect()->route('aluno.login');
    }
}
