<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Thread;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

//Broadcast::channel('thread.{id}', function ($user, Thread $thread) {
  //  return (int) $user->id === (int) $thread->message->user_id;
//}, ['guards' => ['client']]);


// Broadcast::channel('thread.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
