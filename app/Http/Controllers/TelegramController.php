<?php

namespace App\Http\Controllers;

use App\Services\BotService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TelegramController extends Controller
{
    public function webhook(Request $request, BotService $botService): void
    {
        $botService->run(true);
    }
}
