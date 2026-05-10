<!DOCTYPE html>
@php
    use Illuminate\Support\Facades\Auth;
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-50">
    @if (Auth::check())
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 gap-6">
                    <div class="flex min-w-0">
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600">
                                {{ config('app.name') }}
                            </a>
                        </div>
                        <div class="hidden lg:ml-8 lg:flex lg:space-x-6">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center border-b-2 {{ request()->routeIs('dashboard') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} px-1 pt-1 text-sm font-medium">Dashboard</a>
                            <a href="{{ route('packages.index') }}" class="inline-flex items-center border-b-2 {{ request()->routeIs('packages.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} px-1 pt-1 text-sm font-medium">Colis</a>
                            <a href="{{ route('customers.index') }}" class="inline-flex items-center border-b-2 {{ request()->routeIs('customers.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} px-1 pt-1 text-sm font-medium">Clients</a>
                            <a href="{{ route('withdrawals.process') }}" class="inline-flex items-center border-b-2 {{ request()->routeIs('withdrawals.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} px-1 pt-1 text-sm font-medium">Retraits</a>
                            <a href="{{ route('search.index') }}" class="inline-flex items-center border-b-2 {{ request()->routeIs('search.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} px-1 pt-1 text-sm font-medium">Recherche</a>
                            <a href="{{ route('notifications.index') }}" class="inline-flex items-center border-b-2 {{ request()->routeIs('notifications.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} px-1 pt-1 text-sm font-medium">Notifications</a>
                            @if (in_array(Auth::user()->role->value, ['admin', 'gp_manager'], true))
                                <a href="{{ route('users.index') }}" class="inline-flex items-center border-b-2 {{ request()->routeIs('users.*') ? 'border-blue-600 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} px-1 pt-1 text-sm font-medium">
                                    Utilisateurs
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center space-x-4">
                        <a href="{{ route('user.profile') }}" class="text-gray-700 hover:text-gray-900">
                            {{ Auth::user()->first_name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    @endif

    <main>
        <!-- Messages de succès/erreur -->
        @if ($message = Session::get('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ $message }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ $message }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>
    @livewireScripts
</body>
</html>
