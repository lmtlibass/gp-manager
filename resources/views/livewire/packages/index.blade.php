<div class="space-y-6">

    {{-- Page header --}}
    <div class="page-header animate-fade-in">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-box-open text-[#FF8F1D] mr-2" aria-hidden="true"></i>Colis
            </h1>
            <p class="page-subtitle">Enregistrement, suivi et mise à jour des statuts.</p>
        </div>
        <a href="{{ route('packages.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>Nouveau colis
        </a>
    </div>

    {{-- Filters --}}
    <div class="card-panel p-4 animate-fade-in stagger-1">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="field-wrap sm:col-span-2 lg:col-span-2">
                <span class="field-icon"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                <input wire:model.live.debounce.300ms="search" type="search"
                    placeholder="Tracking, code retrait, client, téléphone…"
                    class="form-input pl-10 py-2.5"
                    aria-label="Rechercher un colis">
            </div>
            <div class="select-wrap">
                <select wire:model.live="status" class="form-select py-2.5" aria-label="Filtrer par statut">
                    <option value="">Tous les statuts</option>
                    @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                    @endforeach
                </select>
                <span class="select-chevron"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span>
            </div>
            <div class="select-wrap">
                <select wire:model.live="perPage" class="form-select py-2.5" aria-label="Nombre de résultats par page">
                    <option value="10">10 résultats</option>
                    <option value="25">25 résultats</option>
                    <option value="50">50 résultats</option>
                </select>
                <span class="select-chevron"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span>
            </div>
        </div>
    </div>

    {{-- Package cards --}}
    <div class="space-y-3" role="list" aria-label="Liste des colis">
        @forelse ($packages as $i => $package)
        <div wire:key="pkg-{{ $package->id }}"
             class="pkg-card animate-fade-in-up"
             style="animation-delay: {{ min($i * 0.05, 0.3) }}s"
             role="listitem">
            <div class="flex items-start gap-4">

                {{-- Status icon --}}
                <div class="stat-icon bg-slate-100 text-slate-500 shrink-0 mt-0.5 dark:bg-slate-700 dark:text-slate-400"
                     style="width:2.5rem;height:2.5rem;border-radius:0.875rem;font-size:0.875rem;"
                     aria-hidden="true">
                    <i class="fa-solid fa-box"></i>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('packages.show', $package) }}"
                            class="text-base font-bold text-[#001F3D] hover:text-[#0062a3] transition-colors dark:text-white dark:hover:text-[#4da6ff]"
                            aria-label="Voir le colis {{ $package->tracking_number }}">
                            {{ $package->tracking_number }}
                        </a>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }}">
                            {{ $package->status->label() }}
                        </span>
                    </div>

                    <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span class="flex items-center gap-1.5 text-sm text-slate-700 dark:text-slate-300">
                            <i class="fa-solid fa-user text-slate-400 text-xs" aria-hidden="true"></i>
                            {{ $package->sender->full_name }}
                            <i class="fa-solid fa-arrow-right text-[#FF8F1D] text-xs" aria-label="vers" aria-hidden="true"></i>
                            <span class="font-medium">{{ $package->receiver->full_name }}</span>
                        </span>
                        <span class="flex items-center gap-1 text-xs text-slate-400 dark:text-slate-500">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                            {{ $package->origin_country }}
                            <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                            {{ $package->destination_country }}
                        </span>
                    </div>

                    <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-500">
                        <i class="fa-solid fa-key text-[#FF8F1D]" aria-hidden="true"></i>
                        <span aria-label="Code de retrait">{{ $package->withdrawal_code }}</span>
                        @if($package->receiver->phone)
                        <span class="mx-1 text-slate-200 dark:text-slate-700">·</span>
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        {{ $package->receiver->phone }}
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    <a href="{{ route('packages.edit', $package) }}"
                        class="btn-icon" title="Modifier ce colis" aria-label="Modifier le colis {{ $package->tracking_number }}">
                        <i class="fa-solid fa-pen text-xs" aria-hidden="true"></i>
                    </a>
                    @can('delete', $package)
                    <button wire:click="delete({{ $package->id }})"
                        wire:confirm="Supprimer définitivement ce colis ?"
                        class="btn-icon-danger" title="Supprimer ce colis"
                        aria-label="Supprimer le colis {{ $package->tracking_number }}">
                        <i class="fa-solid fa-trash text-xs" aria-hidden="true"></i>
                    </button>
                    @endcan
                </div>

            </div>
        </div>
        @empty
        <div class="card-panel flex flex-col items-center justify-center py-16 text-center animate-scale-in">
            <div class="stat-icon bg-slate-100 text-slate-300 mb-4 dark:bg-slate-700 dark:text-slate-600"
                 style="width:3.5rem;height:3.5rem;border-radius:1rem;font-size:1.25rem;"
                 aria-hidden="true">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <p class="text-base font-semibold text-slate-400 dark:text-slate-500">Aucun colis trouvé.</p>
            <p class="mt-1 text-sm text-slate-300 dark:text-slate-600">Essayez de modifier vos filtres ou créez un nouveau colis.</p>
            <a href="{{ route('packages.create') }}" class="btn-primary mt-5">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>Créer un colis
            </a>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($packages->hasPages())
    <div class="card-panel p-4">
        {{ $packages->links() }}
    </div>
    @endif

</div>
