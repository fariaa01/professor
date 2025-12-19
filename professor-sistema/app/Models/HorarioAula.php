<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioAula extends Model
{
    protected $table = 'horarios_aulas';

    protected $fillable = [
        'aluno_id',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
        'duracao_minutos',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'dia_semana' => 'integer',
    ];

    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDiaSemana($query, int $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    public static function diasSemana(): array
    {
        return [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
        ];
    }

    public function getDiaSemanaTextoAttribute(): string
    {
        return self::diasSemana()[$this->dia_semana] ?? '';
    }
}
