<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class Aluno extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'user_id',
        'nome',
        'email',
        'telefone',
        'endereco',
        'responsavel',
        'telefone_responsavel',
        'valor_aula',
        'data_inicio',
        'observacoes',
        'ativo',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'data_inicio' => 'date',
        'valor_aula' => 'decimal:2',
        'password' => 'hashed',
    ];

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'aluno_id' => $this->id,
            'professor_id' => $this->user_id,
            'email' => $this->email,
            'nome' => $this->nome,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aulas()
    {
        return $this->hasMany(Aula::class);
    }

    public function horarios()
    {
        return $this->hasMany(HorarioAula::class);
    }

    public function horariosAtivos()
    {
        return $this->hasMany(HorarioAula::class)->where('ativo', true);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'aluno_tag');
    }

    public function planos()
    {
        return $this->hasMany(Plano::class);
    }

    public function planoAtivo()
    {
        return $this->hasOne(Plano::class)->where('ativo', true)->latest();
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
