<div class="space-y-6">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-address-book text-accent mr-2"></i>Clients
            </h1>
            <p class="page-subtitle">Expéditeurs et destinataires centralisés par agence.</p>
        </div>
        <button wire:click="create" class="btn-primary">
            <i class="fa-solid fa-plus"></i>Nouveau client
        </button>
    </div>

    {{-- Form panel --}}
    @if ($showForm)
    <div class="card-panel">
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
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-phone"></i></span>
                        <input wire:model="phone" type="tel" class="form-input pl-10" placeholder="+33 6 00 00 00 00">
                    </div>
                    @error('phone') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Téléphone alternatif</label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-phone"></i></span>
                        <input wire:model="alternative_phone" type="tel" class="form-input pl-10" placeholder="+33 6 00 00 00 00">
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
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-globe"></i></span>
                        <input wire:model="country" type="text" class="form-input pl-10" placeholder="France">
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
    <div class="card-panel p-4">
        <div class="field-wrap">
            <span class="field-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input wire:model.live.debounce.300ms="search" type="search"
                placeholder="Rechercher par nom, téléphone, email…"
                class="form-input pl-10 py-2.5">
        </div>
    </div>

    {{-- Customer cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($customers as $customer)
        <div class="pkg-card flex flex-col gap-3">

            {{-- Avatar + name --}}
            <div class="flex items-center gap-3">
                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-primary text-sm font-bold uppercase text-white">
                    {{ mb_strtoupper(mb_substr($customer->first_name, 0, 1)) }}{{ mb_strtoupper(mb_substr($customer->last_name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="font-semibold text-slate-900 truncate">{{ $customer->full_name }}</p>
                    <p class="text-xs text-slate-400 truncate">
                        @if($customer->city || $customer->country)
                            <i class="fa-solid fa-location-dot mr-1"></i>{{ $customer->city }}{{ $customer->city && $customer->country ? ', ' : '' }}{{ $customer->country }}
                        @else
                            <span class="italic">Localisation non renseignée</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Contact info --}}
            <div class="space-y-1.5 border-t border-slate-100 pt-3">
                <div class="flex items-center gap-2 text-sm text-slate-700">
                    <i class="fa-solid fa-phone text-accent text-xs w-4 text-center"></i>
                    {{ $customer->phone }}
                </div>
                @if($customer->email)
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <i class="fa-solid fa-envelope text-xs w-4 text-center text-slate-400"></i>
                    {{ $customer->email }}
                </div>
                @endif
            </div>

            {{-- Activity + actions --}}
            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                <div class="flex items-center gap-2">
                    <span class="badge-info badge">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>{{ $customer->sent_packages_count }} envoyés
                    </span>
                    <span class="badge-neutral badge">
                        <i class="fa-solid fa-inbox text-[10px]"></i>{{ $customer->received_packages_count }} reçus
                    </span>
                </div>
                <div class="flex items-center gap-1.5">
                    <button wire:click="edit({{ $customer->id }})" class="btn-icon" title="Modifier">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <button wire:click="delete({{ $customer->id }})"
                        wire:confirm="Supprimer ce client ?"
                        class="btn-icon-danger" title="Supprimer">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="sm:col-span-2 xl:col-span-3 card-panel flex flex-col items-center justify-center py-16 text-center">
            <div class="stat-icon bg-slate-100 text-slate-300 mb-4" style="width:3.5rem;height:3.5rem;border-radius:1rem;font-size:1.25rem;">
                <i class="fa-solid fa-address-book"></i>
            </div>
            <p class="text-base font-semibold text-slate-400">Aucun client trouvé.</p>
            <p class="mt-1 text-sm text-slate-300">Créez votre premier client pour commencer.</p>
            <button wire:click="create" class="btn-primary mt-4">
                <i class="fa-solid fa-plus"></i>Nouveau client
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
