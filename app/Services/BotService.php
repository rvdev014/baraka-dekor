<?php

namespace App\Services;

use App\Models\User;
use App\Services\Scenes\RegisterScene;
use App\Services\Telegram\TgBot;
use App\Services\Telegram\TgBotParams;

class BotService
{
    protected const REGISTER_SCENE = 'registerScene';

    public function run(): void
    {
        try {
            $telegram = new TgBot(config('app.telegram.token'), new TgBotParams());
            $telegram->registerScene(self::REGISTER_SCENE, RegisterScene::class);

            $telegram->onCommand('start', function (TgBot $ctx) {
                $chatId = $ctx->getFromId();
                $existUser = User::where('chat_id', $chatId)->first();
                if ($existUser) {
                    $ctx->answer('Приветственное сообщение для ' . $existUser->getFilamentName());
                    return;
                }

                $ctx->startScene(self::REGISTER_SCENE);
            });

            $telegram->onCommand('help', function (TgBot $ctx) {
                $ctx->answer('Для подробной информации, пишите @ravshan014');
            });

            $telegram->onAnyCallbackQuery(function (TgBot $ctx) {
                $ctx->answerCbQuery(['text' => '👌']);
            });

            $telegram->launch();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
