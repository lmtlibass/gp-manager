<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
            <p class="mt-1 text-sm text-gray-600">Expéditeurs et destinataires partagent le même annuaire.</p>
        </div>
        <button wire:click="create" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Nouveau client</button>
    </div>

    @if ($showForm)
        <form wire:submit="save" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 space-y-5">
            <div class="grid gap-4 md:grid-cols-3">
                <input wire:model="first_name" placeholder="Prénom / nom principal" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input wire:model="last_name" placeholder="Nom complémentaire" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input wire:model="phone" placeholder="Téléphone" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input wire:model="alternative_phone" placeholder="Téléphone alternatif" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input wire:model="email" type="email" placeholder="Email" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input wire:model="city" placeholder="Ville" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input wire:model="country" placeholder="Pays" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input wire:model="address" placeholder="Adresse" class="md:col-span-2 rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <textarea wire:model="notes" rows="2" placeholder="Notes" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            @if ($errors->any())
                <p class="text-sm text-red-600">{{ $errors->first() }}</p>
            @endif
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="$set('showForm', false)" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Annuler</button>
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Enregistrer</button>
            </div>
        </form>
    @endif

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
        <div class="border-b border-gray-100 p-4">
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher nom, téléphone ou email" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 md:max-w-md">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Client</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Colis</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($customers as $customer)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">{{ $customer->full_name }}</div>
                                <div class="text-sm text-gray-500">{{ $customer->city }} {{ $customer->country }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $customer->phone }}<br>{{ $customer->email }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">Envoyés: {{ $customer->sent_packages_count }} · Reçus: {{ $customer->received_packages_count }}</td>
                            <td class="px-4 py-4 text-right text-sm">
                                <button wire:click="edit({{ $customer->id }})" class="font-medium text-blue-700">Modifier</button>
                                <button wire:click="delete({{ $customer->id }})" wire:confirm="Supprimer ce client ?" class="ml-3 font-medium text-red-600">Supprimer</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500">Aucun client trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-4 py-3">{{ $customers->links() }}</div>
    </div>
</div>
