<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request, Conversation $conversation)
    {
        $userId = $request->user()->id;

        if (
            $conversation->user_one_id !== $userId &&
            $conversation->user_two_id !== $userId
        ) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $messages = $conversation->messages()
            ->with('sender:id,name,email')
            ->orderBy('created_at')
            ->get();

        return response()->json($messages);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $userId = $request->user()->id;

        if (
            $conversation->user_one_id !== $userId &&
            $conversation->user_two_id !== $userId
        ) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $data = $request->validate([
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'message' => $data['message'],
        ]);

        $conversation->touch();

        return response()->json([
            'message' => $message->load('sender:id,name,email'),
        ], 201);
    }
}