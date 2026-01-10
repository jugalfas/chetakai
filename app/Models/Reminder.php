<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'date',
        'sent_at',
    ];

    protected $casts = [
        'date' => 'date',
        'sent_at' => 'datetime',
    ];
}
