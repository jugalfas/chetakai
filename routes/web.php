<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Models\Post;
use App\Models\Quote;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
})->name('welcome');

Route::get('/privacy', function () {
    return Inertia::render('Privacy', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
})->name('privacy');

Route::get('/terms-of-service', function () {
    return Inertia::render('TermsOfService', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
})->name('terms-of-service');

Route::get('/contact', function () {
    return Inertia::render('Contact', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('contact');

Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');

    Route::post('quotes/generate', [QuoteController::class, 'generate'])->name('quotes.generate');

    Route::resource('quotes', QuoteController::class)->except(['show', 'create', 'edit']);
    Route::post('quotes/{quote}/schedule', [QuoteController::class, 'schedule'])->name('quotes.schedule');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

Route::get('/dashboard', function () {
    $quotes = Quote::count();
    $scheduledPosts = Post::where('status', 'scheduled')->count();
    $postedPosts = Post::where('status', 'posted')->count();
    return Inertia::render('Dashboard', [
        'quotes' => $quotes,
        'scheduledPosts' => $scheduledPosts,
        'postedPosts' => $postedPosts,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/auto-post-schedule', [ProfileController::class, 'updateAutoPostSchedule'])->name('profile.update.auto-post-schedule');
    Route::patch('/profile/post-notifications', [ProfileController::class, 'updatePostNotifications'])->name('profile.update.post-notifications');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-as-read');
});


require __DIR__ . '/auth.php';
