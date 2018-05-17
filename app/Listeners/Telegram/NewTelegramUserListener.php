<?php

namespace App\Listeners\Telegram;

use App\Entities\Customer;
use App\Events\Telegram\NewTelegramUser;
use Telegram;

class NewTelegramUserListener
{
    /**
     * @param NewTelegramUser $event
     * @throws \Throwable
     */
    public function handle(NewTelegramUser $event)
    {
        $user = $event->user;
        $recipients = $event->recipients;
        $quantity = Customer::where(['messenger' => Customer::TELEGRAM])->count();
        $render = view('telegram.new_user', compact('user', 'quantity'))->render();

        foreach ($recipients as $recipient) {
            try {
                Telegram::bot()->sendMessage([
                    'chat_id' => $recipient,
                    'text' => $render,
                    'parse_mode' => 'HTML'
                ]);
            } catch (\Exception $exception) {}
        }
    }
}
