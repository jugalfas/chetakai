<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conversation_id',
        'parent_message_id',
        'role',
        'content',
        'version',
        'is_superseded',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_message_id');
    }

    public function children()
    {
        return $this->hasMany(Message::class, 'parent_message_id');
    }
}
