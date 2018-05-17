<?php

namespace App\Http\Controllers\Webhook;

use App\Entities\Customer;
use App\Entities\TelegramWebhook;
use App\Events\Telegram\CommandEvent;
use App\Http\Controllers\Controller;
use App\Http\Conversations\Telegram\Flows\Flow;
use App\Http\Conversations\Telegram\Flows\StartFlow;
use App\Http\Conversations\Telegram\Traits\InteractsWithContext;
use Telegram;
use Telegram\Bot\Objects\Update;

class TelegramController extends Controller
{
    use InteractsWithContext;

    /**
     * @var Telegram\Bot\Objects\User
     */
    protected $user;

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function process()
    {
        $update = Telegram::bot()->getWebhookUpdate();

        /** Insert to MongoDB */
        TelegramWebhook::add($update);

        //return response('', 200);
        switch ($update->detectType()) {
            case 'message':
                $this->handlerMessage($update);
                break;
            case 'callback_query':
                $this->handlerCallbackQuery($update);
                break;
        }

        return response('', 200);
    }

    /**
     * @param Update $update
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|null|\Symfony\Component\HttpFoundation\Response
     * @throws Telegram\Bot\Exceptions\TelegramSDKException
     */
    private function handlerMessage(Update $update)
    {
        $message = $update->getMessage();
        $this->user = $message->from;
        $this->setLanguage();

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

        // Not found command
        $flow = new StartFlow();
        $flow->setUser($this->user);
        $flow->setUpdate($update);
        $flow->run();

        return response('', 200);
    }

    private function handlerCallbackQuery(Update $update)
    {
        $data = $update->callbackQuery->data;
        $this->user = $update->callbackQuery->from;
        $this->setLanguage();

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
