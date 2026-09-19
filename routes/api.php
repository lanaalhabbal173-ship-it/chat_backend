<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Models\User;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


// Users list
Route::middleware('auth:sanctum')->get('/users', function (Request $request) {

    return User::where('id', '!=', $request->user()->id)
        ->get();

});


// Authentication
Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {


    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);


    /*
    |--------------------------------------------------------------------------
    | Conversations
    |--------------------------------------------------------------------------
    */


    // Create or get conversation with user
    Route::post('/conversations', 
        [ConversationController::class, 'start']
    );


    // Get my conversations
    Route::get('/conversations', 
        [ConversationController::class, 'index']
    );


    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */


    // Get messages of conversation
    Route::get('/conversations/{conversation}/messages',
        [MessageController::class, 'index']
    );


    // Send message
    Route::post('/conversations/{conversation}/messages',
        [MessageController::class, 'store']
    );


});