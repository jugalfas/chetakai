<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptTemplate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'platform_id',
        'content_type_id',
        'name',
        'scope',
        'role',
        'version',
        'prompt_template',
        'output_schema',
        'variables',
        'model_preferences',
        'notes',
        'is_default',
        'status',
    ];

    protected $casts = [
        'variables' => 'array',
        'output_schema' => 'array',
        'model_preferences' => 'array',
        'is_default' => 'boolean',
        'status' => 'boolean',
        'version' => 'integer',
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
}
