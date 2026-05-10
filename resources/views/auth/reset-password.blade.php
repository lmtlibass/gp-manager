@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                Définir un nouveau mot de passe
            </h2>
        </div>

        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form class="mt-8 space-y-6" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    Email
                </label>
                <input id="email" name="email" type="email" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror"
                    value="{{ $email ?? old('email') }}" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Nouveau mot de passe
                </label>
                <input id="password" name="password" type="password" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-500 @enderror" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Confirmer le mot de passe
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
            </div>

            <div>
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Réinitialiser le mot de passe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
