<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    protected $fillable = [
        'title',
        'script',
        'video_path',
        'audio_path',
        'duration',
        'status',
    ];

    protected $casts = [
        'duration' => 'float',
    ];
}
