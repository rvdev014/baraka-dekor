<?php

namespace App\Services\Telegram\ScenesCore;


use App\Services\Telegram\TgBot;

interface SceneInterface
{
    public function onStart(): void;

    public function onFinish(TgBot $ctx): void;

    public function initSteps(): array;
}
