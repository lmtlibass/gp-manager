<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <p class="page-eyebrow">Répertoire</p>
                <h1 class="page-heading mt-1">Clients</h1>
                <p class="page-subtitle">Expéditeurs et destinataires centralisés par agence.</p>
            </div>
            <button wire:click="create" class="btn-primary">
                <i class="fa-solid fa-plus"></i>Nouveau client
            </button>
        </div>
    </div>

    {{-- Form panel --}}
    @if ($showForm)
    <div class="card-panel">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
            <div>
                <h2 class="section-title">{{ $editingCustomerId ? 'Modifier le client' : 'Nouveau client' }}</h2>
                <p class="mt-0.5 text-sm text-slate-500">Coordonnées et informations de contact.</p>
            </div>
            <button wire:click="$set('showForm', false)"
                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form wire:submit.prevent="save" class="space-y-5">
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label">Prénom / nom principal</label>
                    <input wire:model="first_name" type="text" class="form-input" placeholder="Jean">
                </div>
                <div>
                    <label class="form-label">Nom complémentaire</label>
                    <input wire:model="last_name" type="text" class="form-input" placeholder="Dupont">
                </div>
                <div>
                    <label class="form-label">Téléphone</label>
                    <input wire:model="phone" type="tel" class="form-input" placeholder="+33 6 00 00 00 00">
                </div>
                <div>
                    <label class="form-label">Téléphone alternatif</label>
                    <input wire:model="alternative_phone" type="tel" class="form-input" placeholder="+33 6 00 00 00 00">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input wire:model="email" type="email" class="form-input" placeholder="jean@exemple.com">
                </div>
                <div>
                    <label class="form-label">Ville</label>
                    <input wire:model="city" type="text" class="form-input" placeholder="Paris">
                </div>
                <div>
                    <label class="form-label">Pays</label>
                    <input wire:model="country" type="text" class="form-input" placeholder="France">
                </div>
                <div class="md:col-span-2">
                    <label class="form-label">Adresse</label>
                    <input wire:model="address" type="text" class="form-input" placeholder="12 rue de la Paix">
                </div>
            </div>
            <div>
                <label class="form-label">Notes</label>
                <textarea wire:model="notes" rows="3" class="form-input"
                    placeholder="Informations complémentaires…"></textarea>
            </div>

            @if ($errors->any())
            <p class="text-sm font-medium text-rose-600">
                <i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $errors->first() }}
            </p>
            @endif

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <button type="button" wire:click="$set('showForm', false)" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Table --}}
    <div class="card-panel space-y-0">

        {{-- Search --}}
        <div class="flex flex-col gap-3 border-b border-slate-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="section-title">Liste des clients</h2>
            <div class="relative max-w-sm">
                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input wire:model.live.debounce.300ms="search" type="search"
                    placeholder="Nom, téléphone, email…"
                    class="form-input pl-10">
            </div>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="table-surface w-full">
                <thead>
                    <tr>
                        <th class="table-header">Client</th>
                        <th class="table-header">Contact</th>
                        <th class="table-header">Activité</th>
                        <th class="table-header text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($customers as $customer)
                    <tr class="table-row-hover">
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-bold uppercase text-slate-600">
                                    {{ mb_strtoupper(mb_substr($customer->first_name, 0, 1)) }}
                                </span>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $customer->full_name }}</div>
                                    <div class="mt-0.5 text-xs text-slate-400">{{ $customer->city }}{{ $customer->city && $customer->country ? ' · ' : '' }}{{ $customer->country }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600">
                            <div>{{ $customer->phone }}</div>
                            <div class="mt-0.5 text-xs text-slate-400">{{ $customer->email ?: '—' }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600">
                            <span class="badge badge-info">{{ $customer->sent_packages_count }} envoyés</span>
                            <span class="ml-2 badge badge-neutral">{{ $customer->received_packages_count }} reçus</span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <button wire:click="edit({{ $customer->id }})"
                                class="font-semibold text-[#0062a3] hover:text-[#004e7b] transition-colors">
                                Modifier
                            </button>
                            <button wire:click="delete({{ $customer->id }})"
                                wire:confirm="Supprimer ce client ?"
                                class="ml-4 font-semibold text-rose-500 hover:text-rose-700 transition-colors">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-14 text-center">
                            <i class="fa-solid fa-address-book text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">Aucun client trouvé.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 border-t border-slate-100 pt-4">
            {{ $customers->links() }}
        </div>

    </div>
</div>
