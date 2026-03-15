<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\StudioCategory;
use App\Models\Template;
use App\Models\GeneratedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContentStudioController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $defaultCategories = [
            'Motivation','Business','AI','Finance','Self Improvement','Fitness','Travel','Fashion','Memes','Relationships','Marketing','Education','Technology','Productivity','Health'
        ];

        $userCategories = StudioCategory::where('user_id', $user->id)->orderBy('name')->get(['id','name','slug']);
        $categories = $userCategories->pluck('name')->toArray();
        $categories = array_values(array_unique(array_merge($defaultCategories, $categories)));

        $templates = Template::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get(['id','template_name','content_type','settings_json']);

        $history = GeneratedContent::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get(['id','content_type','category','result','created_at']);

        return Inertia::render('Studio/Index', [
            'categories' => $categories,
            'templates' => $templates,
            'history' => $history,
        ]);
    }
}
