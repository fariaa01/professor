<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\ProfessorAluno;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlunoConnectController extends Controller
{
    public function show()
    {
        return view('aluno.connect');
    }

    public function connect(Request $request)
    {
        $request->validate([
            'professor_id' => 'required|integer|min:1',
        ]);

        $aluno = Auth::guard('aluno')->user();

        $professor = User::find($request->input('professor_id'));

        if (! $professor) {
            return back()->withErrors(['professor_id' => 'Professor não encontrado.'])->withInput();
        }

        // assign professor_id directly to aluno (single association)
        $aluno->professor_id = $professor->id;
        $aluno->save();

        return redirect()->route('aluno.dashboard')->with('status', 'Vínculo criado com sucesso.');
    }
}
