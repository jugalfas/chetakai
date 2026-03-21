<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'platform_id',
        'content_type_id',
        'category_id',
        'goal_id',
        'tone_id',
        'audience_id',
        'style_id',
        'brand_kit_id',
        'name',
        'description',
        'posting_mode',
        'posts_per_day',
        'preferred_time',
        'timezone',
        'length',
        'custom_instructions',
        'generation_settings',
        'is_active',
    ];

    protected $casts = [
        'posts_per_day' => 'integer',
        'is_active' => 'boolean',
        'generation_settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class)->withDefault();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function contentGoal(): BelongsTo
    {
        return $this->belongsTo(ContentGoal::class, 'goal_id')->withDefault();
    }

    public function tone(): BelongsTo
    {
        return $this->belongsTo(Tone::class)->withDefault();
    }

    public function audience(): BelongsTo
    {
        return $this->belongsTo(Audience::class)->withDefault();
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class)->withDefault();
    }

    public function brandKit(): BelongsTo
    {
        return $this->belongsTo(BrandKit::class, 'brand_kit_id')->withDefault();
    }

    public function templateAttributes(): HasMany
    {
        return $this->hasMany(TemplateAttribute::class);
    }
    
    public function automationRules(): HasMany
    {
        return $this->hasMany(AutomationRule::class);
    }

    public function generatedContents(): HasMany
    {
        return $this->hasMany(GeneratedContent::class);
    }
}
