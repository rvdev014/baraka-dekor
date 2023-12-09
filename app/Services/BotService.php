<?php

namespace App\Services;

use App\Models\User;
use App\Services\Scenes\PurchaseScene;
use App\Services\Scenes\RegisterScene;
use App\Services\Telegram\TgBot;
use App\Services\Telegram\TgBotParams;
use Exception;


class BotService
{
    public const REGISTER_SCENE = 'registerScene';
    public const PURCHASE_SCENE = 'purchaseScene';

    public const START_COMMAND = 'start';
    public const PURCHASE_COMMAND = 'purchase';
    public const HELP_COMMAND = 'help';
    public const COMMANDS = [
        self::START_COMMAND => 'Запустить бота',
        self::PURCHASE_COMMAND => 'Добавить покупку',
        self::HELP_COMMAND => 'Помощь',
    ];

    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = config('app.telegram.base_url');
        $this->token = config('app.telegram.token');
    }

    public function run(bool $webhook = false): void
    {
        try {
            $options = TgBotParams::make($webhook, [
                'host' => config('database.redis.default.host'),
                'port' => config('database.redis.default.port'),
                'password' => config('database.redis.default.password'),
            ]);

            $telegram = new TgBot(config('app.telegram.token'), $options);

            $telegram->registerScene(self::REGISTER_SCENE, RegisterScene::class);
            $telegram->registerScene(self::PURCHASE_SCENE, PurchaseScene::class);

            $telegram->onCommand(self::START_COMMAND, function (TgBot $ctx) {
                $chatId = $ctx->getFromId();
                $existUser = User::where('chat_id', $chatId)->first();
                if ($existUser) {
                    $ctx->answer('Приветственное сообщение для ' . $existUser->getFilamentName());
                    return;
                }

                $ctx->startScene(self::REGISTER_SCENE);
            });

            $telegram->use(function (TgBot $ctx, $next) {
                if (!User::where('chat_id', $ctx->getFromId())->exists()) {
                    $ctx->answer('Сначала зарегистрируйтесь!');
                    return;
                }
                $next($ctx);
            });

            $telegram->onCommand(self::PURCHASE_COMMAND, function (TgBot $ctx) {
                $ctx->startScene(self::PURCHASE_SCENE);
            });

            $telegram->onCommand(self::HELP_COMMAND, function (TgBot $ctx) {
                $ctx->answer('Для подробной информации, пишите @ravshan014');
            });

            $telegram->onAnyCallbackQuery(fn(TgBot $ctx) => $ctx->answerCbQuery(['text' => '👌']));

            $telegram->launch();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function apiRequest(string $method, array $params = []): array
    {
        return TgBot::sendRequestStatic("$this->baseUrl/bot$this->token/$method", $params);
    }

    public function setCommands(): void
    {
        $commands = [];
        foreach (BotService::COMMANDS as $command => $description) {
            $commands[] = ['command' => $command, 'description' => $description,];
        }
        $this->apiRequest('setMyCommands', ['commands' => $commands]);
    }
}
