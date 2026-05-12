<div class="space-y-6">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-box text-accent mr-2"></i>{{ $package->tracking_number }}
            </h1>
            <p class="page-subtitle">
                <i class="fa-solid fa-key text-accent mr-1.5"></i>Code retrait :
                <span class="font-semibold text-primary">{{ $package->withdrawal_code }}</span>
                <span class="mx-2 text-slate-300">·</span>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }}">
                    {{ $package->status->label() }}
                </span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('packages.edit', $package) }}" class="btn-secondary">
                <i class="fa-solid fa-pen"></i>Modifier
            </a>
            <a href="{{ route('packages.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>Retour
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Details + History --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Package info --}}
            <div class="card-panel">
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="stat-icon bg-orange-50 text-orange-500 shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                        <i class="fa-solid fa-box"></i>
                    </span>
                    <h2 class="section-title">Informations du colis</h2>
                </div>
                <dl class="grid gap-x-8 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="detail-label">Nom</dt>
                        <dd class="detail-value">{{ $package->package_name }}</dd>
                    </div>
                    <div>
                        <dt class="detail-label">Agence</dt>
                        <dd class="detail-value">{{ $package->agency->name }}</dd>
                    </div>
                    <div>
                        <dt class="detail-label">Expéditeur</dt>
                        <dd class="detail-value">
                            {{ $package->sender->full_name }}
                            <span class="ml-2 text-sm font-normal text-slate-500">{{ $package->sender->phone }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="detail-label">Destinataire</dt>
                        <dd class="detail-value">
                            {{ $package->receiver->full_name }}
                            <span class="ml-2 text-sm font-normal text-slate-500">{{ $package->receiver->phone }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="detail-label">Trajet</dt>
                        <dd class="detail-value flex items-center gap-2">
                            {{ $package->origin_country }}
                            <i class="fa-solid fa-arrow-right text-accent text-xs"></i>
                            {{ $package->destination_country }}
                        </dd>
                    </div>
                    <div>
                        <dt class="detail-label">Poids / Quantité</dt>
                        <dd class="detail-value">
                            {{ $package->weight ? $package->weight.' kg' : '—' }}
                            <span class="mx-2 text-slate-300">·</span>
                            {{ $package->quantity }} unité(s)
                        </dd>
                    </div>
                    <div>
                        <dt class="detail-label">Valeur déclarée</dt>
                        <dd class="detail-value">{{ $package->declared_value ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="detail-label">Créé par</dt>
                        <dd class="detail-value">{{ $package->creator->first_name ?? 'N/A' }}</dd>
                    </div>
                    @if ($package->description)
                    <div class="sm:col-span-2">
                        <dt class="detail-label">Description</dt>
                        <dd class="mt-1 text-sm text-slate-700 leading-relaxed">{{ $package->description }}</dd>
                    </div>
                    @endif
                    @if ($package->notes)
                    <div class="sm:col-span-2">
                        <dt class="detail-label">Notes internes</dt>
                        <dd class="mt-1 rounded-xl bg-amber-50 border border-amber-200/70 px-3 py-2.5 text-sm text-amber-800 leading-relaxed">
                            <i class="fa-solid fa-note-sticky mr-1.5 text-amber-400"></i>{{ $package->notes }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- History --}}
            <div class="card-panel">
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="stat-icon bg-slate-100 text-slate-500 shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    <h2 class="section-title">Historique des statuts</h2>
                </div>
                <div class="space-y-0">
                    @forelse ($package->histories->sortByDesc('created_at') as $history)
                    <div class="timeline-item pb-5">
                        <p class="text-sm font-semibold text-slate-800">
                            <span class="text-slate-400 font-normal">{{ $history->old_status ?: 'Création' }}</span>
                            <i class="fa-solid fa-arrow-right mx-1.5 text-accent text-xs"></i>
                            {{ $history->new_status }}
                        </p>
                        @if ($history->comment)
                        <p class="mt-0.5 text-sm text-slate-600">{{ $history->comment }}</p>
                        @endif
                        <p class="mt-0.5 text-xs text-slate-400">
                            {{ $history->user->first_name ?? 'Système' }} · {{ $history->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                    @empty
                    <p class="py-4 text-sm text-slate-400">Aucun historique disponible.</p>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Change status --}}
            <div class="card-panel">
                <div class="flex items-center gap-2.5 mb-4">
                    <span class="stat-icon bg-violet-50 text-violet-500 shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </span>
                    <h2 class="section-title">Changer le statut</h2>
                </div>
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
                        <textarea wire:model="comment" rows="3" class="form-textarea"
                            placeholder="Raison du changement…"></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <i class="fa-solid fa-check"></i>Mettre à jour
                    </button>
                </form>
            </div>

            {{-- Notifications --}}
            <div class="card-panel">
                <div class="flex items-center gap-2.5 mb-4">
                    <span class="stat-icon bg-amber-50 text-amber-500 shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <h2 class="section-title">Notifications</h2>
                </div>
                <div class="space-y-2.5">
                    @forelse ($package->notifications->sortByDesc('created_at') as $notification)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-semibold text-slate-800">{{ $notification->channel->label() }}</span>
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $notification->status->badgeClasses() }}">
                                {{ $notification->status->label() }}
                            </span>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-600 leading-relaxed">{{ $notification->message }}</p>
                    </div>
                    @empty
                    <div class="flex flex-col items-center py-6 text-center">
                        <i class="fa-solid fa-bell-slash text-2xl text-slate-200 mb-2"></i>
                        <p class="text-sm text-slate-400">Aucune notification.</p>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>
