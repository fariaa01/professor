<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'aluno_id',
        'aula_id',
        'title',
        'description',
        'scheduled_at',
        'started_at',
        'ended_at',
        'status',
        'duration_minutes',
        'is_active',
        'participants',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_active' => 'boolean',
        'participants' => 'array',
        'duration_minutes' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($meeting) {
            if (!$meeting->room_id) {
                $meeting->room_id = Str::uuid()->toString();
            }
        });
    }

    // Relationships
    public function professor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class, 'aluno_id');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'aula_id');
    }

    public function messages()
    {
        return $this->hasMany(MeetingMessage::class)->orderBy('created_at', 'asc');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'agendada');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'em_andamento');
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'encerrada');
    }

    public function scopeForProfessor($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAluno($query, $alunoId)
    {
        return $query->where('aluno_id', $alunoId);
    }

    // Helper methods
    public function start()
    {
        $this->update([
            'status' => 'em_andamento',
            'started_at' => now(),
        ]);
    }

    public function end()
    {
        $duration = null;
        if ($this->started_at) {
            $duration = now()->diffInMinutes($this->started_at);
        }

        $this->update([
            'status' => 'encerrada',
            'ended_at' => now(),
            'duration_minutes' => $duration,
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelada',
            'is_active' => false,
        ]);
    }

    public function isParticipant($userId, $type = 'professor')
    {
        if ($type === 'professor') {
            return $this->user_id == $userId;
        }
        return $this->aluno_id == $userId;
    }

    public function addParticipant($userId, $name, $type)
    {
        $participants = $this->participants ?? [];
        $participants[] = [
            'id' => $userId,
            'name' => $name,
            'type' => $type,
            'joined_at' => now()->toISOString(),
        ];
        $this->update(['participants' => $participants]);
    }

    public function getDurationAttribute()
    {
        if ($this->duration_minutes) {
            return $this->duration_minutes;
        }
        
        if ($this->started_at && $this->ended_at) {
            return $this->ended_at->diffInMinutes($this->started_at);
        }
        
        if ($this->started_at && $this->status === 'em_andamento') {
            return now()->diffInMinutes($this->started_at);
        }
        
        return 0;
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'agendada' => 'Agendada',
            'em_andamento' => 'Em Andamento',
            'encerrada' => 'Encerrada',
            'cancelada' => 'Cancelada',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'agendada' => 'blue',
            'em_andamento' => 'green',
            'encerrada' => 'gray',
            'cancelada' => 'red',
            default => 'gray',
        };
    }
}
