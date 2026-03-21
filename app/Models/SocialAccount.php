<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'platform_id',
        'account_name',
        'account_username',
        'external_account_id',
        'external_business_id',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'account_meta',
        'status',
        'last_synced_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'account_meta' => 'array',
        'last_synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
