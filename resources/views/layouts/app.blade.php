<!DOCTYPE html>
@php use Illuminate\Support\Facades\Auth; @endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          mobileOpen: false,
          dark: document.documentElement.classList.contains('dark')
      }"
      :class="dark ? 'dark h-full' : 'h-full'">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#001F3D" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0f172a"  media="(prefers-color-scheme: dark)">
    <title>{{ config('app.name') }} — Gestion Colis</title>

    {{-- Anti-FOUC: apply dark mode before first paint --}}
    <script>
        (function () {
            var s = localStorage.getItem('gp-dark');
            if (s === '1' || (s === null && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-surface text-slate-900 antialiased dark:bg-[#0f172a] dark:text-slate-100">

{{-- Skip to main content (accessibility) --}}
<a href="#main-content" class="skip-link">Aller au contenu principal</a>

<div class="min-h-full">

@if (Auth::check())

{{-- ══ AUTHENTICATED SHELL ══════════════════════════════════════ --}}
<div class="lg:flex lg:min-h-screen">

    {{-- ── SIDEBAR (desktop) ───────────────────────────────────── --}}
    <aside class="hidden w-64 shrink-0 flex-col bg-primary dark:bg-[#070d1a] lg:flex"
           aria-label="Navigation principale">

        {{-- Brand --}}
        <div class="flex h-16 items-center gap-3 border-b border-white/8 px-5">
            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#FF8F1D] font-black text-base text-[#001F3D]"
                  aria-hidden="true">G</span>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold tracking-wide text-white">{{ config('app.name') }}</div>
                <div class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Gestion Colis</div>
            </div>
            {{-- Dark mode toggle --}}
            <button
                @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('gp-dark', dark ? '1' : '0')"
                class="dark-toggle"
                :title="dark ? 'Passer en mode clair ☀️' : 'Passer en mode sombre 🌙'"
                :aria-label="dark ? 'Activer le mode clair' : 'Activer le mode sombre'">
                <i x-show="dark"  class="fa-solid fa-sun  text-amber-300 text-sm"></i>
                <i x-show="!dark" x-cloak class="fa-solid fa-moon text-sm"></i>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-0.5" aria-label="Menu principal">
            <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-600">Menu</p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i></span>
                <span class="flex-1 leading-tight">
                    Accueil
                    <span class="nav-hint">Vue d'ensemble</span>
                </span>
            </a>

            <a href="{{ route('packages.index') }}"
               class="sidebar-link {{ request()->routeIs('packages.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('packages.*') ? 'page' : 'false' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-box-open" aria-hidden="true"></i></span>
                <span class="flex-1 leading-tight">
                    Colis
                    <span class="nav-hint">Enregistrer &amp; suivre</span>
                </span>
            </a>

            <a href="{{ route('customers.index') }}"
               class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('customers.*') ? 'page' : 'false' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-address-book" aria-hidden="true"></i></span>
                <span class="flex-1 leading-tight">
                    Clients
                    <span class="nav-hint">Contacts &amp; coordonnées</span>
                </span>
            </a>

            <a href="{{ route('withdrawals.process') }}"
               class="sidebar-link {{ request()->routeIs('withdrawals.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('withdrawals.*') ? 'page' : 'false' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-hand-holding-box" aria-hidden="true"></i></span>
                <span class="flex-1 leading-tight">
                    Retraits
                    <span class="nav-hint">Remettre un colis</span>
                </span>
            </a>

            <a href="{{ route('search.index') }}"
               class="sidebar-link {{ request()->routeIs('search.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('search.*') ? 'page' : 'false' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                <span class="flex-1 leading-tight">
                    Recherche
                    <span class="nav-hint">Trouver rapidement</span>
                </span>
            </a>

            <a href="{{ route('notifications.index') }}"
               class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('notifications.*') ? 'page' : 'false' }}">
                <span class="sidebar-icon relative"><i class="fa-solid fa-bell" aria-hidden="true"></i></span>
                <span class="flex-1 leading-tight">
                    Notifications
                    <span class="nav-hint">Messages &amp; alertes</span>
                </span>
            </a>

            @can('manage-agency')
            <a href="{{ route('trips.index') }}"
               class="sidebar-link {{ request()->routeIs('trips.*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('trips.*') ? 'page' : 'false' }}">
                <span class="sidebar-icon"><i class="fa-solid fa-truck-fast" aria-hidden="true"></i></span>
                <span class="flex-1 leading-tight">
                    Voyages
                    <span class="nav-hint">Planifier les trajets</span>
                </span>
            </a>
            @endcan

            @can('manage-users')
            <div class="mt-4 pt-4 border-t border-white/8 space-y-0.5">
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.22em] text-slate-600">Administration</p>
                <a href="{{ route('users.index') }}"
                   class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                   aria-current="{{ request()->routeIs('users.*') ? 'page' : 'false' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-users-cog" aria-hidden="true"></i></span>
                    <span class="flex-1 leading-tight">
                        Utilisateurs
                        <span class="nav-hint">Gérer les comptes</span>
                    </span>
                </a>
            </div>
            @endcan
        </nav>

        {{-- Profile --}}
        <div class="border-t border-white/8 p-4">
            <div class="flex items-center gap-3 rounded-xl bg-white/6 px-3 py-2.5">
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#0062a3] text-xs font-bold uppercase text-white"
                      aria-hidden="true">
                    {{ mb_strtoupper(mb_substr(Auth::user()->first_name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-semibold text-white leading-tight">
                        {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                    </div>
                    <div class="truncate text-xs text-slate-500 mt-0.5">{{ Auth::user()->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Se déconnecter"
                        aria-label="Se déconnecter de votre compte"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition-all hover:bg-rose-500/15 hover:text-rose-400">
                        <i class="fa-solid fa-arrow-right-from-bracket text-sm" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- ── MAIN AREA ──────────────────────────────────────────── --}}
    <div class="flex-1 min-w-0">

        {{-- Mobile header --}}
        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200/80 bg-white/95 backdrop-blur-md px-4 py-3 lg:hidden dark:bg-slate-900/95 dark:border-slate-700/80"
                aria-label="En-tête mobile">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[#FF8F1D] font-black text-base text-[#001F3D]"
                      aria-hidden="true">G</span>
                <div>
                    <div class="text-sm font-bold text-[#001F3D] dark:text-white">{{ config('app.name') }}</div>
                    <div class="text-[11px] text-slate-400 dark:text-slate-500">Gestion colis</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Dark toggle mobile --}}
                <button
                    @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('gp-dark', dark ? '1' : '0')"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:hover:bg-slate-700"
                    :aria-label="dark ? 'Mode clair' : 'Mode sombre'">
                    <i x-show="dark"  class="fa-solid fa-sun text-amber-500 text-sm"  aria-hidden="true"></i>
                    <i x-show="!dark" x-cloak class="fa-solid fa-moon text-sm" aria-hidden="true"></i>
                </button>
                {{-- Hamburger --}}
                <button @click="mobileOpen = !mobileOpen" type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                    :aria-expanded="mobileOpen.toString()"
                    aria-label="Ouvrir le menu de navigation">
                    <i x-show="!mobileOpen" class="fa-solid fa-bars text-sm"  aria-hidden="true"></i>
                    <i x-show="mobileOpen"  class="fa-solid fa-xmark text-sm" aria-hidden="true"></i>
                </button>
            </div>
        </header>

        {{-- Mobile dropdown menu --}}
        <div x-cloak x-show="mobileOpen" @click.outside="mobileOpen = false"
            x-transition:enter="transition ease-out duration-180"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-120"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="z-10 lg:hidden"
            role="navigation" aria-label="Menu mobile">
            <div class="border-b border-white/10 bg-[#001F3D] dark:bg-[#070d1a] px-3 py-4 space-y-0.5">

                <a href="{{ route('dashboard') }}" @click="mobileOpen=false"
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i></span>
                    <span class="flex-1 leading-tight">Accueil <span class="nav-hint">Vue d'ensemble</span></span>
                </a>
                <a href="{{ route('packages.index') }}" @click="mobileOpen=false"
                   class="sidebar-link {{ request()->routeIs('packages.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-box-open" aria-hidden="true"></i></span>
                    <span class="flex-1 leading-tight">Colis <span class="nav-hint">Enregistrer &amp; suivre</span></span>
                </a>
                <a href="{{ route('customers.index') }}" @click="mobileOpen=false"
                   class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-address-book" aria-hidden="true"></i></span>
                    <span class="flex-1 leading-tight">Clients <span class="nav-hint">Contacts &amp; coordonnées</span></span>
                </a>
                <a href="{{ route('withdrawals.process') }}" @click="mobileOpen=false"
                   class="sidebar-link {{ request()->routeIs('withdrawals.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-hand-holding-box" aria-hidden="true"></i></span>
                    <span class="flex-1 leading-tight">Retraits <span class="nav-hint">Remettre un colis</span></span>
                </a>
                <a href="{{ route('search.index') }}" @click="mobileOpen=false"
                   class="sidebar-link {{ request()->routeIs('search.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                    <span class="flex-1 leading-tight">Recherche <span class="nav-hint">Trouver rapidement</span></span>
                </a>
                <a href="{{ route('notifications.index') }}" @click="mobileOpen=false"
                   class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-bell" aria-hidden="true"></i></span>
                    <span class="flex-1 leading-tight">Notifications <span class="nav-hint">Messages &amp; alertes</span></span>
                </a>
                @can('manage-agency')
                <a href="{{ route('trips.index') }}" @click="mobileOpen=false"
                   class="sidebar-link {{ request()->routeIs('trips.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-truck-fast" aria-hidden="true"></i></span>
                    <span class="flex-1 leading-tight">Voyages <span class="nav-hint">Planifier les trajets</span></span>
                </a>
                @endcan
                @can('manage-users')
                <a href="{{ route('users.index') }}" @click="mobileOpen=false"
                   class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="fa-solid fa-users-cog" aria-hidden="true"></i></span>
                    <span class="flex-1 leading-tight">Utilisateurs <span class="nav-hint">Gérer les comptes</span></span>
                </a>
                @endcan

                {{-- Profile row in mobile menu --}}
                <div class="mt-3 pt-3 border-t border-white/8 flex items-center justify-between px-1">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#0062a3] text-xs font-bold uppercase text-white">
                            {{ mb_strtoupper(mb_substr(Auth::user()->first_name, 0, 1)) }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-white leading-tight">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                            <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-500/15 px-3 py-1.5 text-xs font-semibold text-rose-400 transition hover:bg-rose-500/25">
                            <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>Déconnecter
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── PAGE CONTENT ──────────────────────────────────── --}}
        <main id="main-content" class="px-4 py-6 sm:px-6 lg:px-8 pb-28 lg:pb-10" tabindex="-1">
            <div class="mx-auto max-w-7xl">

                {{-- Flash: success --}}
                @if ($message = Session::get('success'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 animate-slide-down dark:bg-emerald-900/20 dark:border-emerald-700/40"
                     role="alert" aria-live="polite">
                    <i class="fa-solid fa-circle-check mt-0.5 shrink-0 text-emerald-600 dark:text-emerald-400" aria-hidden="true"></i>
                    <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ $message }}</p>
                </div>
                @endif

                {{-- Flash: error --}}
                @if ($message = Session::get('error'))
                <div class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 animate-slide-down dark:bg-rose-900/20 dark:border-rose-700/40"
                     role="alert" aria-live="assertive">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0 text-rose-600 dark:text-rose-400" aria-hidden="true"></i>
                    <p class="text-sm font-semibold text-rose-800 dark:text-rose-300">{{ $message }}</p>
                </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>
</div>

{{-- ══ MOBILE BOTTOM NAV ════════════════════════════════════════ --}}
<nav class="fixed inset-x-0 bottom-0 z-30 lg:hidden"
     style="padding-bottom: max(0px, env(safe-area-inset-bottom));"
     aria-label="Navigation rapide">
    <div class="bg-white/97 backdrop-blur-2xl border-t border-slate-200/70 shadow-up dark:bg-slate-900/97 dark:border-slate-700/60">
        <div class="flex items-center justify-around px-1 pt-2 pb-2.5">

            <a href="{{ route('withdrawals.process') }}"
               class="mobile-nav-item {{ request()->routeIs('withdrawals.*') ? 'active' : '' }}"
               aria-label="Retraits — Remettre un colis"
               aria-current="{{ request()->routeIs('withdrawals.*') ? 'page' : 'false' }}">
                <span class="mobile-nav-pill"><i class="fa-solid fa-hand-holding-box" aria-hidden="true"></i></span>
                <span class="mobile-nav-label">Retraits</span>
            </a>

            <a href="{{ route('packages.index') }}"
               class="mobile-nav-item {{ request()->routeIs('packages.*') ? 'active' : '' }}"
               aria-label="Colis — Enregistrer et suivre"
               aria-current="{{ request()->routeIs('packages.*') ? 'page' : 'false' }}">
                <span class="mobile-nav-pill"><i class="fa-solid fa-box-open" aria-hidden="true"></i></span>
                <span class="mobile-nav-label">Colis</span>
            </a>

            <a href="{{ route('dashboard') }}"
               class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               aria-label="Accueil — Vue d'ensemble"
               aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                <span class="mobile-nav-pill"><i class="fa-solid fa-house" aria-hidden="true"></i></span>
                <span class="mobile-nav-label">Accueil</span>
            </a>

            <a href="{{ route('customers.index') }}"
               class="mobile-nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}"
               aria-label="Clients — Contacts et coordonnées"
               aria-current="{{ request()->routeIs('customers.*') ? 'page' : 'false' }}">
                <span class="mobile-nav-pill"><i class="fa-solid fa-address-book" aria-hidden="true"></i></span>
                <span class="mobile-nav-label">Clients</span>
            </a>

            <a href="{{ route('notifications.index') }}"
               class="mobile-nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
               aria-label="Notifications — Messages et alertes"
               aria-current="{{ request()->routeIs('notifications.*') ? 'page' : 'false' }}">
                <span class="mobile-nav-pill"><i class="fa-solid fa-bell" aria-hidden="true"></i></span>
                <span class="mobile-nav-label">Alertes</span>
            </a>

        </div>
    </div>
</nav>

@else
{{-- ══ GUEST SHELL ═══════════════════════════════════════════════ --}}
<main class="min-h-screen" id="main-content">
    @yield('content')
</main>
@endif

{{-- Voice Command Assistant --}}
@auth
<livewire:voice-command.assistant />
@endauth

</div>
@livewireScripts
</body>
</html>
