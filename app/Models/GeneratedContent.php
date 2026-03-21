<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneratedContent extends Model
{
    protected $fillable = [
        'user_id',
        'template_id',
        'social_account_id',
        'platform_id',
        'content_type_id',
        'prompt_template_id',
        'title',
        'hook',
        'caption',
        'prompt',
        'ai_response',
        'parsed_output',
        'media_url',
        'thumbnail_url',
        'metadata',
        'generation_provider',
        'approval_status',
        'status',
        'scheduled_for',
        'published_at',
        'failed_at',
        'failure_reason',
    ];

    protected $casts = [
        'parsed_output' => 'array',
        'metadata' => 'array',
        'scheduled_for' => 'datetime',
        'published_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    public function promptTemplate()
    {
        return $this->belongsTo(PromptTemplate::class);
    }

    public function contentPublications()
    {
        return $this->hasMany(ContentPublication::class);
    }
}

