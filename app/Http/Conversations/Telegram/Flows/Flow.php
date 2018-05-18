<?php

namespace App\Http\Conversations\Telegram\Flows;

use App\Components\Storage\TelegramStorage;
use App\Http\Conversations\Telegram\Traits\HasOptions;
use App\Http\Conversations\Telegram\Traits\InteractsWithContext;
use App\Http\Conversations\Telegram\Traits\SendMessages;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\User;

abstract class Flow implements FlowInterface
{
    use InteractsWithContext,
        HasOptions,
        SendMessages,
        TelegramStorage;

    /** @var User */
    protected $user;

    /** @var Update */
    protected $update;

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param Update $update
     */
    public function setUpdate(Update $update)
    {
        $this->update = $update;
    }

    /**
     * @param string $flow
     */
    public function startFlow(string $flow)
    {
        /**
         * @var Flow
         */
        $flow = app($flow);
        $flow->setUser($this->user);
        $flow->setUpdate($this->update);
        $flow->run();
    }

    /**
     * @param $flow
     * @param string $state
     * @param array $options
     * @return string
     */
    public function callbackData($flow, string $state, array $options = [])
    {
        $shortNameClass = '';

        if (is_object($flow)) {
            $nameClass = get_class($flow);
            foreach (config('flows.telegram') as $key => $item) {
                if ($item == $nameClass) {
                    $shortNameClass = $key;
                }
            }
        } elseif (isset(config('flows.telegram')[$flow])) {
            $shortNameClass = $flow;
        } else {
            foreach (config('flows.telegram') as $key => $item) {
                if ($item == mb_substr($flow, 0)) {
                    $shortNameClass = $key;
                }
            }
        }

        $optionsText = '';
        foreach ($options as $key => $value) {
            $optionsText .= "@$key:$value";
        }

        return "{$shortNameClass}@{$state}{$optionsText}";
    }
}
