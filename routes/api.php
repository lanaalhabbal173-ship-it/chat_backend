<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post(
    '/register',
    [AuthController::class, 'register']
);

Route::post(
    '/login',
    [AuthController::class, 'login']
);

Route::middleware('auth:sanctum')->get(
    '/users',
    function (Request $request) {
        return User::where(
            'id',
            '!=',
            $request->user()->id
        )->get();
    }
);

Route::middleware('auth:sanctum')->group(
    function () {

        Route::post(
            '/logout',
            [AuthController::class, 'logout']
        );

        Route::post(
            '/conversations',
            [ConversationController::class, 'start']
        );

        Route::get(
            '/conversations',
            [ConversationController::class, 'index']
        );

        Route::get(
            '/conversations/{conversation}/messages',
            [MessageController::class, 'index']
        );

        Route::post(
            '/conversations/{conversation}/messages',
            [MessageController::class, 'store']
        );

        Route::patch(
            '/messages/{message}/delivered',
            [MessageController::class, 'markDelivered']
        );

        Route::patch(
            '/messages/{message}/seen',
            [MessageController::class, 'markSeen']
        );
    }
);