<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClubAdminController;
use App\Http\Controllers\Api\PosetController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\JoueurController;
use App\Http\Controllers\Api\ClubJoueurRequestController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\TitreController;
use App\Http\Middleware\isClub_admin;
use App\Http\Middleware\isJoueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
/*|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('/users/conversations', [UserController::class, 'getConversations']);
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
    Route::delete('/users/{id}/unfollow', [UserController::class, 'unfollow']);
    Route::get('/users/{id}/followers', [UserController::class, 'followers']);
    Route::get('/users/{id}/following', [UserController::class, 'following']);
    Route::get('/users/{id}/followers/count', [UserController::class, 'countFollowers']);
    Route::get('/users/{id}/following/count', [UserController::class, 'countFollowing']);
    Route::get('/users/{id}/is-following', [UserController::class, 'isFollowing']);

    Route::get('/posts/{postId}/comments', [CommentController::class, 'getCommentsByPostId']);
    Route::get('/comments/{commentId}/replies', [CommentController::class, 'getRepliesByCommentId']);


    Route::get('/posts/user/{id_user}', [PosetController::class, 'getPostsByUserId']);
    Route::get('/posts', [PosetController::class, 'getAllPosts']);
    Route::get('/posts/{id}', [PosetController::class, 'getPostById']);
    Route::post('/posts', [PosetController::class, 'createPost']);
    Route::put('/posts/{id}', [PosetController::class, 'updatePost']);
    Route::delete('/posts/{id}', [PosetController::class, 'deletePost']);

    Route::post('/posts/{postId}/comments', [CommentController::class, 'addComment']);
    Route::post('/comments/{commentId}/replies', [CommentController::class, 'addReply']);
    Route::patch('/comments/{id}', [CommentController::class, 'updateComment']);
    Route::patch('/replies/{id}', [CommentController::class, 'updateReply']);
    Route::delete('/comments/{id}', [CommentController::class, 'deleteComment']);
    Route::delete('/replies/{id}', [CommentController::class, 'deleteReply']);

    Route::post('/reactions', [ReactionController::class, 'createReaction']);
    Route::delete('/reactions/{id}', [ReactionController::class, 'deleteReaction']);

    Route::get('/club-admins', [ClubAdminController::class, 'index']);
    Route::post('/club-admins', [ClubAdminController::class, 'create'])->middleware(isClub_admin::class);
    Route::get('/club-admins/user/{userId}', [ClubAdminController::class, 'getByUserId']);
    Route::put('/club-admins/user/{userId}', [ClubAdminController::class, 'update'])->middleware(isClub_admin::class);
    Route::delete('/club-admins/user/{userId}', [ClubAdminController::class, 'delete'])->middleware(isClub_admin::class);
    Route::get('/club-admins/search/{name}', [ClubAdminController::class, 'searchByName']);
    Route::get('/club-admins/{id}', [ClubAdminController::class, 'show']);
    Route::get('/club-admins/exists/{userId}', [ClubAdminController::class, 'clubAdminExists']);
    Route::post('/club-admins/invite-player', [ClubAdminController::class, 'invitePlayer'])->middleware(isClub_admin::class);

    Route::get('/titres/club-admin/{clubAdminId}', [TitreController::class, 'getTitresByClubAdminId']);
    Route::post('/titres', [TitreController::class, 'store'])->middleware(isClub_admin::class);
    Route::delete('/titres/{id}', [TitreController::class, 'destroy'])->middleware(isClub_admin::class);
    Route::put('/titres/{id}', [TitreController::class, 'update'])->middleware(isClub_admin::class);
    Route::get('/titres', [TitreController::class, 'index']);
    Route::get('/titres/{id}', [TitreController::class, 'show']);


    Route::get('/joueurs', [JoueurController::class, 'index']);
    Route::get('/joueurs/{id}', [JoueurController::class, 'show']);
    Route::post('/joueurs', [JoueurController::class, 'store']);
    Route::get('/joueurs/{id}/experiences', [JoueurController::class, 'getExperiencesByJoueurId']);
    Route::post('/experiences', [JoueurController::class, 'createExperience'])->middleware(isJoueur::class);
    Route::put('/experiences/{id}', [JoueurController::class, 'updateExperience'])->middleware(isJoueur::class);
    Route::delete('/experiences/{id}', [JoueurController::class, 'deleteExperience'])->middleware(isJoueur::class);

    Route::get('/club-joueur-requests', [ClubJoueurRequestController::class, 'index']);
    Route::get('/player/my-club', [ClubJoueurRequestController::class, 'myClub'])->middleware(isJoueur::class);
    Route::post('/player/leave-club', [ClubJoueurRequestController::class, 'leave'])->middleware(isJoueur::class);
    Route::post('/club-joueur-requests/{id}/accept', [ClubJoueurRequestController::class, 'accept'])->middleware(isJoueur::class);
    Route::post('/club-joueur-requests/{id}/reject', [ClubJoueurRequestController::class, 'reject'])->middleware(isJoueur::class);
    Route::get('/user/authenticated', [AuthController::class, 'getUserAuthenticated']);
    Route::get('/users/search/{name}', [UserController::class, 'searchByName']);
});

Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/broadcasting/auth', function (Request $request) {
    return Broadcast::auth($request);
})->middleware('auth:sanctum');
