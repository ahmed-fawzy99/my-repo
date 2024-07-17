<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory, HasUlids;

    /**
     * The boot method is called when the model is initialized.
     * It is used to define model event hooks.
     *
     * In this case, the `creating` event is hooked to check if a conversation
     * between two users already exists before creating a new one.
     *
     * If a conversation already exists, the creation of the new model is canceled.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $exists = static::where(function($query) use ($model) {
                $query->where('user_1', $model->user_2)
                    ->where('user_2', $model->user_1);
            })->exists();

            if ($exists) {
                // Cancel the creation of the model
                throw new \Exception('Conversation already exists');
                return false;
            }
        });
    }


    public function user_1(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(User::class, 'user_1');
    }
    public function user_2(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(User::class, 'user_2');
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function hasMessages(): bool
    {
        return $this->messages()->count() > 0;
    }
}
