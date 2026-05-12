<?php

return [
    'api_key'  => env('OPENROUTER_API_KEY', ''),
    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
    'model'    => env('OPENROUTER_MODEL', 'openai/gpt-4o-mini'),
    'timeout'  => (int) env('OPENROUTER_TIMEOUT', 30),
    'retries'  => (int) env('OPENROUTER_RETRIES', 2),
    'retry_delay_ms' => (int) env('OPENROUTER_RETRY_DELAY_MS', 1000),

    // Max tokens for the AI response (JSON only, keep it tight)
    'max_tokens'  => 600,
    'temperature' => 0.15,

    // HTTP referer sent to OpenRouter (optional but recommended)
    'http_referer' => env('APP_URL', 'http://localhost'),
    'app_title'    => env('APP_NAME', 'GP Manager'),
];
