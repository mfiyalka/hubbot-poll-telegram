<?php

namespace App\Listeners\Telegram;

use App\Entities\Customer;
use App\Events\Telegram\CommandEvent;
use App\Http\Conversations\Telegram\Flows\StartFlow;
use Telegram;

class CommandEventListener
{
    /**
     * @param CommandEvent $event
     * @return bool|void
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle(CommandEvent $event)
    {
        $command = $event->command;
        $update = $event->update;

        if ($command == '/start') {
            $flow = new StartFlow();
            $flow->setUser($update->getMessage()->from);
            $flow->setUpdate($update);
            $flow->run();
        }

        if (preg_match('#^/count|cnt$#', trim($command))) {
            $quantity = Customer::where(['messenger' => Customer::TELEGRAM])->count();
            Telegram::bot()->sendMessage([
                'chat_id' => $update->getMessage()->from->id,
                'text' => 'All customers - ' . $quantity
            ]);
        }

        return;
    }
}
