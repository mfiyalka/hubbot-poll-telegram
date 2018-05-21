<?php

namespace App\Http\Conversations\Telegram\Flows;

use App\Entities\Customer;

class StartFlow extends Flow
{
    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function run()
    {
        Customer::addFromTelegram($this->user);
        $this->reply('Привіт! Я бот-опитувальник. Для отримання допомоги, відправ мені команду /help');
    }
}
