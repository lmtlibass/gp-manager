<?php

namespace App\Livewire\VoiceCommand;

use App\Enums\VoiceIntentEnum;
use App\Models\VoiceCommandLog;
use App\Services\AI\OpenRouterService;
use App\Services\AI\PromptBuilder;
use App\Services\AI\VoiceCommandService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Assistant extends Component
{
    public bool    $isOpen      = false;
    public bool    $isProcessing = false;
    public ?string $transcription = null;
    public ?string $error        = null;
    public bool    $applied      = false;

    // Result stored as plain array for Livewire JSON serialisation
    public ?array $commandResult = null;

    public function toggle(): void
    {
        $this->isOpen = ! $this->isOpen;
        $this->reset(['transcription', 'commandResult', 'error', 'applied']);
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->reset(['transcription', 'commandResult', 'error', 'applied']);
    }

    /**
     * Called from Alpine.js after the Web Speech API produces a final transcript.
     */
    public function process(string $transcription): void
    {
        $this->error         = null;
        $this->commandResult = null;
        $this->applied       = false;
        $this->transcription = mb_substr(trim($transcription), 0, 500);
        $this->isProcessing  = true;

        if (empty($this->transcription)) {
            $this->error       = "Aucune parole détectée. Veuillez réessayer.";
            $this->isProcessing = false;
            return;
        }

        try {
            $service = $this->makeService();
            $this->commandResult = $service->process($this->transcription, Auth::user());
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->isProcessing = false;
        }
    }

    /**
     * Apply the result: store in session then navigate to the target page.
     * Same-page dispatch is handled client-side via the 'voice-command-applied' event.
     */
    public function apply(): void
    {
        if (! $this->commandResult) {
            return;
        }

        $intent = VoiceIntentEnum::tryFrom($this->commandResult['intent'] ?? '') ?? VoiceIntentEnum::Unknown;

        if ($intent === VoiceIntentEnum::Unknown) {
            $this->error = "Impossible d'appliquer : intention non reconnue.";
            return;
        }

        // Mark as applied in log
        if (! empty($this->commandResult['log_id'])) {
            $this->makeService()->markApplied($this->commandResult['log_id']);
        }

        // Store enriched data in session keyed by intent
        session()->put($intent->sessionKey(), $this->commandResult['data']);

        $this->applied = true;

        // Dispatch browser event so same-page forms can react immediately
        $this->dispatch('voice-command-applied',
            intent: $intent->value,
            data: $this->commandResult['data'],
        );

        // Navigate to the target route after a short delay (let browser event fire first)
        if ($route = $intent->targetRoute()) {
            $this->redirectRoute($route, navigate: true);
        }
    }

    /**
     * Retry with the same transcription.
     */
    public function retry(): void
    {
        if ($this->transcription) {
            $this->process($this->transcription);
        }
    }

    #[Computed]
    public function recentLogs(): \Illuminate\Database\Eloquent\Collection
    {
        return VoiceCommandLog::query()
            ->where('user_id', Auth::id())
            ->whereIn('status', ['processed', 'applied'])
            ->latest()
            ->limit(5)
            ->get(['id', 'transcription', 'intent', 'confidence', 'status', 'created_at']);
    }

    public function render()
    {
        return view('livewire.voice-command.assistant');
    }

    private function makeService(): VoiceCommandService
    {
        $cfg = config('openrouter');

        return new VoiceCommandService(
            openRouter: new OpenRouterService(
                apiKey:       $cfg['api_key'],
                baseUrl:      $cfg['base_url'],
                model:        $cfg['model'],
                timeout:      $cfg['timeout'],
                retries:      $cfg['retries'],
                retryDelayMs: $cfg['retry_delay_ms'],
            ),
            promptBuilder: new PromptBuilder(),
        );
    }
}
