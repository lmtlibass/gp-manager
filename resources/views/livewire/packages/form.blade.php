<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $package?->exists ? 'Modifier le colis' : 'Créer un colis' }}</h1>
            <p class="mt-1 text-sm text-gray-600">Les numéros de tracking et de retrait sont générés automatiquement.</p>
        </div>
        <a href="{{ route('packages.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Retour</a>
    </div>

    <form wire:submit="save" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 space-y-6">
        <div class="grid gap-5 md:grid-cols-2">
            <label class="block">
                <span class="text-sm font-medium text-gray-700">Expéditeur</span>
                <select wire:model="sender_id" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Choisir</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->full_name }} · {{ $customer->phone }}</option>
                    @endforeach
                </select>
                @error('sender_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-gray-700">Destinataire</span>
                <select wire:model="receiver_id" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Choisir</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->full_name }} · {{ $customer->phone }}</option>
                    @endforeach
                </select>
                @error('receiver_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-gray-700">Nom du colis</span>
                <input wire:model="package_name" type="text" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('package_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-gray-700">Statut</span>
                <select wire:model="status" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-medium text-gray-700">Poids</span>
                <input wire:model="weight" type="number" step="0.01" min="0" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-gray-700">Quantité</span>
                <input wire:model="quantity" type="number" min="1" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-gray-700">Valeur déclarée</span>
                <input wire:model="declared_value" type="number" step="0.01" min="0" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-gray-700">Pays origine</span>
                <input wire:model="origin_country" type="text" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-gray-700">Pays destination</span>
                <input wire:model="destination_country" type="text" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </label>
        </div>

        <label class="block">
            <span class="text-sm font-medium text-gray-700">Description</span>
            <textarea wire:model="description" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </label>
        <label class="block">
            <span class="text-sm font-medium text-gray-700">Notes internes</span>
            <textarea wire:model="notes" rows="3" class="mt-1 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        </label>

        <div class="flex justify-end gap-3">
            <a href="{{ route('packages.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700" wire:loading.attr="disabled">
                <span wire:loading.remove>Enregistrer</span>
                <span wire:loading>Enregistrement...</span>
            </button>
        </div>
    </form>
</div>
