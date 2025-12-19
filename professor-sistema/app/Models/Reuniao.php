<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reuniao extends Model
{
    protected $fillable = [
        'user_id',
        'aluno_id',
        'data_hora',
        'duracao_minutos',
        'titulo',
        'descricao',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function scopeAgendadas($query)
    {
        return $query->where('status', 'agendada');
    }

    public function scopeRealizadas($query)
    {
        return $query->where('status', 'realizada');
    }
}
