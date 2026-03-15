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
        'template_name',
        'length',
        'bulk_generate',
    ];

    protected $casts = [
        'bulk_generate' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function contentType(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function contentGoal(): BelongsTo
    {
        return $this->belongsTo(ContentGoal::class, 'goal_id');
    }

    public function tone(): BelongsTo
    {
        return $this->belongsTo(Tone::class);
    }

    public function audience(): BelongsTo
    {
        return $this->belongsTo(Audience::class);
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(Style::class);
    }

    public function templateAttributes(): HasMany
    {
        return $this->hasMany(TemplateAttribute::class);
    }
}
