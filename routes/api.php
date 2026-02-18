<?php

use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\ReelRenderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/quotes', [QuoteController::class, 'store']);
Route::get('/get_quotes', [QuoteController::class, 'getQuotes']);
Route::post('/update_container_id_in_post', [QuoteController::class, 'update_container_id_in_post']);

Route::get('/reels/pending', [ReelRenderController::class, 'pending']);
Route::post('/reels/complete', [ReelRenderController::class, 'complete']);

Route::post('/audio-duration', [ReelRenderController::class, 'getAudioDuration']);

Route::post('/reels/create', [ReelRenderController::class, 'create']);
