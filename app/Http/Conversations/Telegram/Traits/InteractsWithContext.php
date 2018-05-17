<?php

namespace App\Http\Conversations\Telegram\Traits;

use App\Http\Conversations\Telegram\Context;
use App\Http\Conversations\Telegram\Flows\Flow;
use Cache;
use Telegram\Bot\Objects\User;

trait InteractsWithContext
{
    /**
     * @var User
     */
    protected $user;

    protected function setContext(Flow $flow, string $state, array $options = [])
    {
        $value = new Context($flow, $state, $options);

        $this->save($value);
    }

    protected function clearContext()
    {
        Cache::forget($this->key());
    }

    protected function remember(string $key, string $value)
    {
        $context = $this->context();
        $context->setOption($key, $value);

        $this->save($context);
    }

    protected function forget(string $key)
    {
        $context = $this->context();
        $context->removeOption($key);

        $this->save($context);
    }

    protected function context(): Context
    {
        return Cache::get($this->key(), new Context());
    }

    private function save(Context $context)
    {
        Cache::forever($this->key(), $context);
    }

    private function key(): string
    {
        return 'context_tg_' . $this->user->id;
    }

    protected function isFlowInContext(Flow $flow): bool
    {
        return get_class($this->context()->getFlow()) == get_class($flow);
    }
}
