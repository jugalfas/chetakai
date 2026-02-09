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
            $path = $this->post->image_path;
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            return asset('storage/' . $path);
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
