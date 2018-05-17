<?php

namespace App\Events\Telegram;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Telegram\Bot\Objects\User;

class NewTelegramUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $recipients = [
        '119235180',
    ];

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
