<!DOCTYPE html>
@php
use Illuminate\Support\Facades\Auth;
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ mobileOpen: false }" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — la Gestion intelligente des colis.</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-9uAr5fSg4z2yG7P2H1V5qBfP2qNQAWLLS7nF3wzd5xl0gNuyXx7e3W8lwW9FKoG8bZq8iwXb7nQPj+PlP60Vkg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-[#f0f4f8] text-slate-900 antialiased">
<div class="min-h-full">

@if (Auth::check())

{{-- ── Authenticated shell ─────────────────────────────────── --}}
<div class="lg:flex lg:min-h-screen">

    {{-- Sidebar --}}
    <aside class="hidden w-64 shrink-0 flex-col bg-[#001F3D] lg:flex">

        {{-- Brand --}}
        <div class="flex h-16 items-center justify-center border-b border-white/8 px-5">
            <img src="{{ asset('images/logo.png') }}" alt="GP221" class="rounded-lg shadow-md ring-2 ring-white/10" style="height: calc(var(--spacing) * 14); width: auto;">
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-0.5">
            <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-500">Menu</p>

            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-chart-pie"></i></span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('packages.index') }}" class="sidebar-link {{ request()->routeIs('packages.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-box-open"></i></span>
                <span>Colis</span>
            </a>
            <a href="{{ route('voyages.index') }}" class="sidebar-link {{ request()->routeIs('voyages.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-plane"></i></span>
                <span>Voyage</span>
            </a>
            <a href="{{ route('customers.index') }}" class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-address-book"></i></span>
                <span>Clients</span>
            </a>
            <a href="{{ route('withdrawals.process') }}" class="sidebar-link {{ request()->routeIs('withdrawals.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-hand-holding"></i></span>
                <span>Retraits</span>
            </a>
            <a href="{{ route('search.index') }}" class="sidebar-link {{ request()->routeIs('search.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <span>Recherche</span>
            </a>
            <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-bell"></i></span>
                <span>Notifications</span>
            </a>

            @can('manage-users')
            <div class="mt-4 pt-4 border-t border-white/8 space-y-0.5">
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-500">Administration</p>
                <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-users-cog"></i></span>
                    <span>Utilisateurs</span>
                </a>
            </div>
            @endcan
        </nav>

        {{-- Profile --}}
        <div class="border-t border-white/8 p-4">
            <div class="flex items-center gap-3 rounded-xl bg-white/6 px-3 py-2.5">
                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#0062a3] text-xs font-bold uppercase text-white">
                    {{ mb_strtoupper(mb_substr(Auth::user()->first_name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-semibold text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                    <div class="truncate text-xs text-slate-400">{{ Auth::user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Déconnexion"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- Main area --}}
    <div class="flex-1 min-w-0">

        {{-- Mobile header --}}
        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white/95 backdrop-blur-md px-4 py-3 lg:hidden">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="GP221" class="h-10 w-auto rounded-lg shadow-md ring-2 ring-white/10">
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('notifications.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:bg-slate-200 relative">
                    <i class="fa-solid fa-bell text-sm"></i>
                </a>
                <button @click="mobileOpen = !mobileOpen" type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:bg-slate-200">
                    <i x-show="!mobileOpen" class="fa-solid fa-bars text-sm"></i>
                    <i x-show="mobileOpen" class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </header>

        {{-- Mobile dropdown menu --}}
        <div x-cloak x-show="mobileOpen" @click.outside="mobileOpen = false"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1"
            class="z-10 lg:hidden">
            <div class="border-b border-white/10 bg-[#001F3D] px-3 py-4 space-y-0.5">
                <a href="{{ route('dashboard') }}" @click="mobileOpen=false" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-chart-pie"></i></span>Dashboard
                </a>
                <a href="{{ route('packages.index') }}" @click="mobileOpen=false" class="sidebar-link {{ request()->routeIs('packages.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-box-open"></i></span>Colis
                </a>
                <a href="{{ route('customers.index') }}" @click="mobileOpen=false" class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-address-book"></i></span>Clients
                </a>
                <a href="{{ route('withdrawals.process') }}" @click="mobileOpen=false" class="sidebar-link {{ request()->routeIs('withdrawals.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-hand-holding-box"></i></span>Retraits
                </a>
                <a href="{{ route('search.index') }}" @click="mobileOpen=false" class="sidebar-link {{ request()->routeIs('search.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-magnifying-glass"></i></span>Recherche
                </a>
                <a href="{{ route('notifications.index') }}" @click="mobileOpen=false" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-bell"></i></span>Notifications
                </a>
                @can('manage-users')
                <a href="{{ route('users.index') }}" @click="mobileOpen=false" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-users-cog"></i></span>Utilisateurs
                </a>
                @endcan
            </div>
        </div>

        {{-- Page --}}
        <main class="px-4 py-6 sm:px-6 lg:px-8 pb-24 lg:pb-8">
            <div class="mx-auto max-w-7xl">

                @if ($message = Session::get('success'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4">
                    <i class="fa-solid fa-circle-check mt-0.5 shrink-0 text-emerald-600"></i>
                    <p class="text-sm font-semibold text-emerald-800">{{ $message }}</p>
                </div>
                @endif

                @if ($message = Session::get('error'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-600"></i>
                    <p class="text-sm font-semibold text-rose-800">{{ $message }}</p>
                </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>
</div>

{{-- Mobile bottom nav --}}
<nav class="fixed inset-x-0 bottom-0 z-30 flex items-center border-t border-slate-200 bg-white/95 backdrop-blur-md px-1 py-1.5 shadow-soft lg:hidden">
    <a href="{{ route('dashboard') }}" class="mobile-bottom-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-pie text-base"></i>
        <span>Accueil</span>
    </a>
    <a href="{{ route('packages.index') }}" class="mobile-bottom-link {{ request()->routeIs('packages.*') ? 'active' : '' }}">
        <i class="fa-solid fa-box-open text-base"></i>
        <span>Colis</span>
    </a>
    <a href="{{ route('withdrawals.process') }}" class="mobile-bottom-link {{ request()->routeIs('withdrawals.*') ? 'active' : '' }}">
        <i class="fa-solid fa-hand-holding text-base"></i>
        <span>Retraits</span>
    </a>
    <a href="{{ route('customers.index') }}" class="mobile-bottom-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
        <i class="fa-solid fa-address-book text-base"></i>
        <span>Clients</span>
    </a>
    <a href="{{ route('voyages.index') }}" class="mobile-bottom-link {{ request()->routeIs('voyages.*') ? 'active' : '' }}">
        <i class="fa-solid fa-plane text-base"></i>
        <span>Voyage</span>
    </a>
</nav>

@else
{{-- Guest --}}
<main class="min-h-screen">
    @yield('content')
</main>
@endif

{{-- Voice Command Assistant — floating widget, visible for authenticated users only --}}
@auth
<livewire:voice-command.assistant />
@endauth

</div>
@livewireScripts
</body>

</html>
