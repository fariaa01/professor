<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlunoAuthController extends Controller
{
    public function showLogin()
    {
        return view('aluno.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('aluno_web')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('aluno.dashboard'));
        }

        return back()->withErrors(['email' => 'Credenciais inválidas'])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('aluno_web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('aluno.login');
    }
}
