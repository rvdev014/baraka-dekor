<?php

namespace App\Console\Commands;

use App\Services\BotService;
use App\Services\Scenes\RegisterScene;
use App\Services\Telegram\TgBot;
use App\Services\Telegram\TgBotParams;
use Illuminate\Console\Command;

class RunBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run telegram bot';

    /**
     * Execute the console command.
     */
    public function handle(BotService $botService): void
    {
        $botService->run();
    }
}
