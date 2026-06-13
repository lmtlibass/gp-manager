{{--
    Voice Command Assistant — floating widget
    Web Speech API is handled by Alpine.js (no server round-trip for recording).
    Transcription is sent to Livewire::process() once speech ends.
--}}
<div
    x-data="{
        isListening: false,
        interimText: '',
        finalText: '',
        recognition: null,
        hasSupport: ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window),

        initRecognition() {
            const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SR) return;

            this.recognition = new SR();
            this.recognition.lang = 'fr-FR';
            this.recognition.continuous = false;
            this.recognition.interimResults = true;

            this.recognition.onresult = (event) => {
                let interim = '';
                for (let i = event.resultIndex; i < event.results.length; i++) {
                    if (event.results[i].isFinal) {
                        this.finalText += event.results[i][0].transcript;
                    } else {
                        interim += event.results[i][0].transcript;
                    }
                }
                this.interimText = interim;
            };

            this.recognition.onend = () => {
                this.isListening = false;
                this.interimText = '';
                const text = this.finalText.trim();
                if (text) {
                    $wire.process(text);
                }
            };

            this.recognition.onerror = (event) => {
                this.isListening = false;
                if (event.error !== 'no-speech') {
                    console.warn('Voice recognition error:', event.error);
                }
            };
        },

        startListening() {
            if (!this.recognition) this.initRecognition();
            if (!this.recognition) return;
            this.finalText = '';
            this.interimText = '';
            this.isListening = true;
            this.recognition.start();
        },

        stopListening() {
            this.recognition?.stop();
            this.isListening = false;
        },

        toggleMic() {
            this.isListening ? this.stopListening() : this.startListening();
        },

        get displayText() {
            return this.finalText + this.interimText;
        }
    }"
    x-init="initRecognition()"
    class="fixed bottom-24 right-4 z-50 lg:bottom-6 lg:right-6"
>

    {{-- ── FAB button ──────────────────────────────────── --}}
    <button
        @click="$wire.toggle()"
        type="button"
        title="Assistant vocal IA"
        class="group relative flex h-14 w-14 items-center justify-center rounded-2xl shadow-2xl transition-all duration-200 active:scale-95
               {{ $isOpen ? 'bg-rose-600 hover:bg-rose-700' : 'bg-primary hover:bg-[#002d5a]' }}">

        {{-- Pulse ring when open --}}
        @if($isOpen)
        <span class="absolute inset-0 animate-ping rounded-2xl bg-rose-500/40"></span>
        @endif

        <i class="fa-solid {{ $isOpen ? 'fa-xmark' : 'fa-microphone' }} text-xl text-white relative z-10"></i>

        {{-- Tooltip --}}
        <span class="absolute right-16 whitespace-nowrap rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
            {{ $isOpen ? 'Fermer l\'assistant' : 'Assistant vocal IA' }}
        </span>
    </button>

    {{-- ── Panel ───────────────────────────────────────── --}}
    @if($isOpen)
    <div
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        class="absolute bottom-16 right-0 mb-2 w-80 sm:w-96 rounded-3xl border border-slate-200/60 bg-white shadow-2xl overflow-hidden dark:bg-slate-800 dark:border-slate-700/60"
    >
        {{-- Header --}}
        <div class="flex items-center gap-3 bg-primary px-5 py-4">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-accent">
                <i class="fa-solid fa-wand-magic-sparkles text-sm text-primary"></i>
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white">Assistant vocal IA</p>
                <p class="text-xs text-slate-400">Parlez, je comprends le français</p>
            </div>
            <button wire:click="close" type="button"
                class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 hover:bg-white/10 hover:text-white transition">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <div class="p-5 space-y-4">

            {{-- Browser support warning --}}
            <template x-if="!hasSupport">
                <div class="flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5 shrink-0 text-amber-500"></i>
                    <span>Votre navigateur ne supporte pas la reconnaissance vocale. Utilisez Chrome ou Edge.</span>
                </div>
            </template>

            {{-- ── Microphone zone ── --}}
            <template x-if="hasSupport">
                <div class="flex flex-col items-center gap-4">

                    {{-- Mic button --}}
                    <button
                        @click="toggleMic()"
                        type="button"
                        :disabled="{{ $isProcessing ? 'true' : 'false' }}"
                        :class="isListening
                            ? 'bg-rose-500 hover:bg-rose-600 ring-4 ring-rose-300 scale-110'
                            : '{{ $isProcessing ? 'bg-slate-200 cursor-not-allowed' : 'bg-primary hover:bg-[#002d5a]' }}'"
                        class="relative flex h-20 w-20 items-center justify-center rounded-full shadow-lg transition-all duration-200 focus:outline-none">

                        {{-- Listening pulse rings --}}
                        <template x-if="isListening">
                            <span class="absolute inset-0">
                                <span class="absolute inset-0 animate-ping rounded-full bg-rose-400/50"></span>
                                <span class="absolute inset-2 animate-ping animation-delay-150 rounded-full bg-rose-400/30"></span>
                            </span>
                        </template>

                        @if($isProcessing)
                            <i class="fa-solid fa-circle-notch fa-spin text-2xl text-slate-500"></i>
                        @else
                            <i class="fa-solid fa-microphone text-2xl text-white relative z-10"></i>
                        @endif
                    </button>

                    {{-- Status label --}}
                    <p class="text-sm font-medium text-center text-slate-600">
                        <template x-if="isListening">
                            <span class="text-rose-600">
                                <i class="fa-solid fa-circle-dot fa-beat text-xs mr-1.5"></i>Écoute en cours…
                            </span>
                        </template>
                        <template x-if="!isListening && {{ $isProcessing ? 'true' : 'false' }}">
                            <span class="text-secondary">
                                <i class="fa-solid fa-circle-notch fa-spin text-xs mr-1.5"></i>Traitement IA en cours…
                            </span>
                        </template>
                        <template x-if="!isListening && !{{ $isProcessing ? 'true' : 'false' }}">
                            <span>Appuyez pour parler</span>
                        </template>
                    </p>

                    {{-- Live transcript box --}}
                    <template x-if="displayText">
                        <div class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 leading-relaxed min-h-[3rem]">
                            <span x-text="displayText" class="italic"></span>
                        </div>
                    </template>
                </div>
            </template>

            {{-- ── Error state ── --}}
            @if($error)
            <div class="flex items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-500 text-sm"></i>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-rose-700">{{ $error }}</p>
                    @if($transcription)
                    <button wire:click="retry"
                        class="mt-1.5 text-xs font-semibold text-rose-600 hover:text-rose-800 underline underline-offset-2 transition">
                        Réessayer
                    </button>
                    @endif
                </div>
            </div>
            @endif

            {{-- ── Result card ── --}}
            @if($commandResult && !$error)
            @php
                $intent = \App\Enums\VoiceIntentEnum::tryFrom($commandResult['intent'] ?? '') ?? \App\Enums\VoiceIntentEnum::Unknown;
                $confidence = (int) round(($commandResult['confidence'] ?? 0) * 100);
                $confColor = $confidence >= 80 ? 'text-emerald-600' : ($confidence >= 50 ? 'text-amber-600' : 'text-rose-600');
            @endphp

            <div class="rounded-2xl border border-slate-200/60 bg-slate-50 p-4 space-y-3">

                {{-- Intent + confidence --}}
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="stat-icon bg-primary text-accent shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                            <i class="fa-solid {{ $intent->icon() }}"></i>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $intent->label() }}</p>
                            @if($commandResult['human_readable'] ?? false)
                            <p class="text-xs text-slate-500 mt-0.5 leading-snug">{{ $commandResult['human_readable'] }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-lg font-black {{ $confColor }}">{{ $confidence }}%</span>
                        <p class="text-[10px] text-slate-400">confiance</p>
                    </div>
                </div>

                {{-- Extracted data preview --}}
                @if(!empty($commandResult['data']))
                <div class="space-y-1.5 border-t border-slate-200 pt-3">
                    @foreach($commandResult['data'] as $key => $value)
                        @if(!is_null($value) && !in_array($key, ['sender_match_name', 'receiver_match_name']))
                        <div class="flex items-start justify-between gap-2 text-xs">
                            <span class="text-slate-400 capitalize shrink-0">{{ str_replace('_', ' ', $key) }}</span>
                            <span class="font-medium text-slate-700 text-right truncate max-w-[60%]">
                                @if($key === 'sender_id' && !empty($commandResult['data']['sender_match_name']))
                                    {{ $commandResult['data']['sender_match_name'] }} <span class="text-emerald-500">(✓)</span>
                                @elseif($key === 'receiver_id' && !empty($commandResult['data']['receiver_match_name']))
                                    {{ $commandResult['data']['receiver_match_name'] }} <span class="text-emerald-500">(✓)</span>
                                @elseif(is_bool($value))
                                    {{ $value ? 'Oui' : 'Non' }}
                                @else
                                    {{ $value }}
                                @endif
                            </span>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif

                {{-- Actions --}}
                @if(!$applied)
                <div class="flex gap-2 pt-1">
                    <button wire:click="apply"
                        class="btn-primary flex-1 py-2 text-sm">
                        <i class="fa-solid fa-check"></i>Appliquer
                    </button>
                    <button wire:click="close" type="button"
                        class="btn-secondary px-3 py-2 text-sm">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                @else
                <div class="flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                    <p class="text-sm font-semibold text-emerald-700">Commande appliquée !</p>
                </div>
                @endif

            </div>
            @endif

            {{-- ── Recent history ── --}}
            @if(!$commandResult && !$error && !$isProcessing)
            @php $logs = $this->recentLogs; @endphp
            @if($logs->isNotEmpty())
            <div class="border-t border-slate-100 dark:border-slate-700 pt-3">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Historique récent</p>
                <div class="space-y-1.5">
                    @foreach($logs as $log)
                    @php $logIntent = \App\Enums\VoiceIntentEnum::tryFrom($log->intent ?? ''); @endphp
                    <div class="flex items-center gap-2 rounded-xl px-3 py-2 bg-slate-50 border border-slate-100 dark:bg-slate-700/50 dark:border-slate-700">
                        @if($logIntent)
                        <i class="fa-solid {{ $logIntent->icon() }} text-xs text-slate-400 shrink-0 w-4 text-center"></i>
                        @endif
                        <p class="text-xs text-slate-600 dark:text-slate-300 truncate flex-1">{{ $log->transcription }}</p>
                        <span class="text-[10px] text-slate-400 shrink-0">{{ $log->created_at->format('H:i') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endif

            {{-- ── Hint ── --}}
            <p class="text-center text-[11px] text-slate-400 leading-relaxed">
                <i class="fa-solid fa-lightbulb text-accent mr-1"></i>
                Essayez : <em class="text-slate-500">"Colis pour Mamadou à Dakar, 20 kilos"</em>
            </p>

        </div>
    </div>
    @endif

</div>
