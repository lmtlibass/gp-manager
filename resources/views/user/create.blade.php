@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <p class="page-eyebrow">Administration</p>
                <h1 class="page-heading mt-1">Créer un utilisateur</h1>
                <p class="page-subtitle">Nouveau compte avec rôle et agence.</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>Retour
            </a>
        </div>
    </div>

    @if ($errors->any())
    <div class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4">
        <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-500"></i>
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
            <li class="text-sm font-medium text-rose-700">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Form --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('users.store') }}" class="card-panel space-y-5">
                @csrf

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="form-label">Prénom</label>
                        <input type="text" name="first_name" id="first_name" required
                            class="form-input {{ $errors->has('first_name') ? 'border-rose-400' : '' }}"
                            placeholder="Jean" value="{{ old('first_name') }}" />
                    </div>
                    <div>
                        <label for="last_name" class="form-label">Nom</label>
                        <input type="text" name="last_name" id="last_name" required
                            class="form-input {{ $errors->has('last_name') ? 'border-rose-400' : '' }}"
                            placeholder="Dupont" value="{{ old('last_name') }}" />
                    </div>
                </div>

                <div>
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" name="email" id="email" required
                        class="form-input {{ $errors->has('email') ? 'border-rose-400' : '' }}"
                        placeholder="jean@exemple.com" value="{{ old('email') }}" />
                </div>

                <div>
                    <label for="phone" class="form-label">Téléphone</label>
                    <input type="tel" name="phone" id="phone"
                        class="form-input"
                        placeholder="+33 6 00 00 00 00" value="{{ old('phone') }}" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" name="password" id="password" required
                            class="form-input {{ $errors->has('password') ? 'border-rose-400' : '' }}"
                            placeholder="••••••••" />
                    </div>
                    <div>
                        <label for="password_confirmation" class="form-label">Confirmation</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="form-input" placeholder="••••••••" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="role" class="form-label">Rôle</label>
                        <select name="role" id="role" required class="form-select">
                            <option value="">Sélectionner un rôle</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="gp_manager" {{ old('role') === 'gp_manager' ? 'selected' : '' }}>GP Manager</option>
                            <option value="agent" {{ old('role') === 'agent' ? 'selected' : '' }}>Agent</option>
                        </select>
                    </div>
                    <div>
                        <label for="agency_id" class="form-label">Agence</label>
                        <select name="agency_id" id="agency_id" required class="form-select">
                            <option value="">Sélectionner une agence</option>
                            @foreach ($agencies as $agency)
                            <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>
                                {{ $agency->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <label class="inline-flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                        class="h-4 w-4 rounded border-slate-300 text-[#0062a3] focus:ring-[#0062a3]" />
                    <span class="text-sm font-medium text-slate-700">Compte actif dès la création</span>
                </label>

                <div class="flex gap-3 border-t border-slate-100 pt-5">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-user-plus"></i>Créer l'utilisateur
                    </button>
                    <a href="{{ route('users.index') }}" class="btn-secondary">Annuler</a>
                </div>
            </form>
        </div>

        {{-- Info sidebar --}}
        <div>
            <div class="card-panel">
                <h3 class="section-title mb-4">
                    <i class="fa-solid fa-circle-info text-[#FF8F1D] mr-2"></i>Rôles disponibles
                </h3>
                <div class="space-y-3">
                    <div class="rounded-xl bg-slate-50 p-3">
                        <div class="text-sm font-semibold text-[#001F3D]">Admin</div>
                        <p class="mt-0.5 text-xs text-slate-500">Accès total à toutes les agences et fonctionnalités.</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <div class="text-sm font-semibold text-[#001F3D]">GP Manager</div>
                        <p class="mt-0.5 text-xs text-slate-500">Gestion complète de son agence : colis, clients, utilisateurs.</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <div class="text-sm font-semibold text-[#001F3D]">Agent</div>
                        <p class="mt-0.5 text-xs text-slate-500">Consultation et mise à jour des colis, traitement des retraits.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
