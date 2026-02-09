<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quote_id',
        'caption',
        'image_path',
        'hook',
        'type',
        'scheduled_at',
        'status',
    ];

    const TYPE_POST = 'post';
    const TYPE_REEL = 'reel';

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_POSTED = 'posted';
    const STATUS_FAILED = 'failed';

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
