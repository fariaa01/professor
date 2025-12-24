<?php

namespace App\Http\Controllers\Aluno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfessorAluno;

class AlunoDashboardController extends Controller
{
    public function index()
    {
        $aluno = Auth::guard('aluno')->user();

        // check if aluno has a professor_id assigned
        if (! $aluno->professor_id) {
            return view('aluno.dashboard', ['connected' => false]);
        }

        // optionally load professor data
        $professor = $aluno->professor;

        return view('aluno.dashboard', ['connected' => true, 'professor' => $professor]);
    }
}
