<div class="space-y-6">

    {{-- Page header --}}
    <div class="page-header animate-fade-in">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-hand-holding-box text-[#FF8F1D] mr-2" aria-hidden="true"></i>Retrait de colis
            </h1>
            <p class="page-subtitle">Validation au guichet — saisissez le code, le numéro de suivi ou le nom du client.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- UX tip banner --}}
            @unless ($package)
            <div class="flex items-start gap-3 rounded-2xl border border-[#0062a3]/20 bg-[#0062a3]/5 px-4 py-3.5 animate-slide-down dark:bg-blue-900/20 dark:border-blue-700/30"
                 role="note" aria-label="Aide pour rechercher un colis">
                <i class="fa-solid fa-lightbulb text-[#0062a3] dark:text-blue-400 mt-0.5 shrink-0" aria-hidden="true"></i>
                <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                    <strong class="text-[#0062a3] dark:text-blue-400">Comment retrouver un colis ?</strong><br>
                    Saisissez le <strong>code à 4 chiffres</strong> fourni au client, le <strong>numéro de suivi</strong> (ex: WM-2026-000001)
                    ou le <strong>nom de l'expéditeur / destinataire</strong>. Les suggestions apparaissent automatiquement.
                </div>
            </div>
            @endunless

            {{-- Search hero --}}
            <div class="card-panel animate-fade-in-up stagger-1">
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="stat-icon bg-amber-50 text-amber-500 dark:bg-amber-900/30 dark:text-amber-400 shrink-0"
                          style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;"
                          aria-hidden="true">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <div>
                        <h2 class="section-title">Rechercher un colis</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Code · Numéro de suivi · Nom expéditeur / destinataire</p>
                    </div>
                </div>

                <div x-data="{ open: @entangle('showSuggestions') }" class="relative">
                    <form wire:submit="findPackage" class="flex flex-col gap-3 sm:flex-row">
                        <div class="field-wrap flex-1">
                            <span class="field-icon text-lg"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                            <input
                                wire:model.live.debounce.300ms="search"
                                @focus="open = $wire.suggestions.length > 0"
                                @click.away="open = false"
                                placeholder="Code 1234 · WM-2026-… · Jean Dupont"
                                class="input-hero pl-14"
                                autocomplete="off"
                                autofocus
                                aria-label="Rechercher un colis par code, numéro de suivi ou nom"
                                aria-autocomplete="list"
                                :aria-expanded="open.toString()">
                        </div>
                        <button type="submit"
                                class="btn-accent shrink-0 px-6 text-base"
                                style="padding-top:1rem;padding-bottom:1rem;"
                                aria-label="Lancer la recherche">
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>Trouver
                        </button>
                    </form>

                    {{-- Autocomplete dropdown --}}
                    <div
                        x-show="open && $wire.suggestions.length > 0"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-cloak
                        class="absolute inset-x-0 top-full z-40 mt-1.5 rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden dark:bg-slate-800 dark:border-slate-700"
                        role="listbox" aria-label="Suggestions de colis">
                        <ul class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($suggestions as $s)
                            <li role="option">
                                <button
                                    type="button"
                                    wire:click="selectSuggestion({{ $s['id'] }})"
                                    @click="open = false"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                    aria-label="Sélectionner {{ $s['tracking_number'] }}">
                                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0062a3]/10 text-[#0062a3] dark:bg-blue-900/30 dark:text-blue-400"
                                          aria-hidden="true">
                                        <i class="fa-solid fa-box text-sm"></i>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-sm text-slate-800 dark:text-slate-200">{{ $s['tracking_number'] }}</span>
                                            <span class="text-xs font-mono text-slate-400 dark:text-slate-500">{{ $s['withdrawal_code'] }}</span>
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset {{ $s['status_classes'] }}">{{ $s['status'] }}</span>
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                            <i class="fa-solid fa-arrow-up text-[10px] mr-1 text-slate-400" aria-hidden="true"></i>{{ $s['sender_name'] }}
                                            <span class="mx-1.5 text-slate-300 dark:text-slate-600">→</span>
                                            <i class="fa-solid fa-arrow-down text-[10px] mr-1 text-slate-400" aria-hidden="true"></i>{{ $s['receiver_name'] }}
                                            @if($s['package_name'])
                                            <span class="mx-1.5 text-slate-300 dark:text-slate-600">·</span>{{ $s['package_name'] }}
                                            @endif
                                        </div>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-slate-300 dark:text-slate-600 text-xs shrink-0" aria-hidden="true"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                @error('search')
                <p class="mt-3 flex items-center gap-2 text-sm font-medium text-rose-600 dark:text-rose-400" role="alert">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}
                </p>
                @enderror
            </div>

            {{-- Package found --}}
            @if ($package)
            <div class="rounded-2xl border-2 border-[#0062a3]/30 bg-blue-50 p-5 animate-scale-in dark:bg-blue-900/15 dark:border-blue-700/40"
                 role="region" aria-label="Colis identifié">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="stat-icon bg-[#0062a3] text-white shrink-0"
                             style="width:2.75rem;height:2.75rem;border-radius:0.875rem;"
                             aria-hidden="true">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-[#0062a3] dark:text-blue-400 mb-0.5">Colis identifié</p>
                            <p class="text-lg font-bold text-[#001F3D] dark:text-white">{{ $package->tracking_number }}</p>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-0.5">
                                {{ $package->package_name }}
                                <span class="mx-1.5 text-slate-300 dark:text-slate-600">·</span>
                                <i class="fa-solid fa-arrow-up text-[10px] mr-0.5" aria-hidden="true"></i>{{ $package->sender?->full_name }}
                                <span class="mx-1.5 text-slate-300 dark:text-slate-600">→</span>
                                <i class="fa-solid fa-arrow-down text-[10px] mr-0.5" aria-hidden="true"></i>{{ $package->receiver?->full_name }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }}">
                            {{ $package->status->label() }}
                        </span>
                        <button type="button" wire:click="clearPackage"
                            class="inline-flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-200/80 dark:hover:bg-slate-700 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                            title="Changer de colis" aria-label="Effacer et rechercher un autre colis">
                            <i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                @if ($package->withdrawal)
                <div class="mt-3 flex items-center gap-2 rounded-xl bg-rose-50 border border-rose-200 px-3 py-2 text-sm font-medium text-rose-700 dark:bg-rose-900/20 dark:border-rose-700/40 dark:text-rose-400"
                     role="alert">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                    Ce colis a déjà été retiré le {{ $package->withdrawal->withdrawn_at->format('d/m/Y à H:i') }}.
                </div>
                @endif
            </div>

            {{-- Withdrawal form --}}
            @unless ($package->withdrawal)
            <div class="card-panel animate-fade-in-up stagger-2">
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="stat-icon bg-emerald-50 text-emerald-500 dark:bg-emerald-900/30 dark:text-emerald-400 shrink-0"
                          style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;"
                          aria-hidden="true">
                        <i class="fa-solid fa-user-check"></i>
                    </span>
                    <div>
                        <h2 class="section-title">Identité du retrait</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Vérifiez la pièce d'identité avant de valider</p>
                    </div>
                </div>
                <form wire:submit="validateWithdrawal" class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="receiver_name" class="form-label">
                                <i class="fa-solid fa-user text-slate-400 mr-1.5 text-xs" aria-hidden="true"></i>
                                Nom du receveur <span class="text-rose-500" aria-label="champ obligatoire">*</span>
                            </label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-user" aria-hidden="true"></i></span>
                                <input id="receiver_name" wire:model="receiver_name" type="text"
                                    class="form-input pl-10" placeholder="Nom complet"
                                    aria-required="true">
                            </div>
                            @error('receiver_name') <p class="form-error" role="alert"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="receiver_phone" class="form-label">
                                <i class="fa-solid fa-phone text-slate-400 mr-1.5 text-xs" aria-hidden="true"></i>
                                Téléphone <span class="text-rose-500" aria-label="champ obligatoire">*</span>
                            </label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-phone" aria-hidden="true"></i></span>
                                <input id="receiver_phone" wire:model="receiver_phone" type="tel"
                                    class="form-input pl-10" placeholder="+33 6 00 00 00 00"
                                    aria-required="true">
                            </div>
                            @error('receiver_phone') <p class="form-error" role="alert"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="identity_document" class="form-label">
                                <i class="fa-solid fa-id-card text-slate-400 mr-1.5 text-xs" aria-hidden="true"></i>
                                Pièce d'identité
                            </label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-id-card" aria-hidden="true"></i></span>
                                <input id="identity_document" wire:model="identity_document" type="text"
                                    class="form-input pl-10" placeholder="CNI, Passeport…">
                            </div>
                        </div>
                        <div>
                            <label for="withdrawal_notes" class="form-label">
                                <i class="fa-solid fa-pen-to-square text-slate-400 mr-1.5 text-xs" aria-hidden="true"></i>
                                Notes
                            </label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></span>
                                <input id="withdrawal_notes" wire:model="notes" type="text"
                                    class="form-input pl-10" placeholder="Remarques éventuelles">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <p class="text-xs text-slate-400 flex items-center gap-1.5">
                            <i class="fa-brands fa-whatsapp text-emerald-500 text-base" aria-hidden="true"></i>
                            Un WhatsApp sera envoyé automatiquement à l'expéditeur.
                        </p>
                        <button type="submit" class="btn-success px-6" wire:loading.attr="disabled" aria-live="polite">
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            <span wire:loading.remove>Valider le retrait</span>
                            <span wire:loading>En cours…</span>
                        </button>
                    </div>
                </form>
            </div>
            @endunless
            @endif

        </div>

        {{-- Recent withdrawals --}}
        <section class="card-panel animate-fade-in-up stagger-2" aria-label="Historique des derniers retraits">
            <div class="flex items-center gap-2.5 border-b border-slate-100 dark:border-slate-700 pb-4 mb-4">
                <div class="stat-icon bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400 shrink-0"
                     style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;"
                     aria-hidden="true">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h2 class="section-title">Derniers retraits</h2>
                    <p class="text-xs text-slate-400">Historique du guichet</p>
                </div>
            </div>
            <div class="space-y-0">
                @forelse ($recentWithdrawals as $withdrawal)
                <div class="timeline-item pb-4">
                    <a href="{{ route('packages.show', $withdrawal->package) }}"
                        class="text-sm font-semibold text-[#0062a3] hover:underline dark:text-[#4da6ff]">
                        {{ $withdrawal->package->tracking_number }}
                    </a>
                    <p class="mt-0.5 text-sm font-medium text-slate-700 dark:text-slate-300">{{ $withdrawal->receiver_name }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">
                        <i class="fa-solid fa-phone mr-1" aria-hidden="true"></i>{{ $withdrawal->receiver_phone }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                        {{ $withdrawal->validator->first_name ?? 'Agent' }}
                        · {{ $withdrawal->withdrawn_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                @empty
                <div class="flex flex-col items-center py-10 text-center">
                    <div class="stat-icon bg-slate-100 text-slate-300 mb-3 dark:bg-slate-700 dark:text-slate-600"
                         style="width:2.75rem;height:2.75rem;border-radius:0.875rem;"
                         aria-hidden="true">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <p class="text-sm text-slate-400 dark:text-slate-500">Aucun retrait récent.</p>
                </div>
                @endforelse
            </div>
        </section>

    </div>
</div>
