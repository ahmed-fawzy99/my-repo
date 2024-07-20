<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('messages.{userId}', function (User $user, String $userId) {
    return  $user->id === $userId;
});
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return $user->id === $id;
});

//roadcast::channel('messages.{conversationId}', function (User $user, $conversationId) {
//    return  \App\Models\Conversation::find($conversationId)->users->where('id', $user->id)->exists();
//});
