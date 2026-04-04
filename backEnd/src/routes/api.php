<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('/users/conversations', [UserController::class, 'getConversations']); 
    Route::get('/users/username/{username}', [UserController::class, 'findByUsername']);
    Route::delete('/users/messages/{messageId}', [UserController::class, 'deleteMessage']);
    Route::put('/users/update/messages/{messageId}', [UserController::class, 'updateMessage']);

    Route::get('/users', [UserController::class, 'index']);
    
    Route::get('/users/{id}', [UserController::class, 'show']);
    
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::post('/users/{receiverId}/message', [UserController::class, 'sendMessage']);
    Route::get('/users/{userId1}/messages/{userId2}', [UserController::class, 'getMessages']);
    Route::get('/users/{userId}/messages/{messageId}', [UserController::class, 'updateMessage']);

    Route::post('/users/{id}/follow', [UserController::class, 'follow']);
    Route::post('/users/{id}/unfollow', [UserController::class, 'unfollow']);
    Route::get('/users/{id}/followers', [UserController::class, 'followers']);
    Route::get('/users/{id}/following', [UserController::class, 'following']);
    Route::get('/users/{id}/followers/count', [UserController::class, 'countFollowers']);
    Route::get('/users/{id}/following/count', [UserController::class, 'countFollowing']);
    Route::get('/users/{id}/is-following', [UserController::class, 'isFollowing']);
});

Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});