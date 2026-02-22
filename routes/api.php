<?php

use App\Http\Controllers\Api\QuoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/quotes', [QuoteController::class, 'store']);
Route::get('/get_quotes', [QuoteController::class, 'getQuotes']);
Route::get('/get_post_for_upload', [QuoteController::class, 'get_post_for_upload']);
Route::post('/update_media_id_in_post', [QuoteController::class, 'update_media_id_in_post']);