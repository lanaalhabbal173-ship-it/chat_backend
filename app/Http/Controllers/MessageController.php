<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Services\FirebaseNotificationService;

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

 public function store(
    Request $request,
    Conversation $conversation,
    FirebaseNotificationService $notificationService
)
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


    // Save message
    $message = Message::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $userId,
        'message' => $data['message'],
        'is_read' => false,
    ]);


    $conversation->touch();


    // تحديد المستقبل
    $receiverId =
        $conversation->user_one_id == $userId
        ? $conversation->user_two_id
        : $conversation->user_one_id;


    $receiver = \App\Models\User::find($receiverId);


    // إرسال Notification
    if ($receiver && $receiver->fcm_token) {

        $notificationService->sendNotification(
          $receiver->fcm_token,
    $request->user()->name,
    $data['message'],
    $conversation->id,
    $userId
        );

    }


    return response()->json([
        'message' => $message->load('sender:id,name,email'),
    ], 201);
}

    public function markDelivered(Request $request, Message $message)
    {
        $userId = $request->user()->id;

        if ($message->sender_id === $userId) {
            return response()->json([
                'message' => 'You cannot mark your own message as delivered'
            ], 403);
        }

        if ($message->delivered_at === null) {
            $message->update([
                'delivered_at' => now(),
            ]);
        }

        return response()->json([
            'message' => $message->fresh(),
        ]);
    }

    public function markSeen(Request $request, Message $message)
    {
        $userId = $request->user()->id;

        if ($message->sender_id === $userId) {
            return response()->json([
                'message' => 'You cannot mark your own message as seen'
            ], 403);
        }

        $message->update([
            'is_read' => true,
            'delivered_at' => $message->delivered_at ?? now(),
            'seen_at' => now(),
        ]);

        return response()->json([
            'message' => $message->fresh(),
        ]);
    }
}