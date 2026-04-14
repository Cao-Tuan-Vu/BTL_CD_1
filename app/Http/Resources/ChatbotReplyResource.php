<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin array<string, mixed>
 */
class ChatbotReplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'reply' => (string) ($this['reply'] ?? ''),
            'model' => $this['model'] ?? null,
            'provider' => $this['provider'] ?? 'openai',
            'created_at' => now()->toIso8601String(),
        ];
    }
}
