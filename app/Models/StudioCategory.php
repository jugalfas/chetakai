<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudioCategory extends Model
{
    protected $table = 'studio_categories';

    protected $fillable = [
        'name',
        'slug',
        'user_id',
    ];
}

