<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conteudo extends Model
{
    protected $fillable = [
        'user_id',
        'titulo',
        'descricao',
        'tipo',
        'url',
        'duracao_segundos',
        'materiais',
        'observacoes',
        'status',
        'alunos_ids',
    ];

    protected $casts = [
        'materiais' => 'array',
        'alunos_ids' => 'array',
        'duracao_segundos' => 'integer',
    ];

    /**
     * Professor que criou o conteúdo
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Progresso dos alunos neste conteúdo
     */
    public function progressos(): HasMany
    {
        return $this->hasMany(ConteudoProgresso::class);
    }

    /**
     * Verifica se o aluno tem acesso a este conteúdo
     */
    public function alunoTemAcesso(int $alunoId): bool
    {
        if (!$this->alunos_ids) {
            return false;
        }
        return in_array($alunoId, $this->alunos_ids);
    }

    /**
     * Retorna o progresso do aluno neste conteúdo
     */
    public function progressoDoAluno(int $alunoId)
    {
        return $this->progressos()->where('aluno_id', $alunoId)->first();
    }

    /**
     * Formata a duração em minutos
     */
    public function getDuracaoFormatadaAttribute(): string
    {
        if (!$this->duracao_segundos) {
            return '--';
        }
        
        $minutos = floor($this->duracao_segundos / 60);
        $segundos = $this->duracao_segundos % 60;
        return sprintf('%d:%02d', $minutos, $segundos);
    }

    /**
     * Scopes
     */
    public function scopePublicados($query)
    {
        return $query->where('status', 'publicado');
    }

    public function scopeDoProfessor($query, int $professorId)
    {
        return $query->where('user_id', $professorId);
    }

    public function scopeDisponivelParaAluno($query, int $alunoId)
    {
        return $query->where('status', 'publicado')
            ->whereJsonContains('alunos_ids', $alunoId);
    }
}
