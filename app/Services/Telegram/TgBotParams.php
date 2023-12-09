<?php

namespace App\Services\Telegram;

class TgBotParams
{

    private array $cacheParams;

    public function __construct(
        private readonly bool $webhook = false,
        array $cacheParams = [],
    ) {
        $this->cacheParams = array_merge_recursive([
            'host' => config('database.redis.default.host'),
            'port' => config('database.redis.default.port'),
            'password' => config('database.redis.default.password'),
        ], $cacheParams);
    }

    public function isWebhook(): bool
    {
        return $this->webhook;
    }

    public function getCacheParams(): array
    {
        return $this->cacheParams;
    }
}
