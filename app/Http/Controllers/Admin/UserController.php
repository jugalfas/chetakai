<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
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
}
