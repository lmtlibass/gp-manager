@extends('layouts.app')

@section('content')
<div class="space-y-4">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-plane text-accent mr-2"></i>Voyages
            </h1>
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

        <form class="px-4 py-4 space-y-4">

            {{-- Départ / Arrivée --}}
            <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1">
                    <label class="form-label">Départ *</label>
                    <select class="form-select">
                        <option value="">Choisir...</option>
                        <option>🇸🇳 Sénégal</option>
                        <option>🇺🇸 États-Unis</option>
                        <option>🇫🇷 France</option>
                        <option>🇨🇦 Canada</option>
                        <option>🇮🇹 Italie</option>
                        <option>🇪🇸 Espagne</option>
                        <option>🌍 Autres</option>
                    </select>
                </div>
                <div class="flex items-center justify-center sm:mb-1">
                    <i class="fa-solid fa-arrow-down sm:hidden text-accent"></i>
                    <span class="hidden sm:block text-accent font-bold text-xl">→</span>
                </div>
                <div class="flex-1">
                    <label class="form-label">Arrivée *</label>
                    <select class="form-select">
                        <option value="">Choisir...</option>
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
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
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
                        <span class="text-slate-400 font-normal text-xs">(deadline)</span>
                    </label>
                    <input type="date" class="form-input border-accent/50 focus:border-accent focus:ring-accent/20">
                </div>
            </div>

            {{-- Lieux --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Lieu Dépôt</label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-box-open"></i></span>
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
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div>
                    <label class="form-label">Poids Max <span class="font-normal text-slate-400 text-xs">(opt.)</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-weight-hanging"></i></span>
                        <input type="number" step="0.1" min="0" class="form-input pl-10" placeholder="kg">
                    </div>
                </div>
                <div>
                    <label class="form-label">Max Colis <span class="font-normal text-slate-400 text-xs">(opt.)</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-layer-group"></i></span>
                        <input type="number" min="1" class="form-input pl-10" placeholder="0">
                    </div>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="form-label">Tarif/kg <span class="font-normal text-slate-400 text-xs">(opt.)</span></label>
                    <div class="field-wrap">
                        <span class="field-icon"><i class="fa-solid fa-coins"></i></span>
                        <input type="number" step="0.01" min="0" class="form-input pl-10" placeholder="0.00">
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="form-label">Notes <span class="font-normal text-slate-400">(optionnel)</span></label>
                <textarea rows="3" class="form-textarea" placeholder="Conditions, informations supplémentaires..."></textarea>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 pt-1">
                <button type="button" class="btn-secondary w-full sm:w-auto px-8 py-2.5">
                    Annuler
                </button>
                <button type="submit" class="btn-primary w-full sm:w-auto px-8 py-2.5 uppercase tracking-wide text-sm">
                    <i class="fa-solid fa-plane-departure"></i>Ajouter le Voyage
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
