<?php

namespace App\Http\Conversations\Telegram\Traits;

use App\Http\Conversations\Telegram\Context;

/**
 * Trait HasOptions
 * @package App\Http\Conversations\Traits
 *
 * @method Context context()
 */
trait HasOptions
{
    protected $options = [];

    public function addOption(string $name, string $default = null)
    {
        $this->options[] = [
            'name' => $name,
            'default' => $default
        ];

        return $this;
    }

    protected function getOptions(): array
    {
        return $this->options;
    }

    protected function getOption(string $name)
    {
        $option = collect($this->options)->first(function ($item) use ($name) {
            return $item['name'] == $name;
        });

        $context = $this->context();

        // Get value from context
        if (isset($context->getOptions()[$name])) {
            return $context->getOptions()[$name];
        }

        return $option['default'];
    }
}