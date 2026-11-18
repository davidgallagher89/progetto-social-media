<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\AiController;

// Post
Route::get('/posts', [PostController::class, 'index']);
Route::post('/posts', [PostController::class, 'store']);
Route::delete('/posts/{id}', [PostController::class, 'destroy']);

// Commenti
Route::post('/comments', [CommentController::class, 'store']);
Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

// Like
Route::post('/likes/toggle', [LikeController::class, 'toggle']);

// Funzioni AI
Route::post('/ai/generate', [AiController::class, 'generatePost']);
Route::post('/ai/summarize', [AiController::class, 'summarizeComments']);
