<?php

namespace App\Http\Controllers;

use App\Events\MeetingJoined;
use App\Events\MeetingLeft;
use App\Events\MeetingEnded;
use App\Events\WebRTCOffer;
use App\Events\WebRTCAnswer;
use App\Events\WebRTCIceCandidate;
use App\Models\Meeting;
use App\Models\Aluno;
use App\Models\MeetingMessage;
use App\Models\Mensagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    /**
     * Lista todas as reuniões do professor
     */
    public function index()
    {
        $meetings = Meeting::forProfessor(Auth::id())
            ->with(['aluno', 'aula'])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);

        return view('meetings.index', compact('meetings'));
    }

    /**
     * Exibe formulário para criar nova reunião
     */
    public function create()
    {
        $alunos = Aluno::where('user_id', Auth::id())
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        return view('meetings.create', compact('alunos'));
    }

    /**
     * Salva nova reunião
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'aluno_id' => 'nullable|exists:alunos,id',
            'aula_id' => 'nullable|exists:aulas,id',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $meeting = Meeting::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'aluno_id' => $validated['aluno_id'] ?? null,
            'aula_id' => $validated['aula_id'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'status' => 'agendada',
        ]);

        // Envia mensagem automática para o aluno com o link da reunião
        if ($meeting->aluno_id) {
            $linkReuniao = route('meetings.room', $meeting->room_id);
            
            Mensagem::create([
                'user_id' => Auth::id(),
                'aluno_id' => $meeting->aluno_id,
                'mensagem' => "📹 **Reunião Online Iniciada!**\n\n" .
                             "Título: {$meeting->title}\n" .
                             "Professor: " . Auth::user()->name . "\n\n" .
                             "Clique no link abaixo para entrar na sala de reunião:\n" .
                             $linkReuniao . "\n\n" .
                             "Aguardo você na reunião! 😊",
                'tipo' => 'aviso',
                'remetente' => 'professor',
            ]);
        }

        // Redireciona direto para a sala de reunião
        return redirect()
            ->route('meetings.room', $meeting->room_id)
            ->with('success', 'Reunião iniciada! Link enviado para o aluno.');
    }

    /**
     * Exibe detalhes da reunião
     */
    public function show(Meeting $meeting)
    {
        $meeting->load(['professor', 'aluno', 'aula', 'messages']);

        return view('meetings.show', compact('meeting'));
    }

    /**
     * Página da sala de reunião (WebRTC)
     */
    public function room($roomId)
    {
        $meeting = Meeting::where('room_id', $roomId)->firstOrFail();
        
        // Verifica permissão de acesso
        $userType = 'professor';
        $userId = Auth::id();
        
        if (!$meeting->isParticipant($userId, 'professor')) {
            // Verifica se é aluno
            $aluno = Aluno::where('email', Auth::user()->email)->first();
            if (!$aluno || !$meeting->isParticipant($aluno->id, 'aluno')) {
                abort(403, 'Você não tem permissão para acessar esta reunião.');
            }
            $userType = 'aluno';
            $userId = $aluno->id;
        }

        // Inicia reunião se ainda não iniciada
        if ($meeting->status === 'agendada') {
            $meeting->start();
        }

        return view('meetings.room', compact('meeting', 'userType', 'userId'));
    }

    /**
     * Entra na sala (broadcast)
     */
    public function join(Request $request, $roomId)
    {
        $meeting = Meeting::where('room_id', $roomId)->firstOrFail();
        
        $userType = $request->input('user_type', 'professor');
        $userId = $request->input('user_id');
        $userName = $request->input('user_name');

        // Adiciona participante
        $meeting->addParticipant($userId, $userName, $userType);

        // Broadcast evento
        broadcast(new MeetingJoined($meeting->id, $userId, $userName, $userType))->toOthers();

        // Cria mensagem do sistema
        MeetingMessage::create([
            'meeting_id' => $meeting->id,
            'sender_type' => $userType,
            'sender_id' => $userId,
            'message' => "$userName entrou na reunião",
            'is_system_message' => true,
        ]);

        return response()->json([
            'success' => true,
            'meeting' => $meeting,
        ]);
    }

    /**
     * Sai da sala (broadcast)
     */
    public function leave(Request $request, $roomId)
    {
        $meeting = Meeting::where('room_id', $roomId)->firstOrFail();
        
        $userType = $request->input('user_type');
        $userId = $request->input('user_id');
        $userName = $request->input('user_name');

        // Broadcast evento
        broadcast(new MeetingLeft($meeting->id, $userId, $userType))->toOthers();

        // Cria mensagem do sistema
        MeetingMessage::create([
            'meeting_id' => $meeting->id,
            'sender_type' => $userType,
            'sender_id' => $userId,
            'message' => "$userName saiu da reunião",
            'is_system_message' => true,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Encerra reunião
     */
    public function end($roomId)
    {
        $meeting = Meeting::where('room_id', $roomId)->firstOrFail();

        $meeting->end();

        // Broadcast evento
        broadcast(new MeetingEnded($meeting->id, Auth::user()->name));

        return response()->json([
            'success' => true,
            'redirect_url' => route('meetings.show', $meeting),
        ]);
    }

    /**
     * WebRTC Signaling - Offer
     */
    public function sendOffer(Request $request, $roomId)
    {
        $meeting = Meeting::where('room_id', $roomId)->firstOrFail();
        
        $offer = $request->input('offer');
        $from = $request->input('from');

        broadcast(new WebRTCOffer($meeting->id, $offer, $from))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * WebRTC Signaling - Answer
     */
    public function sendAnswer(Request $request, $roomId)
    {
        $meeting = Meeting::where('room_id', $roomId)->firstOrFail();
        
        $answer = $request->input('answer');
        $from = $request->input('from');

        broadcast(new WebRTCAnswer($meeting->id, $answer, $from))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * WebRTC Signaling - ICE Candidate
     */
    public function sendIceCandidate(Request $request, $roomId)
    {
        $meeting = Meeting::where('room_id', $roomId)->firstOrFail();
        
        $candidate = $request->input('candidate');
        $from = $request->input('from');

        broadcast(new WebRTCIceCandidate($meeting->id, $candidate, $from))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Cancela reunião
     */
    public function cancel(Meeting $meeting)
    {
        $meeting->cancel();

        return redirect()
            ->route('meetings.index')
            ->with('success', 'Reunião cancelada com sucesso!');
    }

    /**
     * Deleta reunião
     */
    public function destroy(Meeting $meeting)
    {
        $meeting->delete();

        return redirect()
            ->route('meetings.index')
            ->with('success', 'Reunião excluída com sucesso!');
    }
}
