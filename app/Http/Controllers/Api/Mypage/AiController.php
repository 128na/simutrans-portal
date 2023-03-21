<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mypage;

use App\Http\Controllers\Controller;
use App\Services\OpenAi\ChatGptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(private ChatGptService $chatGptService)
    {
    }

    public function description(Request $request): JsonResponse
    {
        $text = $request->string('text', '')->toString();

        return response()->json([
            'description' => $this->chatGptService->getDescription($text),
        ]);
    }
}
