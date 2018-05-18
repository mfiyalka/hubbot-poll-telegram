<?php

namespace App\Console\Commands;

use App\Entities\Customer;
use App\Entities\TelegramWebhook;
use App\Events\Telegram\CommandEvent;
use App\Http\Conversations\Telegram\Flows\AnswerInlineQueryFlow;
use App\Http\Conversations\Telegram\Flows\Flow;
use App\Http\Conversations\Telegram\Traits\InteractsWithContext;
use Illuminate\Console\Command;
use Telegram;
use Telegram\Bot\Objects\Update;

class TelegramGetUpdatesCommand  extends Command
{
    use InteractsWithContext;

    /**
     * @var Telegram\Bot\Objects\User
     */
    protected $user;
    protected $signature = 'telegram:updates';
    protected $description = 'Get updates';

    /**
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handle()
    {
        while (true) {
            /** @var Telegram\Bot\Objects\Update $update */
            @$update = Telegram::bot()->getUpdates(['limit' => '1'])[0];
            echo date('Y-m-d | H:m:s', time()) . PHP_EOL;

            if ($update) {
                dump($update);
                TelegramWebhook::add($update);
                switch (@$update->detectType()) {
                    case 'message':
                        $this->handlerMessage($update);
                        break;
                    case 'callback_query':
                        $this->handlerCallbackQuery($update);
                        break;
                    case 'inline_query':
                        $this->handlerInlineQuery($update);
                }

                if ($update->updateId) {
                    Telegram::bot()->getUpdates(['offset' => $update->updateId + 1]);
                    echo 'Cleared' . PHP_EOL;
                }
            }
            sleep(1);
            echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~" . PHP_EOL;
        }
    }

    private function handlerMessage(Update $update)
    {
        $message = $update->getMessage();
        $this->user = $message->from;
        //$this->setLanguage();

        if (
            !is_null($entities = $message->entities) &&
            isset($entities[0]['type']) &&
            $entities[0]['type'] == 'bot_command'
        ) {
            $command = $message->text;
            return event(new CommandEvent($update, $command));
        }

        $context = $this->context();
        if ($context->hasFlow()) {
            $flow = $context->getFlow();
            $flow->setUser($this->user);
            $flow->setUpdate($update);
            $state = $context->getState();
            return $flow->$state();
        }

        return response('', 200);
    }

    private function handlerCallbackQuery(Update $update)
    {
        $data = $update->callbackQuery->data;
        $this->user = $update->callbackQuery->from;
        //$this->setLanguage();

        $flows = config('flows.telegram');

        $segments = explode('@', $data);
        $flow = array_shift($segments);
        $state = array_shift($segments);

        if (isset($flows[$flow])) {
            /**
             * @var Flow $flow
             */
            $flow = app($flows[$flow]);
            $flow->setUser($this->user);
            $flow->setUpdate($update);

            array_map(function($value) use ($flow) {
                $item = explode(':', $value);
                $key = $item[0];
                $value = $item[1];
                $flow->addOption($key, $value);
            }, $segments);

            $flow->$state();
        }
    }

    private function handlerInlineQuery(Update $update)
    {
        $this->user = $update->inlineQuery->from;

        $flow = new AnswerInlineQueryFlow();
        $flow->setUser($this->user);
        $flow->setUpdate($update);
        $flow->run();
    }

    private function setLanguage()
    {
        $user = Customer::where([
            'messenger' => Customer::TELEGRAM,
            'identifier' => $this->user->id
        ])->first();
        if ($user) {
            app()->setLocale($user->language);
        }
    }
}
