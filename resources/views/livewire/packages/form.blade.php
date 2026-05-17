<div class="space-y-6">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid {{ $package?->exists ? 'fa-pen' : 'fa-plus-circle' }} text-accent mr-2"></i>
                {{ $package?->exists ? 'Modifier le colis' : 'Nouveau colis' }}
            </h1>
            <p class="page-subtitle">Les numéros de tracking et de retrait sont générés automatiquement.</p>
        </div>
        <a href="{{ route('packages.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>Retour
        </a>
    </div>

    <form wire:submit="save">
        <div class="grid gap-6 lg:grid-cols-3">

            {{-- ── Main form ──────────────────────── --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Expéditeur & Destinataire --}}
                <div class="card-panel">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="stat-icon bg-blue-50 text-blue-500" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <h2 class="section-title">Expéditeur &amp; Destinataire</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">
                                <i class="fa-solid fa-user-tag text-slate-400 mr-1.5 text-xs"></i>Expéditeur
                            </label>
                            <select wire:model="sender_id" class="form-select">
                                <option value="">Choisir l'expéditeur…</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->full_name }} · {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                            @error('sender_id')
                            <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">
                                <i class="fa-solid fa-user-check text-slate-400 mr-1.5 text-xs"></i>Destinataire
                            </label>
                            <select wire:model="receiver_id" class="form-select">
                                <option value="">Choisir le destinataire…</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->full_name }} · {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                            @error('receiver_id')
                            <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Détails du colis --}}
                <div class="card-panel">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="stat-icon bg-orange-50 text-orange-500" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                            <i class="fa-solid fa-box"></i>
                        </span>
                        <h2 class="section-title">Détails du colis</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2" x-data="{ packageOther: @entangle('package_name').defer === '' ? false : !['Électroniques','Vêtements','Documents','Boîte','Sacs'].includes(@entangle('package_name').defer) }" x-init="packageOther = $wire.package_name && !['Électroniques','Vêtements','Documents','Boîte','Sacs'].includes($wire.package_name)">
                            <label class="form-label">Nom du colis</label>
                            <select class="form-select"
                                @change="packageOther = $event.target.value === 'Autres'; if ($event.target.value !== 'Autres') $wire.set('package_name', $event.target.value)"
                                x-init="$el.value = ['Électroniques','Vêtements','Documents','Boîte','Sacs'].includes($wire.package_name) ? $wire.package_name : ($wire.package_name ? 'Autres' : '')">
                                <option value="">Choisir le type…</option>
                                <option value="Électroniques">Électroniques</option>
                                <option value="Vêtements">Vêtements</option>
                                <option value="Documents">Documents</option>
                                <option value="Boîte">Boîte</option>
                                <option value="Sacs">Sacs</option>
                                <option value="Autres">Autres</option>
                            </select>
                            <div x-show="packageOther" x-cloak class="mt-2">
                                <div class="field-wrap">
                                    <span class="field-icon"><i class="fa-solid fa-tag"></i></span>
                                    <input wire:model="package_name" type="text"
                                        class="form-input pl-10"
                                        placeholder="Précisez le type de colis…">
                                </div>
                            </div>
                            @error('package_name')
                            <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Poids (kg) <span class="font-normal text-slate-400">(optionnel)</span></label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-weight-hanging"></i></span>
                                <input wire:model="weight" type="number" step="0.01" min="0"
                                    class="form-input pl-10" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Quantité <span class="font-normal text-slate-400">(optionnel)</span></label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-layer-group"></i></span>
                                <input wire:model="quantity" type="number" min="1"
                                    class="form-input pl-10" placeholder="1">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Valeur déclarée <span class="font-normal text-slate-400">(optionnel)</span></label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-coins"></i></span>
                                <input wire:model="declared_value" type="number" step="0.01" min="0"
                                    class="form-input pl-10" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Statut</label>
                            <select wire:model="status" class="form-select">
                                @foreach ($statuses as $statusOption)
                                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-data="{ originOther: false }" x-init="originOther = $wire.origin_country && !['Sénégal','États-Unis','Canada','Italie','Espagne'].includes($wire.origin_country)">
                            <label class="form-label">Pays d'origine</label>
                            <select class="form-select"
                                @change="originOther = $event.target.value === 'Autres'; if ($event.target.value !== 'Autres') $wire.set('origin_country', $event.target.value)"
                                x-init="$el.value = ['Sénégal','États-Unis','Canada','Italie','Espagne'].includes($wire.origin_country) ? $wire.origin_country : ($wire.origin_country ? 'Autres' : '')">
                                <option value="">Choisir le pays…</option>
                                <option value="Sénégal">Sénégal</option>
                                <option value="États-Unis">États-Unis</option>
                                <option value="Canada">Canada</option>
                                <option value="Italie">Italie</option>
                                <option value="Espagne">Espagne</option>
                                <option value="Autres">Autres</option>
                            </select>
                            <div x-show="originOther" x-cloak class="mt-2">
                                <div class="field-wrap">
                                    <span class="field-icon"><i class="fa-solid fa-plane-departure"></i></span>
                                    <input wire:model="origin_country" type="text"
                                        class="form-input pl-10" placeholder="Précisez le pays…">
                                </div>
                            </div>
                        </div>
                        <div x-data="{ destOther: false }" x-init="destOther = $wire.destination_country && !['Sénégal','États-Unis','Canada','Italie','Espagne'].includes($wire.destination_country)">
                            <label class="form-label">Pays de destination</label>
                            <select class="form-select"
                                @change="destOther = $event.target.value === 'Autres'; if ($event.target.value !== 'Autres') $wire.set('destination_country', $event.target.value)"
                                x-init="$el.value = ['Sénégal','États-Unis','Canada','Italie','Espagne'].includes($wire.destination_country) ? $wire.destination_country : ($wire.destination_country ? 'Autres' : '')">
                                <option value="">Choisir le pays…</option>
                                <option value="Sénégal">Sénégal</option>
                                <option value="États-Unis">États-Unis</option>
                                <option value="Canada">Canada</option>
                                <option value="Italie">Italie</option>
                                <option value="Espagne">Espagne</option>
                                <option value="Autres">Autres</option>
                            </select>
                            <div x-show="destOther" x-cloak class="mt-2">
                                <div class="field-wrap">
                                    <span class="field-icon"><i class="fa-solid fa-plane-arrival"></i></span>
                                    <input wire:model="destination_country" type="text"
                                        class="form-input pl-10" placeholder="Précisez le pays…">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description & Notes --}}
                <div class="card-panel">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="stat-icon bg-violet-50 text-violet-500" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                            <i class="fa-solid fa-note-sticky"></i>
                        </span>
                        <h2 class="section-title">Description &amp; Notes</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Description du contenu</label>
                            <textarea wire:model="description" rows="3"
                                class="form-textarea"
                                placeholder="Contenu du colis, informations utiles…"></textarea>
                        </div>
                        <div>
                            <label class="form-label">Notes internes</label>
                            <textarea wire:model="notes" rows="3"
                                class="form-textarea"
                                placeholder="Instructions pour l'équipe…"></textarea>
                        </div>
                        <div x-data="{ preview: null }">
                            <label class="form-label">
                                Photo du colis <span class="font-normal text-slate-400">(optionnel)</span>
                            </label>
                            <input type="file" wire:model="photo" accept="image/*"
                                class="form-input py-2"
                                @change="const f = $event.target.files[0]; preview = f ? URL.createObjectURL(f) : null">
                            <div x-show="preview" x-cloak class="mt-3">
                                <img :src="preview" alt="Aperçu" class="h-40 w-auto rounded-xl border border-slate-200 object-cover">
                            </div>
                            @error('photo')
                            <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Sidebar actions ────────────────── --}}
            <div>
                <div class="card-panel sticky top-6 space-y-4">

                    @if ($package?->exists)
                    <div class="rounded-xl bg-slate-50 border border-slate-200/70 p-4 space-y-3">
                        <div>
                            <p class="detail-label">Numéro de tracking</p>
                            <p class="detail-value text-primary">{{ $package->tracking_number }}</p>
                        </div>
                        <div>
                            <p class="detail-label">Code de retrait</p>
                            <p class="detail-value text-accent font-bold">{{ $package->withdrawal_code }}</p>
                        </div>
                    </div>
                    @else
                    <div class="rounded-xl bg-blue-50 border border-blue-200/70 p-4 flex gap-3">
                        <i class="fa-solid fa-info-circle text-blue-400 mt-0.5 shrink-0"></i>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Le numéro de tracking et le code de retrait seront générés automatiquement à l'enregistrement.
                        </p>
                    </div>
                    @endif

                    <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fa-solid fa-floppy-disk"></i>
                            {{ $package?->exists ? 'Enregistrer les modifications' : 'Créer le colis' }}
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i>Enregistrement…
                        </span>
                    </button>

                    <a href="{{ route('packages.index') }}" class="btn-secondary w-full">
                        <i class="fa-solid fa-xmark"></i>Annuler
                    </a>
                </div>
            </div>

        </div>
    </form>

</div>
