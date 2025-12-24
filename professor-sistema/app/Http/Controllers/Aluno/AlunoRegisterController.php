<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AlunoRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('aluno.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:alunos,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $aluno = Aluno::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('aluno_web')->login($aluno);

        return redirect()->route('aluno.dashboard');
    }
}
