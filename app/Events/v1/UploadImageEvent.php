<?php

namespace App\Events\v1;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UploadImageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $model;
    public $images;
    public $collection;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model,$images,$collection)
    {
        $this->model=$model;
        $this->images=$images;
        $this->collection=$collection;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
