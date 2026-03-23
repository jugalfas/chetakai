<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InstagramAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\PromptGenerationController;
use App\Http\Controllers\Studio\ContentStudioController;
use App\Http\Controllers\Studio\GenerationController;
use App\Http\Controllers\Studio\TemplateController;
use App\Http\Controllers\Studio\CategoryController;
use App\Models\Contact;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
        'canRegister' => Route::has('register'),
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
    // Mail::to('jugalfaswala@gmail.com')->send(new App\Mail\ContactFormSubmitted($data));
    return new App\Mail\ContactFormSubmitted($data);
});

Route::get('/mail-preview2', function () {
    $data = [
        'name' => 'Jugal Faswala'
    ];

    return new App\Mail\WelcomeMail($data['name']);
});

Route::get('/mail-preview3', function () {
    $name = 'John Doe';
    $email = 'john@doe.com';

    $resetUrl = url('/reset-password/sample-token?email=' . urlencode($email));

    return view('emails.auth.reset-password', compact('name', 'resetUrl'));
});

Route::middleware(['auth', 'otp.verified'])->group(function () {
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

    //  Route::resource('posts', PostController::class)
    //      ->except(['show', 'create', 'edit'])
    //      ->names('posts')
    //      ->parameters(['posts' => 'post']);
    //  Route::post('posts/{post}/schedule', [PostController::class, 'schedule'])->name('posts.schedule');
    //  Route::get('/render/post/{id}', [PostController::class, 'render'])->name('posts.render');

    Route::resource('prompts', PromptController::class)->except(['show', 'create', 'edit', 'store']);
    Route::post('prompts/bulk', [PromptController::class, 'bulk'])->name('prompts.bulk');

    Route::get('/studio', [ContentStudioController::class, 'index'])->name('studio.index');
    Route::post('/studio/generate', [GenerationController::class, 'generate'])->name('studio.generate');
    Route::post('/studio/templates', [TemplateController::class, 'store'])->name('studio.templates.store');
    Route::put('/studio/templates/{template}', [TemplateController::class, 'update'])->name('studio.templates.update');
    Route::delete('/studio/templates/{template}', [TemplateController::class, 'destroy'])->name('studio.templates.destroy');
    Route::post('/studio/templates/{template}/duplicate', [TemplateController::class, 'duplicate'])->name('studio.templates.duplicate');
    Route::post('/templates/{template}/generate-prompt', [PromptGenerationController::class, 'generateFromTemplate'])->name('templates.generate-prompt');
    Route::get('/studio/categories', [CategoryController::class, 'index'])->name('studio.categories.index');
    Route::post('/studio/categories', [CategoryController::class, 'store'])->name('studio.categories.store');
    Route::delete('/studio/categories/{category}', [CategoryController::class, 'destroy'])->name('studio.categories.destroy');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'otp.verified'])->name('dashboard');

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
