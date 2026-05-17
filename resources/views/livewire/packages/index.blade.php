<div class="space-y-6">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-box-open text-accent mr-2"></i>Colis
            </h1>
            <p class="page-subtitle">Création, suivi et mise à jour des statuts.</p>
        </div>
        <a href="{{ route('packages.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>Nouveau colis
        </a>
    </div>

    {{-- Filters --}}
    <div class="card-panel p-4">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="field-wrap sm:col-span-2 lg:col-span-2">
                <span class="field-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input wire:model.live.debounce.300ms="search" type="search"
                    placeholder="Tracking, code retrait, client, téléphone…"
                    class="form-input pl-10 py-2.5">
            </div>
            <select wire:model.live="status" class="form-select py-2.5">
                <option value="">Tous les statuts</option>
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Package cards --}}
    <div class="space-y-3">
        @forelse ($packages as $package)
        <div wire:key="pkg-{{ $package->id }}" class="pkg-card">
            <div class="flex items-start gap-4">

                {{-- Icon --}}
                <div class="stat-icon bg-slate-100 text-slate-500 shrink-0 mt-0.5" style="width:2.5rem;height:2.5rem;border-radius:0.875rem;font-size:0.875rem;">
                    <i class="fa-solid fa-box"></i>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('packages.show', $package) }}"
                            class="text-base font-bold text-primary hover:text-[#0062a3] transition-colors">
                            {{ $package->tracking_number }}
                        </a>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }}">
                            {{ $package->status->label() }}
                        </span>
                    </div>

                    <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span class="flex items-center gap-1.5 text-sm text-slate-700">
                            <i class="fa-solid fa-user text-slate-400 text-xs"></i>
                            {{ $package->sender->full_name }}
                            <i class="fa-solid fa-arrow-right text-accent text-xs"></i>
                            <span class="font-medium">{{ $package->receiver->full_name }}</span>
                        </span>
                        <span class="flex items-center gap-1 text-xs text-slate-400">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $package->origin_country }}
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            {{ $package->destination_country }}
                        </span>
                    </div>

                    <div class="mt-1 flex items-center gap-1 text-xs text-slate-400">
                        <i class="fa-solid fa-key text-accent"></i>
                        {{ $package->withdrawal_code }}
                        @if($package->receiver->phone)
                        <span class="mx-1.5 text-slate-200">·</span>
                        <i class="fa-solid fa-phone"></i>
                        {{ $package->receiver->phone }}
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1.5 shrink-0">
                    <a href="{{ route('packages.edit', $package) }}"
                        class="btn-icon" title="Modifier">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                    @can('delete', $package)
                    <button wire:click="delete({{ $package->id }})"
                        wire:confirm="Supprimer définitivement ce colis ?"
                        class="btn-icon-danger" title="Supprimer">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                    @endcan
                </div>

            </div>
        </div>
        @empty
        <div class="card-panel flex flex-col items-center justify-center py-16 text-center">
            <div class="stat-icon bg-slate-100 text-slate-300 mb-4" style="width:3.5rem;height:3.5rem;border-radius:1rem;font-size:1.25rem;">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <p class="text-base font-semibold text-slate-400">Aucun colis trouvé.</p>
            <p class="mt-1 text-sm text-slate-300">Essayez de modifier vos filtres.</p>
            <a href="{{ route('packages.create') }}" class="btn-primary mt-4">
                <i class="fa-solid fa-plus"></i>Créer un colis
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
