<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'input_type',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function templateAttributes(): HasMany
    {
        return $this->hasMany(TemplateAttribute::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}
