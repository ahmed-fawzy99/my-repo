<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'sender_id',
        'conversation_id',
    ];

    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

//    public function receiver(): \Illuminate\Database\Eloquent\Relations\hasOneThrough
//    {
//        return $this->hasOneThrough(Conversation::class, 'receiver_id');
//    }

    public function conversation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
