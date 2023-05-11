<?php

namespace App\Events;

use App\Models\Thread;
use App\Models\Client;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\ThreadResource;
use Illuminate\Support\Facades\Log;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $thread;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($thread)
    {

        $this->thread = $thread;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {

        //return new PrivateChannel('channel-name');
        return new Channel('message');
    }


    // public function broadcastWith()
    // {
    //     return [
    //         'body' => $this->thread
    //     ];
    // }


    public function broadcastAs()
    {
        return 'message.created';
    }
}
