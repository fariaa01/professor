<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'sender_type',
        'sender_id',
        'message',
        'is_system_message',
    ];

    protected $casts = [
        'is_system_message' => 'boolean',
    ];

    // Relationships
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function sender()
    {
        if ($this->sender_type === 'professor') {
            return $this->belongsTo(User::class, 'sender_id');
        }
        return $this->belongsTo(Aluno::class, 'sender_id');
    }

    // Accessors
    public function getSenderNameAttribute()
    {
        if ($this->is_system_message) {
            return 'Sistema';
        }
        
        if ($this->sender_type === 'professor') {
            $user = User::find($this->sender_id);
            return $user ? $user->name : 'Professor';
        }
        
        $aluno = Aluno::find($this->sender_id);
        return $aluno ? $aluno->nome : 'Aluno';
    }

    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    // Scopes
    public function scopeForMeeting($query, $meetingId)
    {
        return $query->where('meeting_id', $meetingId);
    }

    public function scopeUserMessages($query)
    {
        return $query->where('is_system_message', false);
    }

    public function scopeSystemMessages($query)
    {
        return $query->where('is_system_message', true);
    }
}
