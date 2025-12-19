<?php

namespace App\Http\Controllers;

use App\Events\MeetingMessageSent;
use App\Models\Meeting;
use App\Models\MeetingMessage;
use Illuminate\Http\Request;

class MeetingChatController extends Controller
{
    /**
     * Retorna mensagens de uma reunião
     */
    public function index($roomId)
    {
        $meeting = Meeting::where('room_id', $roomId)->firstOrFail();
        
        $messages = MeetingMessage::forMeeting($meeting->id)
            ->with('meeting')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender_name,
                    'is_system_message' => $message->is_system_message,
                    'created_at' => $message->created_at->toISOString(),
                    'formatted_time' => $message->formatted_time,
                ];
            });

        return response()->json($messages);
    }

    /**
     * Envia nova mensagem no chat da reunião
     */
    public function store(Request $request, $roomId)
    {
        $meeting = Meeting::where('room_id', $roomId)->firstOrFail();
        
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'sender_type' => 'required|in:professor,aluno',
            'sender_id' => 'required|integer',
        ]);

        $meetingMessage = MeetingMessage::create([
            'meeting_id' => $meeting->id,
            'message' => $validated['message'],
            'sender_type' => $validated['sender_type'],
            'sender_id' => $validated['sender_id'],
            'is_system_message' => false,
        ]);

        // Broadcast evento
        broadcast(new MeetingMessageSent($meetingMessage))->toOthers();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $meetingMessage->id,
                'message' => $meetingMessage->message,
                'sender_type' => $meetingMessage->sender_type,
                'sender_id' => $meetingMessage->sender_id,
                'sender_name' => $meetingMessage->sender_name,
                'is_system_message' => $meetingMessage->is_system_message,
                'created_at' => $meetingMessage->created_at->toISOString(),
                'formatted_time' => $meetingMessage->formatted_time,
            ],
        ]);
    }
}
