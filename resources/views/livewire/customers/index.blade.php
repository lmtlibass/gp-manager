<div class="space-y-6">

    {{-- Page header --}}
    <div class="page-header animate-fade-in">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-address-book text-[#FF8F1D] mr-2" aria-hidden="true"></i>Clients
            </h1>
            <p class="page-subtitle">Expéditeurs et destinataires centralisés par agence.</p>
        </div>
        <button wire:click="create" class="btn-primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>Nouveau client
        </button>
    </div>

    {{-- Form panel --}}
    @if ($showForm)
    <div class="card-panel animate-slide-down">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div class="flex items-center gap-2.5">
                <span class="stat-icon bg-blue-50 text-blue-500" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
<i class="fa-solid {{ $editingId ? 'fa-pen' : 'fa-user-plus' }}"></i>
                </span>
                <div>
<h2 class="section-title">{{ $editingId ? 'Modifier le client' : 'Nouveau client' }}</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Coordonnées et informations de contact</p>
                </div>
            </div>
            <button wire:click="$set('showForm', false)"
                class="btn-icon">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <form wire:submit.prevent="save">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-user text-slate-400 mr-1.5 text-xs"></i>Prénom
                    </label>
                    <input wire:model="first_name" type="text" class="form-input" placeholder="Jean">
                    @error('first_name') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Nom</label>
                    <input wire:model="last_name" type="text" class="form-input" placeholder="Dupont">
                </div>
                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-phone text-slate-400 mr-1.5 text-xs"></i>Téléphone principal
                    </label>
                    <div x-data="phonePicker()" x-modelable="value" wire:model="phone"
                         class="relative" @click.away="open = false">
                        <div class="flex items-stretch rounded-xl border border-slate-200 bg-white shadow-sm transition-all duration-150 hover:border-slate-300 focus-within:border-[#0062a3] focus-within:ring-2 focus-within:ring-[#0062a3]/20">
                            <button type="button" @click="open = !open"
                                    class="flex items-center gap-1.5 px-3 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-slate-100 transition-colors shrink-0 cursor-pointer">
                                <span x-text="selected?.flag" class="text-base leading-none"></span>
                                <span x-text="selected?.dial" class="text-xs font-semibold text-slate-600 min-w-[30px]"></span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-150" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <input x-model="localNumber" @input="_sync()" type="tel"
                                   class="flex-1 min-w-0 rounded-r-xl px-3 py-3 text-sm text-slate-900 bg-transparent outline-none placeholder:text-slate-400"
                                   placeholder="6 00 00 00 00">
                        </div>
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute left-0 top-full z-50 mt-1.5 w-72 rounded-2xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                            <div class="p-2.5 border-b border-slate-100">
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                                    <input x-model="search" type="text" placeholder="Pays ou indicatif…"
                                           class="w-full rounded-xl border border-slate-200 py-2 pl-8 pr-3 text-sm outline-none focus:border-[#0062a3]" @click.stop>
                                </div>
                            </div>
                            <ul class="max-h-52 overflow-y-auto divide-y divide-slate-50 py-1">
                                <template x-for="c in filtered" :key="c.code">
                                    <li><button type="button" @click="selectDialCode(c)"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-[#0062a3]/5 transition-colors">
                                        <span x-text="c.flag" class="text-xl leading-none w-7 text-center shrink-0"></span>
                                        <span x-text="c.name" class="flex-1 text-sm font-medium text-slate-700"></span>
                                        <span x-text="c.dial" class="text-xs font-semibold text-slate-500 font-mono"></span>
                                    </button></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    @error('phone') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Téléphone alternatif</label>
                    <div x-data="phonePicker()" x-modelable="value" wire:model="alternative_phone"
                         class="relative" @click.away="open = false">
                        <div class="flex items-stretch rounded-xl border border-slate-200 bg-white shadow-sm transition-all duration-150 hover:border-slate-300 focus-within:border-[#0062a3] focus-within:ring-2 focus-within:ring-[#0062a3]/20">
                            <button type="button" @click="open = !open"
                                    class="flex items-center gap-1.5 px-3 bg-slate-50 border-r border-slate-200 rounded-l-xl hover:bg-slate-100 transition-colors shrink-0 cursor-pointer">
                                <span x-text="selected?.flag" class="text-base leading-none"></span>
                                <span x-text="selected?.dial" class="text-xs font-semibold text-slate-600 min-w-[30px]"></span>
                                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-150" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <input x-model="localNumber" @input="_sync()" type="tel"
                                   class="flex-1 min-w-0 rounded-r-xl px-3 py-3 text-sm text-slate-900 bg-transparent outline-none placeholder:text-slate-400"
                                   placeholder="6 00 00 00 00">
                        </div>
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute left-0 top-full z-50 mt-1.5 w-72 rounded-2xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                            <div class="p-2.5 border-b border-slate-100">
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-lass text-xs"></i></span>
                                    <input x-model="search" type="text" placeholder="Pays ou indicatif…"
                                           class="w-full rounded-xl border border-slate-200 py-2 pl-8 pr-3 text-sm outline-none focus:border-[#0062a3]" @click.stop>
                                </div>
                            </div>
                            <ul class="max-h-52 overflow-y-auto divide-y divide-slate-50 py-1">
                                <template x-for="c in filtered" :key="c.code">
                                    <li><button type="button" @click="selectDialCode(c)"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-[#0062a3]/5 transition-colors">
                                        <span x-text="c.flag" class="text-xl leading-none w-7 text-center shrink-0"></span>
                                        <span x-text="c.name" class="flex-1 text-sm font-medium text-slate-700"></span>
                                        <span x-text="c.dial" class="text-xs font-semibold text-slate-500 font-mono"></span>
                                    </button></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-envelope text-slate-400 mr-1.5 text-xs"></i>Email
                    </label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-envelope"></i></span>
                        <input wire:model="email" type="email" class="form-input pl-10" placeholder="jean@exemple.com">
                    </div>
                </div>
                <div>
                    <label class="form-label">
                        <i class="fa-solid fa-city text-slate-400 mr-1.5 text-xs"></i>Ville
                    </label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-city"></i></span>
                        <input wire:model="city" type="text" class="form-input pl-10" placeholder="Paris">
                    </div>
                </div>
                <div>
                    <label class="form-label">Pays</label>
                    <div x-data="countryPicker()" x-modelable="value" wire:model="country"
                         class="relative" @click.away="open = false">
                        <button type="button" @click="open = !open"
                            class="form-input w-full flex items-center gap-2.5 text-left cursor-pointer">
                            <span x-show="selected" x-text="selected?.flag" class="text-xl leading-none shrink-0"></span>
                            <span x-show="!selected" class="text-xl leading-none shrink-0 opacity-30">🌍</span>
                            <span class="flex-1 text-sm truncate"
                                  :class="selected ? 'text-slate-900' : 'text-slate-400'"
                                  x-text="selected ? selected.name : 'Choisir un pays…'"></span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-400 shrink-0 transition-transform duration-200"
                               :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             class="absolute inset-x-0 top-full z-50 mt-1.5 rounded-2xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                            <div class="p-2.5 border-b border-slate-100">
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                                    <input x-model="search" type="text" placeholder="Rechercher un pays…"
                                           class="w-full rounded-xl border border-slate-200 py-2 pl-8 pr-3 text-sm outline-none focus:border-[#0062a3] focus:ring-2 focus:ring-[#0062a3]/15" @click.stop>
                                </div>
                            </div>
                            <ul class="max-h-52 overflow-y-auto divide-y divide-slate-50 py-1">
                                <template x-for="c in filtered" :key="c.code">
                                    <li><button type="button" @click="select(c)"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-[#0062a3]/5 transition-colors">
                                        <span x-text="c.flag" class="text-xl leading-none w-7 text-center shrink-0"></span>
                                        <span x-text="c.name" class="flex-1 text-sm font-medium text-slate-700"></span>
                                        <span x-text="c.dial" class="text-xs text-slate-400 font-mono"></span>
                                    </button></li>
                                </template>
                                <li x-show="filtered.length === 0" class="px-4 py-4 text-sm text-slate-400 text-center">Aucun pays trouvé</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Adresse</label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-location-dot"></i></span>
                        <input wire:model="address" type="text" class="form-input pl-10" placeholder="12 rue de la Paix">
                    </div>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="form-label">Notes</label>
                    <textarea wire:model="notes" rows="2" class="form-textarea"
                        placeholder="Informations complémentaires…"></textarea>
                </div>
            </div>

            @if ($errors->any())
            <div class="mt-4 flex items-center gap-2 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i>{{ $errors->first() }}
            </div>
            @endif

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5 mt-5">
                <button type="button" wire:click="$set('showForm', false)" class="btn-secondary">
                    <i class="fa-solid fa-xmark"></i>Annuler
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Search --}}
    <div class="card-panel p-4 animate-fade-in stagger-1">
        <div class="field-wrap">
            <span class="field-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input wire:model.live.debounce.300ms="search" type="search"
                placeholder="Rechercher par nom, téléphone, email…"
                class="form-input pl-10 py-2.5">
        </div>
    </div>

    {{-- Customer cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($customers as $i => $customer)
        <div class="pkg-card flex flex-col gap-3 animate-fade-in-up"
             style="animation-delay: {{ min($i * 0.06, 0.30) }}s">

            {{-- Avatar + name --}}
            <div class="flex items-center gap-3">
                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#001F3D] text-sm font-bold uppercase text-white"
                      aria-hidden="true">
                    {{ mb_strtoupper(mb_substr($customer->first_name, 0, 1)) }}{{ mb_strtoupper(mb_substr($customer->last_name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $customer->full_name }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 truncate">
                        @if($customer->city || $customer->country)
                            <i class="fa-solid fa-location-dot mr-1" aria-hidden="true"></i>{{ $customer->city }}{{ $customer->city && $customer->country ? ', ' : '' }}{{ $customer->country }}
                        @else
                            <span class="italic">Localisation non renseignée</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Contact info --}}
            <div class="space-y-1.5 border-t border-slate-100 dark:border-slate-700 pt-3">
                <div class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                    <i class="fa-solid fa-phone text-[#FF8F1D] text-xs w-4 text-center" aria-hidden="true"></i>
                    {{ $customer->phone }}
                </div>
                @if($customer->email)
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                    <i class="fa-solid fa-envelope text-xs w-4 text-center text-slate-400 dark:text-slate-500" aria-hidden="true"></i>
                    {{ $customer->email }}
                </div>
                @endif
            </div>

            {{-- Activity + actions --}}
            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-3">
                <div class="flex items-center gap-2">
                    <span class="badge-info badge">
                        <i class="fa-solid fa-paper-plane text-[10px]" aria-hidden="true"></i>{{ $customer->sent_packages_count }} envoyés
                    </span>
                    <span class="badge-neutral badge">
                        <i class="fa-solid fa-inbox text-[10px]" aria-hidden="true"></i>{{ $customer->received_packages_count }} reçus
                    </span>
                </div>
                <div class="flex items-center gap-1.5">
                    <button wire:click="edit({{ $customer->id }})" class="btn-icon" title="Modifier ce client"
                            aria-label="Modifier {{ $customer->full_name }}">
                        <i class="fa-solid fa-pen text-xs" aria-hidden="true"></i>
                    </button>
                    <button wire:click="delete({{ $customer->id }})"
                        wire:confirm="Supprimer ce client ?"
                        class="btn-icon-danger" title="Supprimer ce client"
                        aria-label="Supprimer {{ $customer->full_name }}">
                        <i class="fa-solid fa-trash text-xs" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="sm:col-span-2 xl:col-span-3 card-panel flex flex-col items-center justify-center py-16 text-center animate-scale-in">
            <div class="stat-icon bg-slate-100 text-slate-300 mb-4 dark:bg-slate-700 dark:text-slate-600"
                 style="width:3.5rem;height:3.5rem;border-radius:1rem;font-size:1.25rem;"
                 aria-hidden="true">
                <i class="fa-solid fa-address-book"></i>
            </div>
            <p class="text-base font-semibold text-slate-400 dark:text-slate-500">Aucun client trouvé.</p>
            <p class="mt-1 text-sm text-slate-300 dark:text-slate-600">Créez votre premier client pour commencer.</p>
            <button wire:click="create" class="btn-primary mt-4">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>Nouveau client
            </button>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($customers->hasPages())
    <div class="card-panel p-4">
        {{ $customers->links() }}
    </div>
    @endif

</div>
