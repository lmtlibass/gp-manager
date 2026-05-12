<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <p class="page-eyebrow">Fiche colis</p>
                <h1 class="page-heading mt-1">{{ $package->tracking_number }}</h1>
                <p class="page-subtitle">
                    <i class="fa-solid fa-key text-[#FF8F1D] mr-1.5"></i>Code retrait :
                    <span class="font-semibold text-[#001F3D]">{{ $package->withdrawal_code }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }}">
                    {{ $package->status->label() }}
                </span>
                <a href="{{ route('packages.edit', $package) }}" class="btn-secondary">
                    <i class="fa-solid fa-pen"></i>Modifier
                </a>
                <a href="{{ route('packages.index') }}" class="btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Details + History --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Package info --}}
            <div class="card-panel">
                <h2 class="section-title mb-5">
                    <i class="fa-solid fa-box text-[#FF8F1D] mr-2"></i>Informations du colis
                </h2>
                <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Nom</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $package->package_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Agence</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $package->agency->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Expéditeur</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $package->sender->full_name }}
                            <span class="ml-2 text-sm font-normal text-slate-500">{{ $package->sender->phone }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Destinataire</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $package->receiver->full_name }}
                            <span class="ml-2 text-sm font-normal text-slate-500">{{ $package->receiver->phone }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Trajet</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $package->origin_country }}
                            <i class="fa-solid fa-arrow-right mx-2 text-[#FF8F1D] text-xs"></i>
                            {{ $package->destination_country }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Poids / Quantité</dt>
                        <dd class="mt-1 font-medium text-slate-900">
                            {{ $package->weight ? $package->weight.' kg' : '—' }}
                            <span class="mx-2 text-slate-300">·</span>
                            {{ $package->quantity }} unité(s)
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Valeur déclarée</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $package->declared_value ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Créé par</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $package->creator->first_name ?? 'N/A' }}</dd>
                    </div>
                    @if ($package->description)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Description</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $package->description }}</dd>
                    </div>
                    @endif
                    @if ($package->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Notes internes</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $package->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- History --}}
            <div class="card-panel">
                <h2 class="section-title mb-5">
                    <i class="fa-solid fa-clock-rotate-left text-[#FF8F1D] mr-2"></i>Historique des statuts
                </h2>
                <div class="space-y-0">
                    @forelse ($package->histories->sortByDesc('created_at') as $history)
                    <div class="timeline-item pb-5">
                        <p class="text-sm font-semibold text-slate-900">
                            {{ $history->old_status ?: 'Création' }}
                            <i class="fa-solid fa-arrow-right mx-1.5 text-[#FF8F1D] text-xs"></i>
                            {{ $history->new_status }}
                        </p>
                        @if ($history->comment)
                        <p class="mt-0.5 text-sm text-slate-600">{{ $history->comment }}</p>
                        @endif
                        <p class="mt-1 text-xs text-slate-400">
                            {{ $history->user->first_name ?? 'Système' }} &middot;
                            {{ $history->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    @empty
                    <p class="py-4 text-sm text-slate-400">Aucun historique disponible.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Sidebar actions --}}
        <div class="space-y-6">

            {{-- Change status --}}
            <div class="card-panel">
                <h2 class="section-title mb-4">
                    <i class="fa-solid fa-arrows-rotate text-[#FF8F1D] mr-2"></i>Changer le statut
                </h2>
                <form wire:submit="changeStatus" class="space-y-4">
                    <div>
                        <label class="form-label">Nouveau statut</label>
                        <select wire:model="status" class="form-select">
                            @foreach ($statuses as $statusOption)
                                <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Commentaire (optionnel)</label>
                        <textarea wire:model="comment" rows="3" class="form-input"
                            placeholder="Raison du changement…"></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <i class="fa-solid fa-check"></i>Mettre à jour
                    </button>
                </form>
            </div>

            {{-- Notifications --}}
            <div class="card-panel">
                <h2 class="section-title mb-4">
                    <i class="fa-solid fa-bell text-[#FF8F1D] mr-2"></i>Notifications
                </h2>
                <div class="space-y-3">
                    @forelse ($package->notifications->sortByDesc('created_at') as $notification)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-semibold text-slate-800">{{ $notification->channel->label() }}</span>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $notification->status->badgeClasses() }}">
                                {{ $notification->status->label() }}
                            </span>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-600">{{ $notification->message }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-slate-400">Aucune notification liée à ce colis.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>
