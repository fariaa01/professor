<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $fillable = [
        'user_id',
        'aluno_id',
        'data_hora',
        'duracao_minutos',
        'valor',
        'forma_pagamento',
        'status_pagamento',
        'status',
        'conteudo',
        'materiais',
        'exercicios',
        'dificuldades',
        'pontos_atencao',
        'observacoes',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
        'valor' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'aula_tag');
    }

    public function scopeAgendadas($query)
    {
        return $query->where('status', 'agendada');
    }

    public function scopeRealizadas($query)
    {
        return $query->where('status', 'realizada');
    }

    public function scopeCanceladas($query)
    {
        return $query->whereIn('status', ['cancelada_aluno', 'cancelada_professor']);
    }
}
