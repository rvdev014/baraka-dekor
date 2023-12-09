<?php

namespace App\Services\Telegram\ScenesCore;

enum SceneCbEnum: string
{
    case BACK = 'back';
    case NEXT = 'next';
    case CANCEL = 'cancel';

    public function getText(): string
    {
        return match ($this) {
            self::BACK => '⬅️ Назад',
            self::NEXT => 'Далее ➡️',
            self::CANCEL => '🚫 Отмена',
        };
    }


}
