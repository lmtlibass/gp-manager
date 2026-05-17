@extends('layouts.app')

@section('content')
<div
    x-data="{ mode: '{{ isset($mode) && $mode === 'register' ? 'register' : (old('_form', 'login')) }}' }"
    class="relative flex min-h-screen items-start justify-center bg-primary px-4 py-10 sm:items-center sm:py-12">

    {{-- Background blobs --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-48 -right-48 h-128 w-lg rounded-full bg-secondary/25 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-accent/15 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">

        {{-- Brand --}}
        <div class="mb-8 text-center">
            <span class="inline-flex h-20 w-20 items-center justify-center rounded-3xl bg-accent shadow-2xl ring-4 ring-white/10">
                <i class="fa-solid fa-box-open text-3xl text-primary"></i>
            </span>
            <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-white">GP Manager</h1>
            <p class="mt-1.5 text-sm text-slate-400">Plateforme de gestion logistique</p>
        </div>

        {{-- Tab toggle --}}
        <div class="mb-5 flex rounded-2xl bg-white/10 p-1">
            <button type="button" @click="mode = 'login'"
                :class="mode === 'login'
                    ? 'bg-white text-primary shadow-sm'
                    : 'text-slate-300 hover:text-white'"
                class="flex-1 rounded-xl py-2.5 text-sm font-semibold transition-all duration-200">
                <i class="fa-solid fa-arrow-right-to-bracket mr-2"></i>Connexion
            </button>
            <button type="button" @click="mode = 'register'"
                :class="mode === 'register'
                    ? 'bg-white text-primary shadow-sm'
                    : 'text-slate-300 hover:text-white'"
                class="flex-1 rounded-xl py-2.5 text-sm font-semibold transition-all duration-200">
                <i class="fa-solid fa-user-plus mr-2"></i>Créer un compte
            </button>
        </div>

        {{-- ══════════════════════════════════════
             CONNEXION
        ══════════════════════════════════════ --}}
        <div x-show="mode === 'login'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="rounded-3xl border border-white/10 bg-white p-8 shadow-2xl">

                @if ($errors->default->any())
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-500"></i>
                    <div>
                        @foreach ($errors->default->all() as $error)
                        <p class="text-sm font-medium text-rose-700">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                @if (session('error'))
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-500"></i>
                    <p class="text-sm font-medium text-rose-700">{{ session('error') }}</p>
                </div>
                @endif

                <form class="space-y-4" method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <input type="hidden" name="_form" value="login">

                    <div>
                        <label for="email" class="form-label">Adresse email</label>
                        <div class="field-wrap">
                            <span class="field-icon"><i class="fa-solid fa-envelope"></i></span>
                            <input id="email" name="email" type="email" required autocomplete="email"
                                class="input-field pl-10"
                                placeholder="votre@email.com"
                                value="{{ old('email') }}" />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="field-wrap">
                            <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
                            <input id="password" name="password" type="password" required
                                class="input-field pl-10"
                                placeholder="••••••••" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-1">
                        <label class="inline-flex cursor-pointer select-none items-center gap-2">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-secondary focus:ring-secondary" />
                            <span class="text-sm text-slate-600">Se souvenir de moi</span>
                        </label>
                        <a href="{{ route('password.request') }}"
                            class="text-sm font-semibold text-secondary transition hover:text-[#004e7b]">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3 text-base">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>Se connecter
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-slate-400">
                    Pas encore de compte ?
                    <button type="button" @click="mode = 'register'"
                        class="font-semibold text-secondary hover:text-[#004e7b] transition">
                        Créer un compte GP Manager
                    </button>
                </p>
            </div>
        </div>

        {{-- ══════════════════════════════════════
             INSCRIPTION
        ══════════════════════════════════════ --}}
        <div x-show="mode === 'register'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-cloak>

            <div class="rounded-3xl border border-white/10 bg-white p-8 shadow-2xl">

                @if ($errors->registerBag->any())
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-500"></i>
                    <div>
                        @foreach ($errors->registerBag->all() as $error)
                        <p class="text-sm font-medium text-rose-700">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                <form class="space-y-6" method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <input type="hidden" name="_form" value="register">

                    {{-- Section agence --}}
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                            <span class="stat-icon bg-blue-50 text-blue-500 shrink-0" style="width:2rem;height:2rem;border-radius:0.625rem;font-size:0.8rem;">
                                <i class="fa-solid fa-building"></i>
                            </span>
                            <p class="text-sm font-semibold text-slate-700">Votre Agence GP</p>
                        </div>

                        <div>
                            <label class="form-label">Nom de l'agence</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-building"></i></span>
                                <input name="agency_name" type="text"
                                    class="input-field pl-10 @error('agency_name', 'registerBag') border-rose-400 @enderror"
                                    placeholder="Ex : GP Express Paris"
                                    value="{{ old('agency_name') }}" />
                            </div>
                            @error('agency_name', 'registerBag')
                            <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-3 grid-cols-2">
                            <div>
                                <label class="form-label">Ville</label>
                                <div class="field-wrap">
                                    <span class="field-icon"><i class="fa-solid fa-city"></i></span>
                                    <input name="agency_city" type="text"
                                        class="input-field pl-10 @error('agency_city', 'registerBag') border-rose-400 @enderror"
                                        placeholder="Paris"
                                        value="{{ old('agency_city') }}" />
                                </div>
                                @error('agency_city', 'registerBag')
                                <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label">Pays</label>
                                <div class="field-wrap">
                                    <span class="field-icon"><i class="fa-solid fa-globe"></i></span>
                                    <input name="agency_country" type="text"
                                        class="input-field pl-10 @error('agency_country', 'registerBag') border-rose-400 @enderror"
                                        placeholder="France"
                                        value="{{ old('agency_country') }}" />
                                </div>
                                @error('agency_country', 'registerBag')
                                <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Téléphone de l'agence</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-phone"></i></span>
                                <input name="agency_phone" type="tel"
                                    class="input-field pl-10 @error('agency_phone', 'registerBag') border-rose-400 @enderror"
                                    placeholder="+33 1 00 00 00 00"
                                    value="{{ old('agency_phone') }}" />
                            </div>
                            @error('agency_phone', 'registerBag')
                            <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Section compte --}}
                    <div class="space-y-3">
                        <div class="grid gap-3 grid-cols-2">
                            <div>
                                <label class="form-label">Prénom</label>
                                <input name="first_name" type="text"
                                    class="input-field @error('first_name', 'registerBag') border-rose-400 @enderror"
                                    placeholder="Jean"
                                    value="{{ old('first_name') }}" />
                                @error('first_name', 'registerBag')
                                <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="form-label">Nom</label>
                                <input name="last_name" type="text"
                                    class="input-field @error('last_name', 'registerBag') border-rose-400 @enderror"
                                    placeholder="Dupont"
                                    value="{{ old('last_name') }}" />
                                @error('last_name', 'registerBag')
                                <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Email</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-envelope"></i></span>
                                <input name="email" type="email"
                                    class="input-field pl-10 @error('email', 'registerBag') border-rose-400 @enderror"
                                    placeholder="jean@agence.com"
                                    value="{{ old('email') }}" />
                            </div>
                            @error('email', 'registerBag')
                            <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Mot de passe</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
                                <input name="password" type="password"
                                    class="input-field pl-10 @error('password', 'registerBag') border-rose-400 @enderror"
                                    placeholder="••••••••  (8 caractères min.)" />
                            </div>
                            @error('password', 'registerBag')
                            <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label">Confirmer le mot de passe</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-lock"></i></span>
                                <input name="password_confirmation" type="password"
                                    class="input-field pl-10"
                                    placeholder="••••••••" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-accent w-full py-3 text-base">
                        <i class="fa-solid fa-rocket"></i>Créer mon compte GP Manager
                    </button>
                </form>

                <p class="mt-5 text-center text-sm text-slate-400">
                    Déjà un compte ?
                    <button type="button" @click="mode = 'login'"
                        class="font-semibold text-secondary hover:text-[#004e7b] transition">
                        Se connecter
                    </button>
                </p>
            </div>
        </div>

        <div class="mt-5 flex items-center justify-center gap-2 text-xs text-slate-500">
            <i class="fa-solid fa-shield-halved text-slate-400"></i>
            Connexion sécurisée — GP Manager
        </div>

    </div>
</div>
@endsection
