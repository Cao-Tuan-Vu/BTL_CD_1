<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'system_prompt' => env(
            'OPENAI_SYSTEM_PROMPT',
            'Bạn là trợ lý AI cho cửa hàng nội thất. Hãy trả lời ngắn gọn, rõ ràng và thân thiện.'
        ),
    ],

    'chatbot' => [
        'provider' => env('CHATBOT_PROVIDER', 'openai'),
        'force_vietnamese' => env('CHATBOT_FORCE_VIETNAMESE', true),
        'system_prompt' => env(
            'CHATBOT_SYSTEM_PROMPT',
            'Bạn là trợ lý AI cho cửa hàng nội thất. Hãy trả lời ngắn gọn, rõ ràng, thân thiện và tập trung tư vấn sản phẩm/dịch vụ.'
        ),
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        ],
        'openrouter' => [
            'api_key' => env('OPENROUTER_API_KEY'),
            'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
            'model' => env('OPENROUTER_MODEL', 'google/gemini-2.0-flash-001'),
            'fallback_models' => env('OPENROUTER_FALLBACK_MODELS', 'openrouter/auto'),
            'site_url' => env('OPENROUTER_SITE_URL', ''),
            'app_name' => env('OPENROUTER_APP_NAME', 'Laravel CD1 Chatbot'),
        ],
    ],

];
