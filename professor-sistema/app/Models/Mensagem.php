<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    use HasFactory;

    protected $table = 'mensagens';

    protected $fillable = [
        'professor_id',
        'aluno_id',
        'remetente',
        'mensagem',
        'lida',
    ];

    protected $casts = [
        'lida' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Professor que enviou/recebeu a mensagem
     */
    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    /**
     * Aluno que enviou/recebeu a mensagem
     */
    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    /**
     * Verifica se a mensagem foi enviada pelo professor
     */
    public function isProfessor(): bool
    {
        return $this->remetente === 'professor';
    }

    /**
     * Verifica se a mensagem foi enviada pelo aluno
     */
    public function isAluno(): bool
    {
        return $this->remetente === 'aluno';
    }
}
