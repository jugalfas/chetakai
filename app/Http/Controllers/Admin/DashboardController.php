<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'active_users' => \App\Models\User::where('status', 'active')->count(),
            'generated_today' => \App\Models\GeneratedContent::whereDate('created_at', today())->count(),
            'published_today' => \App\Models\GeneratedContent::whereDate('published_at', today())->count(),
            'failed_jobs' => \Illuminate\Support\Facades\DB::table('failed_jobs')->count(),
            'unread_contacts' => \App\Models\Contact::where('is_read', false)->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats
        ]);
    }
}
