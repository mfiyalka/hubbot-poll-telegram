<?php

namespace App\Events\Telegram;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Telegram\Bot\Objects\Update;

class CommandEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $command;
    public $update;

    public function __construct(Update $update, string $command)
    {
        $this->command = $command;
        $this->update = $update;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
