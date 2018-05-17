<?php

namespace App\Http\Conversations\Telegram;

use App\Http\Conversations\Telegram\Flows\Flow;

class Context
{
    public $flow;
    protected $state;
    protected $options;

    public function __construct(
        Flow $flow = null,
        string $state = null,
        array $options = []
    ) {

        $this->flow = !is_null($flow) ? get_class($flow) : null;
        $this->state = $state;
        $this->options = $options;
    }

    public function hasFlow(): bool
    {
        return !is_null($this->flow);
    }

    /**
     * @return \App\Http\Conversations\Telegram\Flows\Flow|null
     */
    public function getFlow()
    {
        return $this->hasFlow() ? app($this->flow) : null;
    }

    public function setFlow(Flow $flow)
    {
        $this->flow = get_class($flow);
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState(string $state)
    {
        $this->state = $state;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function setOption(string $key, string $value)
    {
        $this->options[$key] = $value;
    }

    public function removeOption(string $key)
    {
        if (array_key_exists($key, $this->options)) {
            unset($this->options[$key]);
        }
    }
}
