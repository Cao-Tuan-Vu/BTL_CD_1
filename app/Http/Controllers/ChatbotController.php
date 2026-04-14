<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatbotMessageRequest;
use App\Http\Resources\ChatbotReplyResource;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Throwable;

class ChatbotController extends Controller
{
    public function __construct(protected ChatbotService $chatbotService) {}

    public function message(ChatbotMessageRequest $request): ChatbotReplyResource|JsonResponse
    {
        $validated = $request->validated();

        try {
            $result = $this->chatbotService->generateReply(
                (string) $validated['message'],
                $validated['history'] ?? []
            );
        } catch (Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        }

        return new ChatbotReplyResource($result);
    }
}
