<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConteudoProgresso extends Model
{
    protected $table = 'conteudo_progresso';

    protected $fillable = [
        'conteudo_id',
        'aluno_id',
        'iniciado_em',
        'concluido_em',
        'progresso_segundos',
        'completo',
        'visualizacoes',
    ];

    protected $casts = [
        'iniciado_em' => 'datetime',
        'concluido_em' => 'datetime',
        'completo' => 'boolean',
        'progresso_segundos' => 'integer',
        'visualizacoes' => 'integer',
    ];

    /**
     * Conteúdo relacionado
     */
    public function conteudo(): BelongsTo
    {
        return $this->belongsTo(Conteudo::class);
    }

    /**
     * Aluno que está progredindo
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(Aluno::class);
    }

    /**
     * Calcula percentual de progresso
     */
    public function getPercentualAttribute(): int
    {
        if (!$this->conteudo || !$this->conteudo->duracao_segundos) {
            return 0;
        }
        
        $percentual = ($this->progresso_segundos / $this->conteudo->duracao_segundos) * 100;
        return min(100, max(0, (int) $percentual));
    }

    /**
     * Atualiza o progresso do aluno
     */
    public function atualizarProgresso(int $segundos): void
    {
        $this->progresso_segundos = $segundos;
        
        if (!$this->iniciado_em) {
            $this->iniciado_em = now();
        }
        
        // Marca como completo se assistiu 90% ou mais
        if ($this->percentual >= 90 && !$this->completo) {
            $this->completo = true;
            $this->concluido_em = now();
        }
        
        $this->save();
    }

    /**
     * Incrementa visualizações
     */
    public function registrarVisualizacao(): void
    {
        $this->increment('visualizacoes');
        
        if (!$this->iniciado_em) {
            $this->iniciado_em = now();
            $this->save();
        }
    }
}
