<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryApiController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () { //prefix auth
    //PRIHLÁSENIE
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1'); //login môžeš robiť 5krát v priebehu 1minúty

    //PRE PRIHLÁSENÝCH
    Route::middleware('auth:sanctum')->group(function () {
        //profil
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);

        //pfp
        Route::post('/me/profile-photo', [AuthController::class, 'storeProfilePhoto']);
        Route::delete('/me/profile-photo', [AuthController::class, 'destroyProfilePhoto']);
    });

    //VERIFIED
    Route::middleware(['auth:sanctum','verified'])->group(function () {

        Route::get('/verified', function () {
            return 'OK';
        });

    });
});

//PRE PRIHLÁSENÝCH BEZ PREFIXU AUTH
Route::middleware('auth:sanctum')->group(function () {
    //NOTES
    Route::get('/my-notes', [NoteController::class, 'myNotes']); // len moje poznámky (aj drafty)

    Route::get('/notes', [NoteController::class, 'index']);      // nástenka - všetky poznámky
    Route::post('/notes', [NoteController::class, 'store']);
    Route::get('/notes/{note}', [NoteController::class, 'show']);
    Route::patch('/notes/{note}', [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

    Route::patch('/notes/{id}/publish', [NoteController::class, 'publish']);
    Route::patch('/notes/{id}/archive', [NoteController::class, 'archive']);
    Route::patch('/notes/{id}/pin', [NoteController::class, 'pin']);
    Route::patch('/notes/{id}/unpin', [NoteController::class, 'unpin']);

    Route::get('notes/stats/status', [NoteController::class, 'statsByStatus']);
    Route::patch('notes/actions/archive-old-drafts', [NoteController::class, 'archiveOldDrafts']);
    Route::get('users/{userId}/notes', [NoteController::class, 'userNotesWithCategories']);
    Route::get('notes-actions/search', [NoteController::class, 'search']);
    Route::get('notes/actions/pinned', [NoteController::class, 'pinnedNotes']);



    //TASKS
    Route::apiResource('notes.tasks', TaskController::class)->scoped();


    //CATEGORIES
    //keď si prihlásený môžeš vidieť kategórie
    Route::apiResource('categories', CategoryApiController::class)
        ->only(['index','show']);

    //ale ak si admin môžeš robiť nad nimi CRUD
    Route::middleware('admin')->group(function () {

        Route::apiResource('categories', CategoryApiController::class)
            ->except(['index','show']);

    });

    //COMMENTS

    // COMMENTS pre NOTE
    Route::get('/notes/{note}/comments', [CommentController::class, 'indexNote']);
    Route::post('/notes/{note}/comments', [CommentController::class, 'storeNote']);

    // COMMENTS pre TASK
    Route::get('/notes/{note}/tasks/{task}/comments', [CommentController::class, 'indexTask']);
    Route::post('/notes/{note}/tasks/{task}/comments', [CommentController::class, 'storeTask']);

    // COMMENTS CRUD
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
    Route::patch('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    //DOCUMENTS
    Route::get('notes/{note}/attachments', [AttachmentController::class, 'index']);
    Route::get('attachments/{attachment:public_id}/link', [AttachmentController::class, 'link']);

    Route::post('notes/{note}/attachments', [AttachmentController::class, 'store'])
        ->middleware('premium');
    Route::delete('notes/{note}/attachments/{attachment}', [AttachmentController::class, 'destroy'])
        ->middleware('premium');
});
