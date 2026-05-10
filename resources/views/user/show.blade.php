@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            {{ $user->first_name }} {{ $user->last_name }}
        </h1>
        <div class="flex gap-2">
            <a href="{{ route('user.edit', $user) }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                Éditer
            </a>
            <form method="POST" action="{{ route('user.destroy', $user) }}" onsubmit="return confirm('Êtes-vous sûr ?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Informations personnelles
                    </h2>
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Prénom</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->first_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nom</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->last_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->phone ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        Statut
                    </h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rôle</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->role->value === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role->value === 'gp_manager' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($user->role->value) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Agence</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->agency->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Statut</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('user.index') }}"
            class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Retour à la liste
        </a>
    </div>
</div>
@endsection
