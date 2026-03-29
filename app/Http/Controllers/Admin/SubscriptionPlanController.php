<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');

        $query = Plan::query();
        if ($status === 'active') {
            $query->where('status', true);
        } elseif ($status === 'inactive') {
            $query->where('status', false);
        }

        $activePlans = Plan::where('status', true)->count();
        $inactivePlans = Plan::where('status', false)->count();
        $totalPlans = $activePlans + $inactivePlans;

        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $newSubscriptionsThisMonth = Subscription::where('status', 'active')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        // Calculate MRR
        $mrr = Subscription::where('subscriptions.status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.monthly_price');

        // Growth calculations (comparing with last month)
        $lastMonthMrr = Subscription::where('subscriptions.status', 'active')
            ->where('subscriptions.created_at', '<', now()->startOfMonth())
            ->where('subscriptions.created_at', '>=', now()->subMonth()->startOfMonth())
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.monthly_price');

        $mrrGrowth = $lastMonthMrr > 0 ? (($mrr - $lastMonthMrr) / $lastMonthMrr) * 100 : 0;

        // Free -> Paid rate
        $totalUsers = User::count();
        $paidUsers = Subscription::where('subscriptions.status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('plans.monthly_price', '>', 0)
            ->distinct('user_id')
            ->count('user_id');
        $conversionRate = $totalUsers > 0 ? ($paidUsers / $totalUsers) * 100 : 0;

        return Inertia::render('Admin/SubscriptionPlans/Index', [
            'subscriptionPlans' => $query->get(),
            'filters' => [
                'status' => $status,
            ],
            'stats' => [
                [
                    'label' => 'Total plans',
                    'value' => number_format($totalPlans),
                    'subtext' => "{$activePlans} active, {$inactivePlans} inactive",
                ],
                [
                    'label' => 'Total subscribers',
                    'value' => number_format($activeSubscriptions),
                    'subtext' => ($newSubscriptionsThisMonth >= 0 ? '+' : '') . number_format($newSubscriptionsThisMonth) . ' this month',
                ],
                [
                    'label' => 'MRR',
                    'value' => '$' . number_format($mrr, 2),
                    'subtext' => ($mrrGrowth >= 0 ? '+' : '-') . round(abs($mrrGrowth), 1) . '% vs last month',
                ],
                [
                    'label' => 'Free → paid rate',
                    'value' => round($conversionRate, 1) . '%',
                    'subtext' => 'Avg. conversion rate',
                ],
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/SubscriptionPlans/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:free,paid,custom',
            'description' => 'nullable|string',
            'monthly_price' => 'nullable|numeric|min:0',
            'annual_price' => 'nullable|numeric|min:0',
            'trial_days' => 'nullable|integer|min:0',
            'currency' => 'required|string|size:3',
            'stripe_price_id' => 'nullable|string',
            'post_limit_type' => 'required|string',
            'post_limit_count' => 'nullable|integer|min:1',
            'max_social_accounts' => 'nullable|integer|min:0',
            'max_brand_profiles' => 'nullable|integer|min:0',
            'max_platforms' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'allow_new_signups' => 'boolean',
            'show_on_pricing' => 'boolean',
            'internal_notes' => 'nullable|string',
            'badge_label' => 'nullable|string',
            'is_most_popular' => 'boolean',
        ]);

        // Reset other most popular plans if this one is most popular
        if ($request->is_most_popular) {
            Plan::where('is_most_popular', true)->update(['is_most_popular' => false]);
        }

        Plan::create(array_merge($validated, [
            'slug' => Str::slug($validated['name']),
            'status' => $validated['is_active'] ?? true,
        ]));

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function edit(Plan $subscriptionPlan)
    {
        return Inertia::render('Admin/SubscriptionPlans/Create', [
            'plan' => $subscriptionPlan
        ]);
    }

    public function update(Request $request, Plan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:free,paid,custom',
            'description' => 'nullable|string',
            'monthly_price' => 'nullable|numeric|min:0',
            'annual_price' => 'nullable|numeric|min:0',
            'trial_days' => 'nullable|integer|min:0',
            'currency' => 'required|string|size:3',
            'stripe_price_id' => 'nullable|string',
            'post_limit_type' => 'required|string',
            'post_limit_count' => 'nullable|integer|min:1',
            'max_social_accounts' => 'nullable|integer|min:0',
            'max_brand_profiles' => 'nullable|integer|min:0',
            'max_platforms' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'allow_new_signups' => 'boolean',
            'show_on_pricing' => 'boolean',
            'internal_notes' => 'nullable|string',
            'badge_label' => 'nullable|string',
            'is_most_popular' => 'boolean',
        ]);

        if ($request->is_most_popular) {
            Plan::where('id', '!=', $subscriptionPlan->id)->where('is_most_popular', true)->update(['is_most_popular' => false]);
        }

        $subscriptionPlan->update(array_merge($validated, [
            'slug' => Str::slug($validated['name']),
            'status' => $validated['is_active'] ?? true,
        ]));

        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(Plan $subscriptionPlan)
    {
        $subscriptionPlan->delete();
        return redirect()->route('admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }
}
