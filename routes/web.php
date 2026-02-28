<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InstagramAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PromptController;
use App\Models\Post;
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

Route::get('/mail-preview', function () {
    $data = [
        'name' => 'Jugal Faswala',
        'email' => 'jugalfaswala@gmail.com',
        'message' => "Hello!\n\nThis is a preview of the new contact form email template. It supports multiple lines and uses the custom layout we just created.\n\nBest regards,\nChetak Team"
    ];

    return new App\Mail\ContactFormSubmitted($data);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{id}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::patch('/conversations/{id}/rename', [ChatController::class, 'rename'])->name('conversations.rename');
    Route::delete('/conversations/{id}', [ChatController::class, 'destroyConversation'])->name('conversations.destroy');
    Route::patch('/conversations/{id}/pin', [ChatController::class, 'pin'])->name('conversations.pin');
    Route::patch('/messages/{id}/edit', [ChatController::class, 'editMessage'])->name('messages.edit');
    Route::post('/messages/{id}/regenerate', [ChatController::class, 'regenerateMessage'])->name('messages.regenerate');
    Route::delete('/messages/{id}', [ChatController::class, 'destroyMessage'])->name('messages.destroy');

    Route::resource('posts', PostController::class)
        ->except(['show', 'create', 'edit'])
        ->names('quotes')
        ->parameters(['posts' => 'post']);
    Route::post('posts/{post}/schedule', [PostController::class, 'schedule'])->name('quotes.schedule');
    Route::get('/render/quote/{id}', [PostController::class, 'render']);

    Route::resource('prompts', PromptController::class)->except(['show', 'create', 'edit', 'store']);
    Route::post('prompts/bulk', [PromptController::class, 'bulk'])->name('prompts.bulk');
});

Route::get('/dashboard', function () {
    $posts = Post::count();
    $scheduledPosts = Post::where('status', 'scheduled')->count();
    $postedPosts = Post::where('status', 'posted')->count();
    return Inertia::render('Dashboard', [
        'posts' => $posts,
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

    // Instagram Auth
    Route::get('/auth/instagram/redirect', [InstagramAuthController::class, 'redirect'])->name('auth.instagram.redirect');
    Route::get('/auth/instagram/callback', [InstagramAuthController::class, 'callback'])->name('auth.instagram.callback');
});


require __DIR__ . '/auth.php';
