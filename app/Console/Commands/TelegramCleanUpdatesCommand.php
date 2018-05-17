<?php

namespace App\Console\Commands;

use App\Http\Conversations\Telegram\Traits\InteractsWithContext;
use Illuminate\Console\Command;
use Telegram;

class TelegramCleanUpdatesCommand  extends Command
{
    use InteractsWithContext;

    /**
     * @var Telegram\Bot\Objects\User
     */
    protected $user;
    protected $signature = 'telegram:updates:clean';
    protected $description = 'Delete updates';

    /**
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        /** @var Telegram\Bot\Objects\Update $update */
        while(@$update = Telegram::bot()->getUpdates(['limit' => '1'])[0]) {
            echo date('Y-m-d | H:m:s', time()) . PHP_EOL;
            Telegram::bot()->getUpdates(['offset' => $update->updateId + 1]);
            echo 'Cleared' . PHP_EOL;
            echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~" . PHP_EOL;
        }
    }
}
