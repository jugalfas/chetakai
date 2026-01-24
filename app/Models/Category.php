<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'category');
    }
}
