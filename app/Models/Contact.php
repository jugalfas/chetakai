<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
        'is_read',
        'reply_sent_at',
        'reply_content',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'reply_sent_at' => 'datetime',
    ];
}
