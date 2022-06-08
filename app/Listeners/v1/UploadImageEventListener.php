<?php

namespace App\Listeners\v1;

use App\Events\v1\UploadImageEvent as UploadImageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UploadImageEventListener implements ShouldQueue
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
     * @param  \App\Events\UploadImageEvent  $event
     * @return void
     */
    public function handle(UploadImageEvent $event)
    {
        //
    }
}
