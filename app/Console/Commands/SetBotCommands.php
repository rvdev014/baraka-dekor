<?php

namespace App\Console\Commands;

use App\Services\BotService;
use Illuminate\Console\Command;

class SetBotCommands extends Command
{
    protected $signature = 'bot:commands';
    protected $description = 'Set bot commands';

    public function handle(BotService $botService): void
    {
        $botService->setCommands();
        $this->info('Bot commands was set');
    }
}
