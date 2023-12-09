<?php

namespace App\Console\Commands;

use App\Services\BotService;
use App\Services\Telegram\TgBotParams;
use Illuminate\Console\Command;

class RunBot extends Command
{
    protected $signature = 'bot:run';
    protected $description = 'Run telegram bot';

    public function handle(BotService $botService): void
    {
        $botService->run();
    }
}
