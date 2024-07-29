<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasFactory, Notifiable, InteractsWithMedia, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'public_key_ecdh',
        'public_key_eddsa',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function contacts(): belongsToMany
    {
        return $this->belongsToMany(User::class,'contacts',
            'user_id', 'contact_id')
            ->where('contact_accepted', true)
            ->withTimestamps();
    }


    private function conversations_2(): hasMany
    {
        return $this->hasMany(Conversation::class, 'user_2');

    }
    public function conversations(): HasMany
    {
//        return $this->hasManyMerged(Conversation::class, ['user_1', 'user_2']);

        return $this->hasMany(Conversation::class, 'user_1')->union($this->conversations_2());
    }

    public function sendMessage(String $conversationId, String $msg, String $signature)
    {
        return Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $this->id,
            'content' => $msg,
            'signature' => $signature,
        ]);
    }


}
