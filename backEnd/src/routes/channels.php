<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('SporeLink.{id}', function ($user, $id) {
    return true; // Allow all authenticated users to listen to this channel
    // return (int) $user->id === (int) $id;
});