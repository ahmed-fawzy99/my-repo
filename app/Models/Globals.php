<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Globals extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Check if there's already a record
            if (static::count() > 0) {
                return false; // Cancel the creation
            }
        });
    }
}
