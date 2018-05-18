<?php

namespace App\Components\Storage;

use Cache;

class Storage
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function save(array $data)
    {
        $cache = Cache::get($this->getKey()) ?: [];
        Cache::forever($this->getKey(), array_merge($cache, $data));
    }

    public function get(string $name)
    {
        $cache = Cache::get($this->getKey());
        return collect($cache)->first(function ($item, $key) use ($name) {
            return $key == $name ? $item : false;
        });
    }

    private function getKey(): string
    {
        return 'storage:telegram:' . $this->id;
    }
}
