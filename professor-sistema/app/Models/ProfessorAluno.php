<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessorAluno extends Model
{
    protected $table = 'professor_aluno';
    protected $fillable = ['professor_id','aluno_id','status'];
}
