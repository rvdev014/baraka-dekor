<?php

namespace App\Services\Telegram;

use App\Services\Telegram\ScenesCore\BaseScene;
use Exception;

trait TelegramTrait
{

    public array $message = [];

    public function getMessageType(string $type): ?string
    {
        return $this->message[$type] ?? null;
    }

    public function isMessageType(string $type): bool
    {
        return $this->getMessageType($type) !== null;
    }

    /**
     * @throws Exception
     */
    public function isScene(string $scene): bool
    {
        $sceneName = BaseScene::getSceneKeyStatic($scene, $this->getFromId());
        TgHelper::console('$sceneName: ' . $sceneName);
        return $this->getCache()->exists($sceneName);
    }

    public function isCbEquals(string $cb): bool
    {
        return $this->getCallbackQuery() === $cb;
    }


    public function isCommandEquals(string $command): bool
    {
        return $this->getCommand() === $command;
    }


    public function isCommand(): bool
    {
        $text = TgHelper::get($this->input, 'message.text');
        return self::isCommandStatic($text);
    }

    public static function isCommandStatic($text): bool
    {
        return TgHelper::get($text, 0) === '/';
    }

    public function isPrivateChat(): bool
    {
        $cbChat = TgHelper::get($this->input, 'callback_query.message.chat');
        $chat = TgHelper::get($this->input, 'message.chat');
        $chat = $cbChat ?: $chat;
        return self::isPrivateChatStatic($chat);
    }

    public static function isPrivateChatStatic($chat): bool
    {
        return TgHelper::get($chat, 'type') === 'private';
    }


    public function getPhoto(): ?array
    {
        return TgHelper::get($this->input, 'message.photo');
    }

    public function getPhotoId(): ?string
    {
        $photo = $this->getPhoto();
        if (empty($photo)) {
            return null;
        }
        return TgHelper::get(end($photo), 'file_id');
    }


    public function getCallbackQuery(): ?string
    {
        return TgHelper::get($this->input, 'callback_query.data');
    }


    // get command name
    public function getCommand(): ?string
    {
        $text = TgHelper::get($this->input, 'message.text');
        if (empty($text)) {
            return null;
        }
        return self::getCommandStatic($text);
    }

    public static function getCommandStatic($text): ?string
    {
        $text = trim($text);
        $text = explode(' ', $text);
        $text = $text[0];
        return str_replace('/', '', $text);
    }


    public function getFromId(): int
    {
        $cbFrom = TgHelper::get($this->input, 'callback_query.from');
        $editedFrom = TgHelper::get($this->input, 'edited_message.from');
        $messageFrom = TgHelper::get($this->message, 'from');

        $fromArr = $cbFrom ?: $editedFrom ?: $messageFrom;
        return TgHelper::get($fromArr, 'id');
    }


    public function isCallbackQuery(): bool
    {
        return TgHelper::get($this->input, 'callback_query') !== null;
    }


    public function getMessageObject(): array
    {
        if (TgHelper::get($this->input, 'message') !== null) {
            return TgHelper::get($this->input, 'message');
        }

        if (TgHelper::get($this->input, 'callback_query.message') !== null) {
            return TgHelper::get($this->input, 'callback_query.message');
        }

        return [];
    }

    public function isText(): bool
    {
        return TgHelper::get($this->message, 'text') !== null;
    }

    public function isEmptyText(): bool
    {
        return empty($this->getText()) || $this->isCallbackQuery();
    }

    public function getText(): ?string
    {
        return TgHelper::get($this->message, 'text');
    }

    public function getContact(): ?array
    {
        return TgHelper::get($this->message, 'contact');
    }

    public function getFirstName(): ?string
    {
        return TgHelper::get($this->message, 'from.first_name');
    }

    public function getLastName(): ?string
    {
        return TgHelper::get($this->message, 'from.last_name');
    }

    public function getUserName(): ?string
    {
        return TgHelper::get($this->message, 'from.username');
    }

    public function getLanguageCode(): ?string
    {
        return TgHelper::get($this->message, 'from.language_code');
    }

    public function getCommandArgs(): array
    {
        return explode(' ', $this->getCommandArgString());
    }

    public function getCommandArg(int $index): ?string
    {
        return TgHelper::get($this->getCommandArgs(), $index);
    }

    public function getCommandArgCount(): int
    {
        return count($this->getCommandArgs());
    }

    public function getCommandArgString(): ?string
    {
        return substr($this->getCommand(), 1);
    }

    public function isMessage(): bool
    {
        return TgHelper::get($this->input, 'message') !== null;
    }


}
