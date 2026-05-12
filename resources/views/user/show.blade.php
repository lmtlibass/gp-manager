@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div class="panel-header">
            <div class="flex items-center gap-4">
                <span class="inline-flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#001F3D] text-xl font-bold uppercase text-white">
                    {{ mb_strtoupper(mb_substr($user->first_name, 0, 1)) }}
                </span>
                <div>
                    <p class="page-eyebrow">Fiche utilisateur</p>
                    <h1 class="page-heading mt-0.5">{{ $user->first_name }} {{ $user->last_name }}</h1>
                    <p class="page-subtitle">{{ $user->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('users.edit', $user) }}" class="btn-primary">
                    <i class="fa-solid fa-pen"></i>Modifier
                </a>
                <form method="POST" action="{{ route('users.destroy', $user) }}"
                    onsubmit="return confirm('Supprimer cet utilisateur définitivement ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <i class="fa-solid fa-trash"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Main info --}}
        <div class="lg:col-span-2 card-panel">
            <h2 class="section-title mb-5">
                <i class="fa-solid fa-id-card text-[#FF8F1D] mr-2"></i>Informations personnelles
            </h2>
            <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Prénom</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $user->first_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Nom</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $user->last_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Email</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Téléphone</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $user->phone ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Status sidebar --}}
        <div class="card-panel space-y-5">
            <h2 class="section-title">
                <i class="fa-solid fa-shield-halved text-[#FF8F1D] mr-2"></i>Statut & Accès
            </h2>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2">Rôle</dt>
                <dd>
                    <span class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset {{ $user->role->badgeClasses() }}">
                        {{ $user->role->label() }}
                    </span>
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2">Agence</dt>
                <dd class="font-medium text-slate-900">{{ $user->agency->name ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2">Statut du compte</dt>
                <dd>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset
                        {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-500/20' : 'bg-rose-50 text-rose-700 ring-rose-500/20' }}">
                        <span class="h-2 w-2 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </dd>
            </div>
        </div>

    </div>

    <div>
        <a href="{{ route('users.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>Retour à la liste
        </a>
    </div>

</div>
@endsection
