<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Plano extends Model
{
    protected $table = 'planos';

    protected $fillable = [
        'aluno_id',
        'tipo_plano',
        'valor_aula',
        'valor_total',
        'quantidade_aulas',
        'data_inicio',
        'data_fim',
        'ativo',
        'observacoes',
    ];

    protected $casts = [
        'valor_aula' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'quantidade_aulas' => 'integer',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ativo' => 'boolean',
    ];

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }

    public function parcelas()
    {
        return $this->hasMany(Parcela::class);
    }

    public function parcelasPendentes()
    {
        return $this->hasMany(Parcela::class)
            ->whereIn('status_pagamento', ['pendente', 'atrasado'])
            ->orderBy('data_vencimento', 'asc');
    }

    public function proximasParcelas($limite = 5)
    {
        return $this->hasMany(Parcela::class)
            ->whereIn('status_pagamento', ['pendente', 'atrasado'])
            ->where('data_vencimento', '>=', Carbon::now())
            ->orderBy('data_vencimento', 'asc')
            ->limit($limite);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function getTipoPlanoNomeAttribute()
    {
        $tipos = [
            'por_aula' => 'Pagamento por Aula',
            'pacote' => 'Pacote de Aulas',
            'mensalidade' => 'Mensalidade',
        ];

        return $tipos[$this->tipo_plano] ?? $this->tipo_plano;
    }
}
