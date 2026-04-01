<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PromptManagementController;
use App\Http\Controllers\Admin\ContentStudioController;
use App\Http\Controllers\Admin\PromptTemplateController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\PromptTestController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest:admin')->group(function () {
    Route::redirect('/', '/admin/login');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/content-types/{contentType}/attributes', [TemplateController::class, 'contentTypeAttributes'])->name('content_types.attributes');

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::post('/{user}/impersonate', [UserController::class, 'impersonate'])->name('impersonate');
        Route::post('/{user}/reset-otp', [UserController::class, 'resetOtp'])->name('reset-otp');
        Route::patch('/{user}/status', [UserController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/change-subscription', [UserController::class, 'changeSubscription'])->name('change-subscription');
        Route::post('/{user}/reset-post-usage', [UserController::class, 'resetPostUsage'])->name('reset-post-usage');
        Route::post('/{user}/internal-note', [UserController::class, 'saveNote'])->name('note');
    });

    // Quotes and Categories removed for now

    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->name('index');
        Route::patch('/mark-all-as-read', [ContactController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::patch('/{contact}/read', [ContactController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/{contact}/reply', [ContactController::class, 'reply'])->name('reply');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('prompt-templates')->name('prompt-templates.')->group(function () {
        Route::get('/', [PromptTemplateController::class, 'index'])->name('index');
        Route::get('/create', [PromptTemplateController::class, 'create'])->name('create');
        Route::post('/', [PromptTemplateController::class, 'store'])->name('store');
        Route::get('/{promptTemplate}/edit', [PromptTemplateController::class, 'edit'])->name('edit');
        Route::put('/{promptTemplate}', [PromptTemplateController::class, 'update'])->name('update');
        Route::delete('/{promptTemplate}', [PromptTemplateController::class, 'destroy'])->name('destroy');
        Route::patch('/{promptTemplate}/toggle-status', [PromptTemplateController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{promptTemplate}/test', [PromptTemplateController::class, 'test'])->name('test');
    });

    Route::post('/templates/{template}/generate-prompt', [PromptTestController::class, 'generate'])
        ->name('templates.generate-prompt');

    Route::get('/content-studio', [ContentStudioController::class, 'index'])->name('content-studio.index');
    Route::post('/content-studio/preview', [ContentStudioController::class, 'preview'])->name('content-studio.preview');
    Route::post('/content-studio/generate', [ContentStudioController::class, 'generate'])->name('content-studio.generate');

    Route::prefix('prompts')->group(function () {
        Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
        Route::get('/templates/create', [TemplateController::class, 'create'])->name('templates.create');
        Route::post('/templates', [TemplateController::class, 'store'])->name('templates.store');
        Route::get('/templates/{template}/edit', [TemplateController::class, 'edit'])->name('templates.edit');
        Route::put('/templates/{template}', [TemplateController::class, 'update'])->name('templates.update');
        Route::delete('/templates/{template}', [TemplateController::class, 'destroy'])->name('templates.destroy');
    });

    Route::prefix('prompts')->name('prompts.')->group(function () {
        Route::get('/content-types', [PromptManagementController::class, 'contentTypes'])->name('content_types');
        Route::get('/platforms', [PromptManagementController::class, 'platforms'])->name('platforms');
        Route::get('/categories', [PromptManagementController::class, 'categories'])->name('categories');
        Route::get('/goals', [PromptManagementController::class, 'contentGoals'])->name('content_goals');
        Route::get('/tones', [PromptManagementController::class, 'tones'])->name('tones');
        Route::get('/audiences', [PromptManagementController::class, 'audiences'])->name('audiences');
        Route::get('/styles', [PromptManagementController::class, 'styles'])->name('styles');

        Route::post('/store', [PromptManagementController::class, 'store'])->name('store');
        Route::delete('/delete', [PromptManagementController::class, 'destroy'])->name('destroy');
    });

    // Subscription plans
    Route::prefix('subscription-plans')->name('subscription-plans.')->group(function () {
        Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
        Route::get('/create', [SubscriptionPlanController::class, 'create'])->name('create');
        Route::post('/', [SubscriptionPlanController::class, 'store'])->name('store');
        Route::get('/{subscriptionPlan}/edit', [SubscriptionPlanController::class, 'edit'])->name('edit');
        Route::put('/{subscriptionPlan}', [SubscriptionPlanController::class, 'update'])->name('update');
        Route::delete('/{subscriptionPlan}', [SubscriptionPlanController::class, 'destroy'])->name('destroy');
    });
});
