<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function start(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $currentUserId = $request->user()->id;
        $otherUserId = (int) $data['user_id'];

        if ($currentUserId === $otherUserId) {
            return response()->json([
                'message' => 'You cannot start a conversation with yourself.'
            ], 422);
        }

        $userOne = min($currentUserId, $otherUserId);
        $userTwo = max($currentUserId, $otherUserId);

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $userOne,
            'user_two_id' => $userTwo,
        ]);

        return response()->json([
            'conversation' => $conversation,
        ]);
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Conversation::with([
            'userOne:id,name,email',
            'userTwo:id,name,email',
            'messages' => function ($query) {
                $query->latest()->limit(1);
            }
        ])
        ->where(function ($query) use ($userId) {
            $query->where('user_one_id', $userId)
                  ->orWhere('user_two_id', $userId);
        })
        ->latest('updated_at')
        ->get();

        return response()->json($conversations);
    }
}