<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'badge_label',
        'is_most_popular',
        'monthly_price',
        'annual_price',
        'trial_days',
        'currency',
        'stripe_price_id',
        'post_limit_type',
        'post_limit_count',
        'max_social_accounts',
        'max_brand_profiles',
        'max_platforms',
        'features',
        'status',
        'allow_new_signups',
        'show_on_pricing',
        'internal_notes',
    ];

    protected $casts = [
        'features' => 'array',
        'is_most_popular' => 'boolean',
        'status' => 'boolean',
        'allow_new_signups' => 'boolean',
        'show_on_pricing' => 'boolean',
        'monthly_price' => 'decimal:2',
        'annual_price' => 'decimal:2',
    ];
}
