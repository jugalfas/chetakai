<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Models\ImpersonationToken;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $planType = $request->input('plan', 'all');
        $search = $request->input('search', '');

        $query = User::query()
            ->with(['subscriptions.plan'])
            ->withCount('generatedContents')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($planType !== 'all', function ($query) use ($planType) {
                if ($planType === 'free') {
                    $query->where(function ($q) {
                        $q->whereHas('subscriptions', function ($sq) {
                            $sq->where('status', 'active')
                               ->whereHas('plan', function ($p) {
                                   $p->where('type', 'free');
                               });
                        })->orWhereDoesntHave('subscriptions', function ($sq) {
                            $sq->where('status', 'active');
                        });
                    });
                } else {
                    $query->whereHas('subscriptions', function ($q) use ($planType) {
                        $q->where('status', 'active')
                          ->whereHas('plan', function ($p) use ($planType) {
                              $p->where('type', $planType);
                          });
                    });
                }
            });

        // Calculate stats
        $totalUsers = User::count();
        $newThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        $activeUsers = User::where('status', 'active')->count();
        $suspendedUsers = User::where('status', 'suspended')->count();
        $bannedUsers = User::where('status', 'banned')->count();
        $onboardingDoneCount = User::whereNotNull('onboarding_completed_at')->count();
        $onboardingRate = $totalUsers > 0 ? ($onboardingDoneCount / $totalUsers) * 100 : 0;

        $defaultPlan = Plan::where('type', 'free')->first();

        return Inertia::render('Admin/Users/Index', [
            'users' => $query->latest()->paginate(10)->withQueryString(),
            'defaultPlan' => $defaultPlan,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'plan' => $planType,
            ],
            'stats' => [
                [
                    'label' => 'Total users',
                    'value' => number_format($totalUsers),
                    'subtext' => ($newThisMonth >= 0 ? '+' : '') . number_format($newThisMonth) . ' this month',
                ],
                [
                    'label' => 'Active',
                    'value' => number_format($activeUsers),
                    'subtext' => ($totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0) . '% of total',
                ],
                [
                    'label' => 'Suspended / banned',
                    'value' => number_format($suspendedUsers + $bannedUsers),
                    'subtext' => "{$suspendedUsers} suspended · {$bannedUsers} banned",
                ],
                [
                    'label' => 'Onboarding done',
                    'value' => round($onboardingRate, 1) . '%',
                    'subtext' => "{$onboardingDoneCount} of " . number_format($totalUsers) . " users",
                ],
            ]
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }

    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:active,suspended,banned',
        ]);

        $user->update([
            'status' => $validated['status']
        ]);

        return back()->with('success', "User status updated to {$validated['status']}.");
    }

    public function show(User $user)
    {
        $user->load([
            'subscriptions.plan',
            'socialAccounts',
        ]);

        // Mixed activity log: recent generated contents + recent usage logs
        $recentContents = $user->generatedContents()
            ->with(['platform', 'contentType'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($c) {
                return [
                    'id' => 'content_' . $c->id,
                    'type' => $c->status === 'published' ? 'Published' : ($c->status === 'failed' ? 'Failed' : 'Generated'),
                    'description' => $c->title ?: ($c->contentType?->name . ' content' . ($c->platform ? ' for ' . $c->platform->name : '')),
                    'platform' => $c->platform?->name,
                    'created_at' => $c->created_at,
                    'status' => $c->status,
                ];
            });

        $usageLogs = $user->usageLogs()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($l) {
                return [
                    'id' => 'log_' . $l->id,
                    'type' => ucfirst($l->metric),
                    'description' => ucfirst($l->metric) . ' usage detected',
                    'platform' => null,
                    'created_at' => $l->created_at,
                    'status' => 'info',
                ];
            });

        $activityLog = $recentContents->concat($usageLogs)->sortByDesc('created_at')->values()->take(10);

        // Get count of generated contents
        $user->loadCount('generatedContents');

        // Content breakdown by content type
        $contentBreakdown = $user->generatedContents()
            ->join('content_types', 'generated_contents.content_type_id', '=', 'content_types.id')
            ->selectRaw('content_types.name as title, count(*) as count')
            ->groupBy('content_types.name')
            ->get();

        $defaultPlan = Plan::where('type', 'free')->first();

        // Calculate stats for the UI
        $stats = [
            'posts_this_month' => $user->generatedContents()
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
            'total_posts_ever' => $user->generated_contents_count,
            'published_count' => $user->generatedContents()
                ->where('status', 'published')
                ->count(),
        ];

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'contentBreakdown' => $contentBreakdown,
            'defaultPlan' => $defaultPlan,
            'stats' => $stats,
            'activityLog' => $activityLog,
        ]);
    }

    public function impersonate(User $user)
    {
        $token = ImpersonationToken::create([
            'admin_id' => auth()->id(),
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => now()->addMinutes(15),
        ]);

        return response()->json([
            'url' => route('impersonate.login', $token->token)
        ]);
    }

    public function resetOtp(User $user)
    {
        $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpVerificationMail($user, $otp));

        return back()->with('success', "Reset OTP sent to {$user->email}.");
    }
}
