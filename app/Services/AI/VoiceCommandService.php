<?php

namespace App\Services\AI;

use App\Enums\VoiceIntentEnum;
use App\Models\Customer;
use App\Models\User;
use App\Models\VoiceCommandLog;
use Illuminate\Support\Facades\Log;
use JsonException;
use RuntimeException;

class VoiceCommandService
{
    public function __construct(
        private readonly OpenRouterService $openRouter,
        private readonly PromptBuilder     $promptBuilder,
    ) {}

    /**
     * Process a voice transcription: call AI, parse intent, enrich with DB lookups, log.
     *
     * @return array{intent: string, confidence: float, human_readable: string|null, data: array, log_id: int}
     *
     * @throws RuntimeException
     */
    public function process(string $transcription, User $user): array
    {
        // Guard: max 500 chars
        $transcription = mb_substr(trim($transcription), 0, 500);

        if (empty($transcription)) {
            throw new RuntimeException("La transcription est vide.");
        }

        $systemPrompt = $this->promptBuilder->buildSystemPrompt($user);
        $userMessage  = $this->promptBuilder->buildUserMessage($transcription);

        // Initialize log early so we can update it on failure
        $log = VoiceCommandLog::create([
            'user_id'       => $user->id,
            'agency_id'     => $user->agency_id,
            'transcription' => $transcription,
            'status'        => 'pending',
        ]);

        try {
            $apiResult = $this->openRouter->sendPrompt($systemPrompt, $userMessage);
            $parsed    = $this->parseJsonResponse($apiResult['content']);
            $intent    = VoiceIntentEnum::tryFrom($parsed['intent'] ?? '') ?? VoiceIntentEnum::Unknown;
            $data      = $parsed['data'] ?? [];

            // Enrich data: resolve customer names to IDs for package operations
            if ($intent === VoiceIntentEnum::CreatePackage) {
                $data = $this->resolveCustomerIds($data, $user);
            }

            $log->update([
                'intent'          => $intent->value,
                'confidence'      => (float) ($parsed['confidence'] ?? 0.0),
                'extracted_data'  => $data,
                'tokens_used'     => $apiResult['tokens_used'],
                'response_time_ms'=> $apiResult['response_time_ms'],
                'model_used'      => config('openrouter.model'),
                'raw_response'    => $apiResult['content'],
                'status'          => 'processed',
            ]);

            return [
                'intent'         => $intent->value,
                'confidence'     => (float) ($parsed['confidence'] ?? 0.0),
                'human_readable' => $parsed['human_readable'] ?? null,
                'data'           => $data,
                'log_id'         => $log->id,
            ];

        } catch (\Throwable $e) {
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('VoiceCommandService::process failed', [
                'transcription' => $transcription,
                'user_id'       => $user->id,
                'error'         => $e->getMessage(),
            ]);

            throw new RuntimeException("Traitement impossible : {$e->getMessage()}", previous: $e);
        }
    }

    /**
     * Mark a voice command log as applied by the user.
     */
    public function markApplied(int $logId): void
    {
        VoiceCommandLog::where('id', $logId)->update(['status' => 'applied']);
    }

    /**
     * Parse and validate the raw JSON string returned by the AI.
     *
     * @throws RuntimeException
     */
    private function parseJsonResponse(string $raw): array
    {
        // Strip markdown code fences if the model accidentally adds them
        $clean = preg_replace('/^```(?:json)?\s*/i', '', trim($raw));
        $clean = preg_replace('/\s*```$/i', '', $clean);

        try {
            $parsed = json_decode(trim($clean), associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Log::warning('AI returned invalid JSON', ['raw' => $raw, 'error' => $e->getMessage()]);
            throw new RuntimeException("Le modèle IA n'a pas retourné un JSON valide.");
        }

        if (!isset($parsed['intent'])) {
            throw new RuntimeException("Le JSON retourné ne contient pas de champ 'intent'.");
        }

        return $parsed;
    }

    /**
     * Try to match sender_name / receiver_name to real customer IDs in the user's agency.
     * Keeps the original name fields alongside for display.
     */
    private function resolveCustomerIds(array $data, User $user): array
    {
        if (!empty($data['sender_name'])) {
            $match = $this->findCustomerByName($data['sender_name'], $user->agency_id);
            $data['sender_id']   = $match?->id;
            $data['sender_match_name'] = $match?->full_name;
        }

        if (!empty($data['receiver_name'])) {
            $match = $this->findCustomerByName($data['receiver_name'], $user->agency_id);
            $data['receiver_id']   = $match?->id;
            $data['receiver_match_name'] = $match?->full_name;
        }

        return $data;
    }

    /**
     * Fuzzy name search within an agency — split words and match all parts.
     */
    private function findCustomerByName(string $name, int $agencyId): ?Customer
    {
        $words = array_filter(explode(' ', trim($name)));

        if (empty($words)) {
            return null;
        }

        $query = Customer::query()->where('agency_id', $agencyId);

        foreach ($words as $word) {
            $like = "%{$word}%";
            $query->where(function ($q) use ($like): void {
                $q->where('first_name', 'like', $like)
                  ->orWhere('last_name', 'like', $like);
            });
        }

        return $query->first();
    }
}
