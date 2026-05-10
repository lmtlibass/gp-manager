@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Gestion des utilisateurs
        </h1>
        <a href="{{ route('user.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Ajouter un utilisateur
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse ($users as $user)
                <li>
                    <a href="{{ route('user.show', $user) }}"
                        class="block hover:bg-gray-50 px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $user->email }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->role->value === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role->value === 'gp_manager' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($user->role->value) }}
                                </span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-4 py-12 sm:px-6 text-center">
                    <p class="text-gray-500">Aucun utilisateur trouvé.</p>
                </li>
            @endforelse
        </ul>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
