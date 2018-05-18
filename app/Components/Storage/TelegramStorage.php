<?php

namespace App\Components\Storage;

use Telegram\Bot\Objects\User;

trait TelegramStorage
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @return Storage
     */
    public function userStorage()
    {
        return (new Storage($this->user->id));
    }
}
