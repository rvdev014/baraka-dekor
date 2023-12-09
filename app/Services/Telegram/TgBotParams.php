<?php

namespace App\Services\Telegram;

class TgBotParams
{

    public function __construct(
        private readonly bool $webhook = false,
        private readonly array $cacheParams = [],
    ) {
    }

    public static function make(bool $webhook, array $cacheParams = []): self
    {
        return new self($webhook, $cacheParams);
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
