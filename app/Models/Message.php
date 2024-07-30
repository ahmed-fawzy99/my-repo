<?php

namespace App\Models;

use App\Events\MessageDeleted;
use App\Events\MessageSent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'sender_id',
        'conversation_id',
        'signature',
        'read',
    ];

    protected $dispatchesEvents = [
        'created' => MessageSent::class,
        'updated' => MessageDeleted::class, // you only update a message when you delete it, as we only remove the content and the signature
        'deleted' => MessageDeleted::class, // you only update a message when you delete it, as we only remove the content and the signature
    ];

    public function sender(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function recipientId()
    {
        return $this->conversation->user_1 === $this->sender_id ? $this->conversation->user_2 : $this->conversation->user_1;
    }

    public function conversation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
