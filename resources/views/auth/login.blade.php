@extends('layouts.app')

@section('content')
<div class="flex min-h-screen items-center justify-center bg-[#001F3D] px-4 py-12 sm:px-6 lg:px-8">

    {{-- Background decoration --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-[#0062a3]/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 h-80 w-80 rounded-full bg-[#FF8F1D]/10 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-[#FF8F1D] text-[#001F3D] shadow-lg">
                <i class="fa-solid fa-box-open text-2xl"></i>
            </span>
            <h1 class="mt-5 text-3xl font-bold tracking-tight text-white">GP Manager</h1>
            <p class="mt-2 text-sm text-slate-400">Plateforme de gestion logistique de colis</p>
        </div>

        {{-- Card --}}
        <div class="rounded-2xl border border-white/10 bg-white/95 p-8 shadow-2xl backdrop-blur-xl">

            @if ($errors->any())
            <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-500"></i>
                <div class="space-y-0.5">
                    @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium text-rose-700">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            <form class="space-y-5" method="POST" action="{{ route('login.store') }}">
                @csrf

                <div>
                    <label for="email" class="form-label">
                        Adresse email
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </span>
                        <input id="email" name="email" type="email" required autocomplete="email"
                            class="input-field pl-10 {{ $errors->has('email') ? 'border-rose-400 ring-2 ring-rose-200' : '' }}"
                            placeholder="votre@email.com"
                            value="{{ old('email') }}" />
                    </div>
                </div>

                <div>
                    <label for="password" class="form-label">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input id="password" name="password" type="password" required
                            class="input-field pl-10 {{ $errors->has('password') ? 'border-rose-400 ring-2 ring-rose-200' : '' }}"
                            placeholder="••••••••" />
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-[#0062a3] focus:ring-[#0062a3]" />
                        <span class="text-sm text-slate-600">Se souvenir de moi</span>
                    </label>
                    <a href="{{ route('password.request') }}"
                        class="text-sm font-semibold text-[#0062a3] transition hover:text-[#004e7b]">
                        Mot de passe oublié ?
                    </a>
                </div>

                <button type="submit" class="btn-primary w-full mt-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    Se connecter
                </button>
            </form>

            <div class="mt-6 flex items-center justify-center gap-2 border-t border-slate-100 pt-5 text-xs text-slate-400">
                <i class="fa-solid fa-shield-halved text-slate-300"></i>
                Connexion sécurisée — GP Manager
            </div>
        </div>

    </div>
</div>
@endsection
