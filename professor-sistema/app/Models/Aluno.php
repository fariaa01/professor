<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Aluno extends Authenticatable
{
    use Notifiable;

    protected $table = 'alunos';

    protected $fillable = [
        'name',
        'email',
        'password',
        'professor_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function professors()
    {
        return $this->belongsToMany(User::class, 'professor_aluno', 'aluno_id', 'professor_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }
}
