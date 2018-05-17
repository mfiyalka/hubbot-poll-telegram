<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram;

class TelegramRemoveWebhookCommand  extends Command
{
    protected $signature = 'telegram:webhook:remove';
    protected $description = 'Remove Telegram\'s webhook';

    /**
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        $result = Telegram::bot()->removeWebhook();
        echo "Remove Webhook: {$result}" . PHP_EOL;
    }
}
