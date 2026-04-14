<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

class ChatbotService
{
    /**
     * @param  array<int, array{role:string, content:string}>  $history
     * @return array{reply:string, model:string|null, provider:string}
     */
    public function generateReply(string $message, array $history = []): array
    {
        $providerConfig = $this->resolveProviderConfig();
        $provider = $providerConfig['provider'];
        $providerLabel = $providerConfig['label'];
        $apiKey = $providerConfig['api_key'];
        $baseUrl = $providerConfig['base_url'];
        $model = $providerConfig['model'];
        $systemPrompt = $providerConfig['system_prompt'];
        $headers = $providerConfig['headers'];
        $credentialEnv = $providerConfig['credential_env'];
        $fallbackModels = $providerConfig['fallback_models'];
        $forceVietnamese = $providerConfig['force_vietnamese'];

        $languageConstraint = $forceVietnamese
            ? 'YEU CAU BAT BUOC: Chi tra loi bang tieng Viet. Khong tra loi bang tieng Anh hoac ngon ngu khac. Neu nguoi dung viet tieng khac, ban van phai tra loi bang tieng Viet.'
            : '';

        $combinedSystemPrompt = $systemPrompt;
        if ($languageConstraint !== '') {
            $combinedSystemPrompt .= "\n\n".$languageConstraint;
        }

        if ($apiKey === '') {
            return $this->fallbackResponse(
                $message,
                'Chưa cấu hình '.$credentialEnv.'. Chatbot đang chạy ở chế độ dự phòng.'
            );
        }

        $messages = [
            [
                'role' => 'system',
                'content' => $combinedSystemPrompt,
            ],
        ];

        foreach ($history as $item) {
            if (! in_array($item['role'], ['user', 'assistant'], true)) {
                continue;
            }

            $messages[] = [
                'role' => $item['role'],
                'content' => $item['content'],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        try {
            [$response, $model] = $this->requestChatCompletion(
                $provider,
                $apiKey,
                $baseUrl,
                $headers,
                $model,
                $fallbackModels,
                $messages
            );
        } catch (Throwable $exception) {
            return $this->fallbackResponse(
                $message,
                'Không thể kết nối tới dịch vụ '.$providerLabel.'. Hệ thống đã chuyển sang chế độ dự phòng.'
            );
        }

        if (! $response->successful()) {
            return $this->fallbackResponse(
                $message,
                $this->buildProviderErrorMessage($provider, $response->status(), (array) $response->json())
            );
        }

        $reply = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($reply) || trim($reply) === '') {
            return $this->fallbackResponse(
                $message,
                'AI không trả về nội dung hợp lệ. Hệ thống đã chuyển sang chế độ dự phòng.'
            );
        }

        $reply = trim($reply);

        if ($forceVietnamese && ! $this->isVietnameseReply($reply)) {
            $rewritePrompt = [
                [
                    'role' => 'system',
                    'content' => 'Ban la bien tap vien. Nhiem vu cua ban la viet lai noi dung thanh tieng Viet ro rang, giu nguyen y nghia.',
                ],
                [
                    'role' => 'user',
                    'content' => 'Hay viet lai noi dung sau bang tieng Viet, ngan gon va de hieu: '.$reply,
                ],
            ];

            try {
                [$rewriteResponse, $model] = $this->requestChatCompletion(
                    $provider,
                    $apiKey,
                    $baseUrl,
                    $headers,
                    $model,
                    $fallbackModels,
                    $rewritePrompt
                );

                if ($rewriteResponse->successful()) {
                    $rewrittenReply = data_get($rewriteResponse->json(), 'choices.0.message.content');

                    if (is_string($rewrittenReply) && trim($rewrittenReply) !== '') {
                        $reply = trim($rewrittenReply);
                    }
                }
            } catch (Throwable $exception) {
                // Keep the original reply and continue to strict guard below.
            }

            if (! $this->isVietnameseReply($reply)) {
                $reply = 'Xin lỗi, mình sẽ trả lời lại bằng tiếng Việt. Bạn vui lòng gửi lại yêu cầu để mình hỗ trợ chính xác hơn.';
            }
        }

        return [
            'reply' => $reply,
            'model' => $model,
            'provider' => $provider,
        ];
    }

    /**
     * @param  array<int, string>  $fallbackModels
     * @param  array<int, array{role:string, content:string}>  $messages
     * @return array{0:Response, 1:string}
     */
    private function requestChatCompletion(
        string $provider,
        string $apiKey,
        string $baseUrl,
        array $headers,
        string $model,
        array $fallbackModels,
        array $messages
    ): array {
        $request = Http::timeout(30)
            ->withToken($apiKey)
            ->acceptJson();

        if (! empty($headers)) {
            $request = $request->withHeaders($headers);
        }

        $response = $request->post($baseUrl.'/chat/completions', [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);

        if ($provider === 'openrouter' && $this->shouldRetryOpenRouterModel($response->status(), (array) $response->json())) {
            foreach ($fallbackModels as $fallbackModel) {
                if ($fallbackModel === $model) {
                    continue;
                }

                $retryResponse = $request->post($baseUrl.'/chat/completions', [
                    'model' => $fallbackModel,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ]);

                if ($retryResponse->successful()) {
                    return [$retryResponse, $fallbackModel];
                }

                $response = $retryResponse;
            }
        }

        return [$response, $model];
    }

    /**
     * @return array{reply:string, model:string|null, provider:string}
     */
    private function fallbackResponse(string $message, string $reason): array
    {
        return [
            'reply' => $this->buildFallbackReply($message, $reason),
            'model' => null,
            'provider' => 'fallback',
        ];
    }

    private function buildProviderErrorMessage(string $provider, int $status, array $body): string
    {
        $errorCode = (string) data_get($body, 'error.code', '');
        $providerLabel = $this->providerLabel($provider);

        if ($status === 401) {
            return 'API key '.$providerLabel.' không hợp lệ hoặc đã hết hiệu lực.';
        }

        if ($status === 429 || $errorCode === 'insufficient_quota') {
            return 'Tài khoản '.$providerLabel.' đã hết quota hoặc vượt giới hạn sử dụng.';
        }

        return 'Dịch vụ '.$providerLabel.' tạm thời không khả dụng (HTTP '.$status.').';
    }

    private function buildFallbackReply(string $message, string $reason): string
    {
        $normalized = mb_strtolower(trim($message));

        $advice = 'Bạn có thể nêu rõ ngân sách, kích thước phòng và phong cách mong muốn (hiện đại, tối giản, Bắc Âu...) để mình gợi ý chính xác hơn.';

        if (str_contains($normalized, 'sofa')) {
            $advice = 'Với sofa, bạn nên ưu tiên chiều dài vừa phòng, chất liệu dễ vệ sinh và màu trung tính như be/xam để phối nội thất linh hoạt.';
        } elseif (str_contains($normalized, 'ban')) {
            $advice = 'Khi chọn bàn, hãy cân đối diện tích sử dụng, chiều cao tiêu chuẩn và chất liệu mặt bàn phù hợp nhu cầu hàng ngày.';
        } elseif (str_contains($normalized, 'giuong')) {
            $advice = 'Với giường ngủ, nên chọn khung chắc chắn, kích thước đúng không gian và nệm phù hợp thói quen nằm để ngủ ngon hơn.';
        }

        return $reason.'\n\nGợi ý nhanh: '.$advice;
    }

    private function shouldRetryOpenRouterModel(int $status, array $body): bool
    {
        if ($status !== 404) {
            return false;
        }

        $errorMessage = mb_strtolower((string) data_get($body, 'error.message', ''));

        return str_contains($errorMessage, 'no endpoints found');
    }

    private function isVietnameseReply(string $text): bool
    {
        $normalized = mb_strtolower($text);

        if (preg_match('/[ăâđêôơưáàảãạấầẩẫậắằẳẵặéèẻẽẹếềểễệíìỉĩịóòỏõọốồổỗộớờởỡợúùủũụứừửữựýỳỷỹỵ]/u', $normalized) === 1) {
            return true;
        }

        $markers = [
            'ban',
            'cua',
            'khong',
            'toi',
            'san pham',
            'noi that',
            'goi y',
            'phong',
            'gia',
        ];

        $score = 0;
        foreach ($markers as $marker) {
            if (str_contains($normalized, $marker)) {
                $score++;
            }
        }

        return $score >= 2;
    }

    /**
     * @return array{
     *     provider:string,
     *     label:string,
     *     api_key:string,
     *     base_url:string,
     *     model:string,
     *     system_prompt:string,
     *     headers:array<string, string>,
    *     credential_env:string,
     *     fallback_models:array<int, string>,
     *     force_vietnamese:bool
     * }
     */
    private function resolveProviderConfig(): array
    {
        $provider = mb_strtolower(trim((string) config('services.chatbot.provider', 'openai')));
        $systemPrompt = (string) config(
            'services.chatbot.system_prompt',
            'Bạn là trợ lý AI cho cửa hàng nội thất. Hãy trả lời ngắn gọn, rõ ràng, thân thiện và tập trung tư vấn sản phẩm/dịch vụ.'
        );
        $forceVietnamese = (bool) config('services.chatbot.force_vietnamese', true);

        if ($provider === 'openrouter') {
            $siteUrl = trim((string) config('services.chatbot.openrouter.site_url', ''));
            $appName = trim((string) config('services.chatbot.openrouter.app_name', ''));

            $headers = [];

            if ($siteUrl !== '') {
                $headers['HTTP-Referer'] = $siteUrl;
            }

            if ($appName !== '') {
                $headers['X-Title'] = $appName;
            }

            $rawFallbackModels = (string) config('services.chatbot.openrouter.fallback_models', 'openrouter/auto');
            $fallbackModels = collect(explode(',', $rawFallbackModels))
                ->map(fn ($modelName) => trim((string) $modelName))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($fallbackModels)) {
                $fallbackModels = ['openrouter/auto'];
            }

            return [
                'provider' => 'openrouter',
                'label' => 'OpenRouter',
                'api_key' => trim((string) config('services.chatbot.openrouter.api_key', '')),
                'base_url' => rtrim((string) config('services.chatbot.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/'),
                'model' => (string) config('services.chatbot.openrouter.model', 'google/gemini-2.0-flash-001'),
                'system_prompt' => $systemPrompt,
                'headers' => $headers,
                'credential_env' => 'OPENROUTER_API_KEY',
                'fallback_models' => $fallbackModels,
                'force_vietnamese' => $forceVietnamese,
            ];
        }

        return [
            'provider' => 'openai',
            'label' => 'OpenAI',
            'api_key' => trim((string) config('services.chatbot.openai.api_key', '')),
            'base_url' => rtrim((string) config('services.chatbot.openai.base_url', 'https://api.openai.com/v1'), '/'),
            'model' => (string) config('services.chatbot.openai.model', 'gpt-4o-mini'),
            'system_prompt' => $systemPrompt,
            'headers' => [],
            'credential_env' => 'OPENAI_API_KEY',
            'fallback_models' => [],
            'force_vietnamese' => $forceVietnamese,
        ];
    }

    private function providerLabel(string $provider): string
    {
        return $provider === 'openrouter' ? 'OpenRouter' : 'OpenAI';
    }
}
