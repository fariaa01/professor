<?php

use App\Models\Meeting;
use App\Models\Aluno;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aqui você pode registrar todos os canais de broadcasting de eventos
| para sua aplicação. O callback fornecido receberá o usuário autenticado
| e quaisquer outros parâmetros relevantes.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privado para cada reunião
Broadcast::channel('meeting.{meetingId}', function ($user, $meetingId) {
    $meeting = Meeting::find($meetingId);
    
    if (!$meeting) {
        return false;
    }
    
    // Verifica se é o professor da reunião
    if ($meeting->user_id == $user->id) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'type' => 'professor',
        ];
    }
    
    // Verifica se é o aluno da reunião
    $aluno = Aluno::where('email', $user->email)->first();
    
    if ($aluno && $meeting->aluno_id == $aluno->id) {
        return [
            'id' => $aluno->id,
            'name' => $aluno->nome,
            'type' => 'aluno',
        ];
    }
    
    return false;
});
