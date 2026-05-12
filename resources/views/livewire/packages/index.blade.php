<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <p class="page-eyebrow">Logistique</p>
                <h1 class="page-heading mt-1">Colis</h1>
                <p class="page-subtitle">Création, suivi et mise à jour des statuts.</p>
            </div>
            <a href="{{ route('packages.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i>Créer un colis
            </a>
        </div>
    </div>

    {{-- Table card --}}
    <div class="card-panel space-y-0">

        {{-- Filters --}}
        <div class="grid gap-3 border-b border-slate-100 pb-5 md:grid-cols-4">
            <div class="relative md:col-span-2">
                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input wire:model.live.debounce.300ms="search" type="search"
                    placeholder="Tracking, code retrait, client, téléphone…"
                    class="form-input pl-10">
            </div>
            <select wire:model.live="status" class="form-select">
                <option value="">Tous les statuts</option>
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="perPage" class="form-select">
                <option value="10">10 par page</option>
                <option value="25">25 par page</option>
                <option value="50">50 par page</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="mt-5 overflow-x-auto">
            <table class="table-surface w-full">
                <thead>
                    <tr>
                        <th class="table-header">
                            <button wire:click="sortBy('tracking_number')" class="flex items-center gap-1 hover:text-white">
                                Tracking <i class="fa-solid fa-sort text-[10px] opacity-60"></i>
                            </button>
                        </th>
                        <th class="table-header">Expéditeur / Destinataire</th>
                        <th class="table-header">
                            <button wire:click="sortBy('destination_country')" class="flex items-center gap-1 hover:text-white">
                                Trajet <i class="fa-solid fa-sort text-[10px] opacity-60"></i>
                            </button>
                        </th>
                        <th class="table-header">
                            <button wire:click="sortBy('status')" class="flex items-center gap-1 hover:text-white">
                                Statut <i class="fa-solid fa-sort text-[10px] opacity-60"></i>
                            </button>
                        </th>
                        <th class="table-header text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($packages as $package)
                    <tr wire:key="pkg-{{ $package->id }}" class="table-row-hover">
                        <td class="px-4 py-4">
                            <a href="{{ route('packages.show', $package) }}"
                                class="font-semibold text-[#0062a3] hover:text-[#004e7b] transition-colors">
                                {{ $package->tracking_number }}
                            </a>
                            <div class="mt-0.5 text-xs text-slate-400">
                                <i class="fa-solid fa-key text-[#FF8F1D] mr-1"></i>{{ $package->withdrawal_code }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-slate-800">{{ $package->sender->full_name }}</div>
                            <div class="mt-0.5 flex items-center gap-1 text-xs text-slate-400">
                                <i class="fa-solid fa-arrow-right text-[#FF8F1D]"></i>
                                {{ $package->receiver->full_name }} · {{ $package->receiver->phone }}
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600">
                            {{ $package->origin_country }}
                            <i class="fa-solid fa-arrow-right mx-1.5 text-[#FF8F1D] text-xs"></i>
                            {{ $package->destination_country }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }}">
                                {{ $package->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <a href="{{ route('packages.edit', $package) }}"
                                class="font-semibold text-slate-500 hover:text-[#0062a3] transition-colors">
                                Modifier
                            </a>
                            @can('delete', $package)
                            <button wire:click="delete({{ $package->id }})"
                                wire:confirm="Supprimer définitivement ce colis ?"
                                class="ml-4 font-semibold text-rose-500 hover:text-rose-700 transition-colors">
                                Supprimer
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-14 text-center">
                            <i class="fa-solid fa-box-open text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">Aucun colis trouvé.</p>
                            <p class="mt-1 text-xs text-slate-300">Essayez de modifier vos filtres.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 border-t border-slate-100 pt-4">
            {{ $packages->links() }}
        </div>

    </div>
</div>
