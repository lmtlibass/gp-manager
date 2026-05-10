@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Éditer l'utilisateur
        </h1>
    </div>

    @if ($errors->any())
        <div class="rounded-md bg-red-50 p-4 mb-6">
            <div class="flex">
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Il y a des erreurs dans le formulaire
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('user.update', $user) }}" class="space-y-6 px-4 py-5 sm:p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">
                        Prénom
                    </label>
                    <input type="text" name="first_name" id="first_name" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        value="{{ old('first_name', $user->first_name) }}" />
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">
                        Nom
                    </label>
                    <input type="text" name="last_name" id="last_name" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        value="{{ old('last_name', $user->last_name) }}" />
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email
                </label>
                <input type="email" name="email" id="email" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('email', $user->email) }}" />
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">
                    Téléphone
                </label>
                <input type="tel" name="phone" id="phone"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    value="{{ old('phone', $user->phone) }}" />
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">
                    Rôle
                </label>
                <select name="role" id="role" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Sélectionner un rôle</option>
                    <option value="admin" {{ old('role', $user->role->value) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="gp_manager" {{ old('role', $user->role->value) === 'gp_manager' ? 'selected' : '' }}>GP Manager</option>
                    <option value="agent" {{ old('role', $user->role->value) === 'agent' ? 'selected' : '' }}>Agent</option>
                </select>
            </div>

            <div>
                <label for="agency_id" class="block text-sm font-medium text-gray-700">
                    Agence
                </label>
                <select name="agency_id" id="agency_id" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Sélectionner une agence</option>
                    @foreach ($agencies as $agency)
                        <option value="{{ $agency->id }}" {{ old('agency_id', $user->agency_id) == $agency->id ? 'selected' : '' }}>
                            {{ $agency->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Utilisateur actif
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Mettre à jour
                </button>
                <a href="{{ route('user.show', $user) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
