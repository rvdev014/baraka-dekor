<?php

namespace App\Http\Controllers;

use App\Services\BotService;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function webhook(Request $request, BotService $botService): array
    {
        $botService->run(true);

        return [
            'success' => true
        ];
    }
}
