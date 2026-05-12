<?php

namespace App\Services\AI;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpenRouterService
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl,
        private readonly string $model,
        private readonly int    $timeout,
        private readonly int    $retries,
        private readonly int    $retryDelayMs,
    ) {}

    /**
     * Send a chat completion request to OpenRouter.
     *
     * @param  string  $systemPrompt  System message (role: system)
     * @param  string  $userMessage   User message (role: user)
     * @return array{content: string, tokens_used: int, response_time_ms: int}
     *
     * @throws RuntimeException on API errors
     */
    public function sendPrompt(string $systemPrompt, string $userMessage): array
    {
        $startTime = hrtime(true);

        $payload = [
            'model'       => $this->model,
            'messages'    => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userMessage],
            ],
            'temperature' => config('openrouter.temperature', 0.15),
            'max_tokens'  => config('openrouter.max_tokens', 600),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization'  => "Bearer {$this->apiKey}",
                'Content-Type'   => 'application/json',
                'HTTP-Referer'   => config('openrouter.http_referer'),
                'X-Title'        => config('openrouter.app_title'),
            ])
            ->timeout($this->timeout)
            ->retry($this->retries, $this->retryDelayMs, fn (\Exception $e) => $e instanceof ConnectionException)
            ->post("{$this->baseUrl}/chat/completions", $payload)
            ->throw();

            $body = $response->json();

        } catch (RequestException $e) {
            $status = $e->response->status();
            $detail = $e->response->json('error.message') ?? 'Erreur inconnue';

            Log::error('OpenRouter API error', [
                'status'  => $status,
                'detail'  => $detail,
                'model'   => $this->model,
            ]);

            throw new RuntimeException("OpenRouter ({$status}): {$detail}");

        } catch (ConnectionException $e) {
            Log::error('OpenRouter connection failed', ['message' => $e->getMessage()]);
            throw new RuntimeException("Impossible de joindre OpenRouter : {$e->getMessage()}");
        }

        $content = data_get($body, 'choices.0.message.content', '');
        $tokensUsed = data_get($body, 'usage.total_tokens', 0);
        $responseTimeMs = (int) round((hrtime(true) - $startTime) / 1_000_000);

        if (empty($content)) {
            throw new RuntimeException("OpenRouter a retourné une réponse vide.");
        }

        return [
            'content'         => $content,
            'tokens_used'     => (int) $tokensUsed,
            'response_time_ms' => $responseTimeMs,
        ];
    }
}
