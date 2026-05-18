<div class="space-y-4">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h1 class="page-heading"><i class="fa-solid fa-plane text-accent mr-2"></i>Voyages</h1>
            <p class="page-subtitle">Planifiez vos prochains voyages et notifiez vos clients.</p>
        </div>
    </div>

    {{-- Form card --}}
    <div class="rounded-2xl border border-slate-200/60 bg-white shadow-card overflow-hidden">

        {{-- Card header --}}
        <div class="flex items-center gap-2.5 px-4 py-4 border-b border-slate-100">
            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-500 text-sm">
                <i class="fa-solid fa-plane-departure"></i>
            </span>
            <h2 class="text-sm font-bold uppercase tracking-widest text-slate-600">Nouveau Voyage</h2>
        </div>

        {{-- Form body --}}
        <div class="px-4 py-4 space-y-4">

            {{-- Départ / Arrivée --}}
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="form-label">Départ *</label>
                    <select class="form-select">
                        <option>Choisir...</option>
                        <option>🇸🇳 Sénégal</option>
                        <option>🇺🇸 États-Unis</option>
                        <option>🇫🇷 France</option>
                        <option>🇨🇦 Canada</option>
                        <option>🇮🇹 Italie</option>
                        <option>🇪🇸 Espagne</option>
                        <option>🌍 Autres</option>
                    </select>
                </div>
                <div class="flex items-center justify-center">
                    <span class="inline-flex items-center gap-2 text-xs font-semibold text-accent">
                        <i class="fa-solid fa-arrow-down sm:hidden"></i>
                        <i class="fa-solid fa-arrow-right hidden sm:inline"></i>
                        <span class="sm:hidden">vers</span>
                    </span>
                </div>
                <div>
                    <label class="form-label">Arrivée *</label>
                    <select class="form-select">
                        <option>Choisir...</option>
                        <option>🇸🇳 Sénégal</option>
                        <option>🇺🇸 États-Unis</option>
                        <option>🇫🇷 France</option>
                        <option>🇨🇦 Canada</option>
                        <option>🇮🇹 Italie</option>
                        <option>🇪🇸 Espagne</option>
                        <option>🌍 Autres</option>
                    </select>
                </div>
            </div>

            {{-- Dates --}}
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="form-label">Date Départ *</label>
                    <input type="date" class="form-input">
                </div>
                <div>
                    <label class="form-label">Date Arrivée</label>
                    <input type="date" class="form-input">
                </div>
                <div>
                    <label class="form-label">
                        <span class="text-accent"><i class="fa-regular fa-clock mr-1"></i>Limite Dépôt</span>
                        <span class="text-slate-400 font-normal text-xs ml-1">(deadline)</span>
                    </label>
                    <input type="date" class="form-input border-accent/50 focus:border-accent focus:ring-accent/20">
                </div>
            </div>

            {{-- Lieux --}}
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="form-label">Lieu Dépôt</label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-box"></i></span>
                        <input type="text" class="form-input pl-10" placeholder="Ex: New York — Queens">
                    </div>
                </div>
                <div>
                    <label class="form-label">Lieu Livraison</label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-location-dot"></i></span>
                        <input type="text" class="form-input pl-10" placeholder="Ex: Dakar — Parcelles">
                    </div>
                </div>
            </div>

            {{-- Capacité & Tarif --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Poids Max <span class="text-slate-400 font-normal text-xs">(opt.)</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-weight-hanging"></i></span>
                        <input type="number" class="form-input pl-10" placeholder="50 kg">
                    </div>
                </div>
                <div>
                    <label class="form-label">Max Colis <span class="text-slate-400 font-normal text-xs">(opt.)</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
                        <input type="number" class="form-input pl-10" placeholder="10">
                    </div>
                </div>
                <div>
                    <label class="form-label">Tarif/kg <span class="text-slate-400 font-normal text-xs">(opt.)</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-coins"></i></span>
                        <input type="number" class="form-input pl-10" placeholder="8">
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="form-label">Notes <span class="text-slate-400 font-normal">(optionnel)</span></label>
                <textarea class="form-textarea" rows="3" placeholder="Conditions, informations supplémentaires..."></textarea>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col gap-2 pt-1">
                <button class="btn-primary w-full py-3">
                    <i class="fa-solid fa-plane"></i>AJOUTER LE VOYAGE
                </button>
                <button class="btn-secondary w-full py-3">
                    Annuler
                </button>
            </div>

        </div>
    </div>

</div>
