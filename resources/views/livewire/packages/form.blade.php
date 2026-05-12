<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <p class="page-eyebrow">{{ $package?->exists ? 'Édition' : 'Nouveau' }}</p>
                <h1 class="page-heading mt-1">
                    {{ $package?->exists ? 'Modifier le colis' : 'Créer un colis' }}
                </h1>
                <p class="page-subtitle">Les numéros de tracking et de retrait sont générés automatiquement.</p>
            </div>
            <a href="{{ route('packages.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>Retour
            </a>
        </div>
    </div>

    {{-- Form --}}
    <form wire:submit="save">
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Main fields --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Parties --}}
                <div class="card-panel">
                    <h2 class="section-title mb-5">
                        <i class="fa-solid fa-users text-[#FF8F1D] mr-2"></i>Expéditeur & Destinataire
                    </h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="form-label">Expéditeur</label>
                            <select wire:model="sender_id" class="form-select">
                                <option value="">Sélectionner un expéditeur</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->full_name }} · {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                            @error('sender_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Destinataire</label>
                            <select wire:model="receiver_id" class="form-select">
                                <option value="">Sélectionner un destinataire</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->full_name }} · {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                            @error('receiver_id') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Colis details --}}
                <div class="card-panel">
                    <h2 class="section-title mb-5">
                        <i class="fa-solid fa-box text-[#FF8F1D] mr-2"></i>Détails du colis
                    </h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="form-label">Nom du colis</label>
                            <input wire:model="package_name" type="text" class="form-input" placeholder="Ex: Vêtements, Électronique…">
                            @error('package_name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Poids (kg)</label>
                            <input wire:model="weight" type="number" step="0.01" min="0" class="form-input" placeholder="0.00">
                        </div>
                        <div>
                            <label class="form-label">Quantité</label>
                            <input wire:model="quantity" type="number" min="1" class="form-input" placeholder="1">
                        </div>
                        <div>
                            <label class="form-label">Valeur déclarée</label>
                            <input wire:model="declared_value" type="number" step="0.01" min="0" class="form-input" placeholder="0.00">
                        </div>
                        <div>
                            <label class="form-label">Statut</label>
                            <select wire:model="status" class="form-select">
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Pays d'origine</label>
                            <input wire:model="origin_country" type="text" class="form-input" placeholder="France">
                        </div>
                        <div>
                            <label class="form-label">Pays de destination</label>
                            <input wire:model="destination_country" type="text" class="form-input" placeholder="Sénégal">
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="card-panel">
                    <h2 class="section-title mb-5">
                        <i class="fa-solid fa-note-sticky text-[#FF8F1D] mr-2"></i>Description & Notes
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Description</label>
                            <textarea wire:model="description" rows="3" class="form-input" placeholder="Contenu du colis, informations utiles…"></textarea>
                        </div>
                        <div>
                            <label class="form-label">Notes internes</label>
                            <textarea wire:model="notes" rows="3" class="form-input" placeholder="Instructions pour l'équipe…"></textarea>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Side actions --}}
            <div class="space-y-4">
                <div class="card-panel sticky top-6">
                    <h2 class="section-title mb-4">Enregistrement</h2>
                    @if ($package?->exists)
                    <div class="mb-4 rounded-xl bg-slate-50 px-4 py-3 text-sm">
                        <div class="text-xs text-slate-400 uppercase tracking-widest mb-1">Tracking</div>
                        <div class="font-semibold text-[#001F3D]">{{ $package->tracking_number }}</div>
                        <div class="mt-2 text-xs text-slate-400 uppercase tracking-widest">Code retrait</div>
                        <div class="font-semibold text-[#FF8F1D]">{{ $package->withdrawal_code }}</div>
                    </div>
                    @else
                    <p class="mb-4 rounded-xl bg-slate-50 px-4 py-3 text-xs text-slate-500">
                        <i class="fa-solid fa-info-circle text-[#0062a3] mr-1.5"></i>
                        Le numéro de tracking et le code de retrait seront générés automatiquement à l'enregistrement.
                    </p>
                    @endif

                    <div class="space-y-3">
                        <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fa-solid fa-floppy-disk"></i>
                                {{ $package?->exists ? 'Mettre à jour' : 'Créer le colis' }}
                            </span>
                            <span wire:loading class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-notch fa-spin"></i>Enregistrement…
                            </span>
                        </button>
                        <a href="{{ route('packages.index') }}" class="btn-secondary w-full">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>

</div>
