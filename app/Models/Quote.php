<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quote',
        'category',
        'status',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->post && $this->post->image_path) {
            return asset('storage/' . $this->post->image_path);
        }
        return null;
    }

    public function post()
    {
        return $this->hasOne(Post::class);
    }

    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category');
    }
}
