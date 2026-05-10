@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                Réinitialiser le mot de passe
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Entrez votre adresse email pour recevoir un lien de réinitialisation.
            </p>
        </div>

        @if (session('status'))
            <div class="rounded-md bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email
                </label>
                <input id="email" name="email" type="email" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror"
                    value="{{ old('email') }}" />
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Envoyer le lien
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Retour à la connexion
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
