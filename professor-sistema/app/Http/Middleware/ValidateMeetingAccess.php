<?php

namespace App\Http\Middleware;

use App\Models\Meeting;
use App\Models\Aluno;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateMeetingAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roomId = $request->route('roomId');
        
        if (!$roomId) {
            abort(404, 'Reunião não encontrada.');
        }

        $meeting = Meeting::where('room_id', $roomId)->first();
        
        if (!$meeting) {
            abort(404, 'Reunião não encontrada.');
        }

        // Verifica se é o professor da reunião
        if ($meeting->user_id == Auth::id()) {
            return $next($request);
        }

        // Verifica se é o aluno da reunião
        $aluno = Aluno::where('email', Auth::user()->email)->first();
        
        if ($aluno && $meeting->aluno_id == $aluno->id) {
            return $next($request);
        }

        abort(403, 'Você não tem permissão para acessar esta reunião.');
    }
}
