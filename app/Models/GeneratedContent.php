<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedContent extends Model
{
    protected $fillable = [
        'user_id',
        'template_id',
        'platform_id',
        'content_type_id',
        'content_type',
        'category',
        'prompt',
        'ai_response',
        'result',
        'status',
        'settings_json',
    ];

    protected $casts = [
        'settings_json' => 'array',
    ];
}

