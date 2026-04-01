<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Models\ImpersonationToken;
use App\Mail\OtpVerificationMail;
use App\Mail\PlanChangedMail;
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

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'plans' => Plan::where('status', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'timezone' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|string|in:Monthly,Annual,Manual / custom',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'internal_notes' => 'nullable|string',
            'mark_email_verified' => 'boolean',
            'is_active' => 'boolean',
            'send_welcome_email' => 'boolean',
            'require_password_change' => 'boolean',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'timezone' => $validated['timezone'] ?: 'UTC',
            'password' => !empty($validated['password']) ? \Illuminate\Support\Facades\Hash::make($validated['password']) : \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
            'email_verified_at' => ($validated['mark_email_verified'] ?? false) ? now() : null,
            'status' => ($validated['is_active'] ?? true) ? 'active' : 'suspended',
            'internal_notes' => $validated['internal_notes'],
            'force_password_reset' => $validated['require_password_change'] ?? false,
        ]);

        // Assign subscription
        $plan = Plan::findOrFail($validated['plan_id']);
        $startsAt = !empty($validated['starts_at']) ? \Carbon\Carbon::parse($validated['starts_at']) : now();
        $endsAt = !empty($validated['ends_at']) ? \Carbon\Carbon::parse($validated['ends_at']) : 
                  ($validated['billing_cycle'] === 'Annual' ? $startsAt->copy()->addYear() : 
                  ($validated['billing_cycle'] === 'Monthly' ? $startsAt->copy()->addMonth() : null));

        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'provider' => 'manual',
            'internal_notes' => 'Assigned during account creation from admin portal',
        ]);

        if ($validated['send_welcome_email'] ?? false) {
            Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user->first_name));
        }

        // Send 'set your password' email link if the password was left blank
        if (empty($validated['password'])) {
            \Illuminate\Support\Facades\Password::sendResetLink([
                'email' => $user->email
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User successfully created.');
    }

    public function destroy(User $user)
    {
        // Hard delete in safe dependency order
        // 1. Subscriptions
        $user->subscriptions()->delete();

        // 2. Usage logs
        $user->usageLogs()->delete();

        // 3. Connected social / OAuth accounts
        if (method_exists($user, 'socialAccounts')) {
            $user->socialAccounts()->delete();
        }

        // 4. Generated content
        if (method_exists($user, 'generatedContents')) {
            $user->generatedContents()->delete();
        }

        // 5. Impersonation tokens (if any)
        \App\Models\ImpersonationToken::where('user_id', $user->id)->delete();

        // 6. Finally, remove the user row
        $user->forceDelete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Account permanently deleted.');
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
            'allPlans' => Plan::where('status', true)->get(),
            'stats' => $stats,
            'activityLog' => $activityLog,
        ]);
    }

    public function changeSubscription(Request $request, User $user)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|string|in:monthly,annual,manual',
            'effective_date' => 'nullable|string|in:immediate,period_end',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date',
            'reason' => 'required|string|max:500',
            'notify_user' => 'boolean',
            'reset_usage' => 'boolean',
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);
        $oldSubscription = $user->subscriptions()->where('status', 'active')->first();
        $oldPlan = $oldSubscription?->plan;

        // Determine start date
        $startsAt = now();
        if (isset($validated['starts_at'])) {
            $startsAt = now()->parse($validated['starts_at']);
        } elseif (isset($validated['effective_date']) && $validated['effective_date'] === 'period_end' && $oldSubscription) {
            $startsAt = $oldSubscription->ends_at ?: now();
        }

        // Update or create active subscription
        if ($oldSubscription) {
            $oldSubscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        }

        // Determine end date
        $endsAt = isset($validated['ends_at']) ? now()->parse($validated['ends_at']) : 
                  ($validated['billing_cycle'] === 'annual' ? $startsAt->copy()->addYear() : $startsAt->copy()->addMonth());

        $user->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'provider' => 'manual',
            'internal_notes' => $validated['reason'],
        ]);

        // Handle usage reset
        if ($validated['reset_usage'] ?? false) {
            $user->usageLogs()->delete();
        }

        // Email notification if $validated['notify_user']
        if ($validated['notify_user'] ?? false) {
            Mail::to($user->email)->send(new PlanChangedMail(
                $user, 
                $plan->name, 
                $oldPlan?->name
            ));
        }

        return back()->with('success', "Plan changed to {$plan->name} successfully.");
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

    public function resetPostUsage(User $user)
    {
        // Delete all usage_logs for this user created in the current calendar month,
        // effectively resetting their monthly post quota back to 0.
        $user->usageLogs()
            ->where('logged_at', '>=', now()->startOfMonth())
            ->delete();

        // TODO (future): Write an audit log row with:
        //   action: post_usage_reset, admin_id: auth()->id(), user_id: $user->id, timestamp: now()
        // This will allow tracking which admin triggered the reset and when.

        return back()->with('success', 'Post usage reset successfully.');
    }

    public function saveNote(Request $request, User $user)
    {
        $request->validate([
            'note' => 'nullable|string'
        ]);

        $user->internal_notes = $request->note;
        $user->save();

        return back()->with('success', 'Note saved.');
    }
}
