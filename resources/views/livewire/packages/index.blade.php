<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Colis</h1>
            <p class="mt-1 text-sm text-gray-600">Création, suivi et workflow logistique.</p>
        </div>
        <a href="{{ route('packages.create') }}" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Créer un colis</a>
    </div>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
        <div class="grid gap-3 border-b border-gray-100 p-4 md:grid-cols-4">
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Tracking, code, client, téléphone" class="md:col-span-2 rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <select wire:model.live="status" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Tous les statuts</option>
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="perPage" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="10">10 lignes</option>
                <option value="25">25 lignes</option>
                <option value="50">50 lignes</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"><button wire:click="sortBy('tracking_number')">Tracking</button></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Clients</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"><button wire:click="sortBy('destination_country')">Destination</button></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"><button wire:click="sortBy('status')">Statut</button></th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($packages as $package)
                        <tr wire:key="package-{{ $package->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <a href="{{ route('packages.show', $package) }}" class="font-semibold text-blue-700">{{ $package->tracking_number }}</a>
                                <div class="text-xs text-gray-500">Code: {{ $package->withdrawal_code }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">
                                <div>Exp: {{ $package->sender->full_name }}</div>
                                <div>Dest: {{ $package->receiver->full_name }} · {{ $package->receiver->phone }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $package->origin_country }} / {{ $package->destination_country }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $package->status->badgeClasses() }}">{{ $package->status->label() }}</span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm">
                                <a href="{{ route('packages.edit', $package) }}" class="font-medium text-gray-700 hover:text-blue-700">Modifier</a>
                                <button wire:click="delete({{ $package->id }})" wire:confirm="Supprimer ce colis ?" class="ml-3 font-medium text-red-600 hover:text-red-700">Supprimer</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">Aucun colis trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-100 px-4 py-3">
            {{ $packages->links() }}
        </div>
    </div>
</div>
