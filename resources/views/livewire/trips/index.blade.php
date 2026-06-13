<div class="space-y-6">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-truck-fast text-accent mr-2"></i>Voyages & Groupages
            </h1>
            <p class="page-subtitle">Planifiez des voyages et constituez les groupages de colis.</p>
        </div>
        @unless ($showForm)
        <button type="button" wire:click="openCreateForm" class="btn-accent shrink-0">
            <i class="fa-solid fa-plus"></i>Planifier un voyage
        </button>
        @endunless
    </div>

    {{-- Create / Edit form --}}
    @if ($showForm)
    <div class="card-panel border-2 border-[#0062a3]/20">
        <div class="flex items-center gap-2.5 mb-5">
            <span class="stat-icon bg-[#0062a3]/10 text-[#0062a3] shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                <i class="fa-solid fa-{{ $editingTripId ? 'pen' : 'plus' }}"></i>
            </span>
            <div>
                <h2 class="section-title">{{ $editingTripId ? 'Modifier le voyage' : 'Nouveau voyage' }}</h2>
                <p class="text-xs text-slate-400 mt-0.5">Renseignez les informations du voyage</p>
            </div>
        </div>

        <form wire:submit="saveTrip" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="form-label">Nom du voyage <span class="text-rose-500">*</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-tag"></i></span>
                        <input wire:model="name" type="text" class="form-input pl-10" placeholder="Ex : Paris → Dakar — Juin 2026">
                    </div>
                    @error('name') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Date de départ <span class="text-rose-500">*</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-calendar-days"></i></span>
                        <input wire:model="departure_date" type="date" class="form-input pl-10">
                    </div>
                    @error('departure_date') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Chauffeur / Responsable</label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-user-tie"></i></span>
                        <input wire:model="driver_name" type="text" class="form-input pl-10" placeholder="Nom du chauffeur">
                    </div>
                </div>
                <div>
                    <label class="form-label">Origine <span class="text-rose-500">*</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-location-dot"></i></span>
                        <input wire:model="origin" type="text" class="form-input pl-10" placeholder="Ville / Pays de départ">
                    </div>
                    @error('origin') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Destination <span class="text-rose-500">*</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-flag-checkered"></i></span>
                        <input wire:model="destination" type="text" class="form-input pl-10" placeholder="Ville / Pays d'arrivée">
                    </div>
                    @error('destination') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Notes</label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-pen-to-square"></i></span>
                        <input wire:model="notes" type="text" class="form-input pl-10" placeholder="Informations complémentaires">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" wire:click="cancelForm" class="btn-secondary">
                    <i class="fa-solid fa-xmark"></i>Annuler
                </button>
                <button type="submit" class="btn-accent" wire:loading.attr="disabled">
                    <i class="fa-solid fa-check"></i>
                    <span wire:loading.remove>{{ $editingTripId ? 'Mettre à jour' : 'Planifier' }}</span>
                    <span wire:loading>En cours…</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-5">

        {{-- Trip list --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Status filter --}}
            <div class="flex gap-2 flex-wrap">
                @foreach (['' => 'Tous', 'planned' => 'Planifiés', 'in_transit' => 'En transit', 'completed' => 'Terminés'] as $val => $label)
                <button type="button"
                    wire:click="$set('filterStatus', '{{ $val }}')"
                    class="rounded-full px-3 py-1 text-xs font-semibold border transition-colors
                        {{ $filterStatus === $val
                            ? 'bg-[#0062a3] text-white border-[#0062a3]'
                            : 'bg-white text-slate-600 border-slate-200 hover:border-[#0062a3]/40' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            @forelse ($trips as $trip)
            <div
                wire:key="trip-{{ $trip->id }}"
                class="card-panel cursor-pointer transition-all hover:shadow-md
                    {{ $selectedTripId === $trip->id ? 'border-2 border-[#0062a3]/40 ring-2 ring-[#0062a3]/10' : '' }}"
                wire:click="toggleTrip({{ $trip->id }})">

                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $trip->status->badgeClasses() }}">
                                <i class="fa-solid {{ $trip->status->icon() }} mr-1.5"></i>{{ $trip->status->label() }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $trip->departure_date->format('d/m/Y') }}</span>
                        </div>
                        <p class="mt-1.5 font-semibold text-slate-800 truncate">{{ $trip->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            <i class="fa-solid fa-location-dot mr-1"></i>{{ $trip->origin }}
                            <i class="fa-solid fa-arrow-right mx-1.5 text-slate-300"></i>
                            {{ $trip->destination }}
                        </p>
                        @if ($trip->driver_name)
                        <p class="text-xs text-slate-400 mt-0.5">
                            <i class="fa-solid fa-user-tie mr-1"></i>{{ $trip->driver_name }}
                        </p>
                        @endif
                    </div>
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                            <i class="fa-solid fa-box text-slate-400"></i>{{ $trip->packages_count }}
                        </span>
                    </div>
                </div>

                @if ($trip->status === \App\Enums\TripStatus::Planned)
                <div class="flex gap-2 mt-4 pt-3 border-t border-slate-100" x-on:click.stop>
                    <button type="button"
                        wire:click="startTrip({{ $trip->id }})"
                        wire:confirm="Démarrer ce voyage ? Tous les colis reçus passeront en transit."
                        class="btn-success text-xs px-3 py-1.5 flex-1">
                        <i class="fa-solid fa-truck-fast"></i>Démarrer
                    </button>
                    <button type="button"
                        wire:click="editTrip({{ $trip->id }})"
                        class="btn-secondary text-xs px-3 py-1.5">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button type="button"
                        wire:click="deleteTrip({{ $trip->id }})"
                        wire:confirm="Supprimer ce voyage ?"
                        class="inline-flex items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
                @endif
            </div>
            @empty
            <div class="card-panel flex flex-col items-center py-12 text-center">
                <div class="stat-icon bg-slate-100 text-slate-300 mb-3" style="width:3rem;height:3rem;border-radius:1rem;">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <p class="text-sm font-medium text-slate-500">Aucun voyage planifié</p>
                <p class="text-xs text-slate-400 mt-1">Cliquez sur « Planifier un voyage » pour commencer.</p>
            </div>
            @endforelse
        </div>

        {{-- Groupage panel --}}
        <div class="lg:col-span-3">
            @if ($selectedTrip)
            <div class="card-panel space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="section-title flex items-center gap-2">
                            <i class="fa-solid fa-layer-group text-[#0062a3]"></i>
                            Groupage — {{ $selectedTrip->name }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $selectedTrip->origin }} → {{ $selectedTrip->destination }}
                            · {{ $selectedTrip->departure_date->format('d/m/Y') }}
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3 py-1.5 text-sm font-bold text-slate-700">
                        <i class="fa-solid fa-box text-slate-400"></i>
                        {{ $selectedTrip->packages->count() }} colis
                    </span>
                </div>

                {{-- Add package search (only for planned trips) --}}
                @if ($selectedTrip->status === \App\Enums\TripStatus::Planned)
                <div x-data="{ open: false }" class="relative">
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-plus-circle text-[#0062a3]"></i></span>
                        <input
                            wire:model.live.debounce.300ms="packageSearch"
                            @focus="open = $wire.packageSuggestions.length > 0"
                            @input="open = $wire.packageSuggestions.length > 0"
                            @click.away="open = false"
                            type="text"
                            class="form-input pl-10"
                            placeholder="Ajouter un colis : code, numéro de suivi ou nom client…"
                            autocomplete="off">
                    </div>

                    <div x-show="open && $wire.packageSuggestions.length > 0"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-cloak
                        class="absolute inset-x-0 top-full z-40 mt-1 rounded-2xl border border-slate-200 bg-white shadow-lg overflow-hidden">
                        <ul class="divide-y divide-slate-100">
                            @foreach ($packageSuggestions as $s)
                            <li>
                                <button
                                    type="button"
                                    wire:click="addPackage({{ $s['id'] }})"
                                    @click="open = false"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-left hover:bg-emerald-50 transition-colors">
                                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-slate-800">{{ $s['tracking_number'] }}
                                            @if($s['package_name'])
                                            <span class="font-normal text-slate-500">— {{ $s['package_name'] }}</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-slate-500 truncate">
                                            <i class="fa-solid fa-arrow-up text-[10px]"></i> {{ $s['sender_name'] }}
                                            <span class="mx-1 text-slate-300">→</span>
                                            <i class="fa-solid fa-arrow-down text-[10px]"></i> {{ $s['receiver_name'] }}
                                        </p>
                                    </div>
                                    <span class="text-xs text-emerald-600 font-semibold shrink-0">Ajouter</span>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                {{-- Package list --}}
                @if ($selectedTrip->packages->isEmpty())
                <div class="flex flex-col items-center py-10 text-center border-2 border-dashed border-slate-200 rounded-2xl">
                    <i class="fa-solid fa-boxes-stacked text-3xl text-slate-300 mb-2"></i>
                    <p class="text-sm text-slate-400">Aucun colis dans ce groupage.</p>
                    @if ($selectedTrip->status === \App\Enums\TripStatus::Planned)
                    <p class="text-xs text-slate-400 mt-1">Recherchez des colis ci-dessus pour les ajouter.</p>
                    @endif
                </div>
                @else
                <div class="divide-y divide-slate-100 rounded-2xl border border-slate-100 overflow-hidden">
                    @foreach ($selectedTrip->packages as $pkg)
                    <div wire:key="gpkg-{{ $pkg->id }}" class="flex items-center gap-3 px-4 py-3 bg-white hover:bg-slate-50 transition-colors">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#0062a3]/10 text-[#0062a3]">
                            <i class="fa-solid fa-box text-xs"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-slate-800">{{ $pkg->tracking_number }}</p>
                            <p class="text-xs text-slate-500 truncate">
                                {{ $pkg->sender?->full_name }} → {{ $pkg->receiver?->full_name }}
                                @if($pkg->package_name)
                                · {{ $pkg->package_name }}
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset {{ $pkg->status->badgeClasses() }} shrink-0">
                            {{ $pkg->status->label() }}
                        </span>
                        @if ($selectedTrip->status === \App\Enums\TripStatus::Planned)
                        <button type="button"
                            wire:click="removePackage({{ $pkg->id }})"
                            wire:confirm="Retirer ce colis du groupage ?"
                            class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-slate-300 hover:bg-rose-50 hover:text-rose-500 transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div class="card-panel flex flex-col items-center py-16 text-center">
                <div class="stat-icon bg-slate-100 text-slate-300 mb-3" style="width:3.5rem;height:3.5rem;border-radius:1rem;">
                    <i class="fa-solid fa-layer-group text-xl"></i>
                </div>
                <p class="text-sm font-medium text-slate-500">Sélectionnez un voyage</p>
                <p class="text-xs text-slate-400 mt-1">Cliquez sur un voyage pour gérer son groupage.</p>
            </div>
            @endif
        </div>
    </div>
</div>
