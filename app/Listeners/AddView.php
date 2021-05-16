<?php

namespace App\Listeners;

use App\Models\View;
use App\Events\Viewed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddView
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Viewed  $event
     * @return void
     */
    public function handle(Viewed $event)
    {
        $view = View::firstOrCreate(
            [
            'client_id' => $event->client->id,
            'viewer_id' => request()->user()->id,
             ],
             ['read_at' => null]
        );
    }
}
