<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/posts', [PostController::class, 'store']);
Route::get('/get_prompt', [PostController::class, 'getPrompt']);
Route::get('/get_post_for_upload', [PostController::class, 'get_post_for_upload']);
Route::post('/update_media_id_in_post', [PostController::class, 'update_media_id_in_post']);
