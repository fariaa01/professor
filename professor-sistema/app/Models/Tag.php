<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
        'cor',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alunos()
    {
        return $this->belongsToMany(Aluno::class, 'aluno_tag');
    }
}
