<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        <p class="mt-1 text-sm text-gray-600">Journal prêt pour une future intégration WhatsApp.</p>
    </div>

    <form wire:submit="create" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 space-y-4">
        <div class="grid gap-4 md:grid-cols-5">
            <input wire:model="package_tracking" placeholder="Tracking colis" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <select wire:model="channel" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach ($channels as $channelOption)
                    <option value="{{ $channelOption->value }}">{{ $channelOption->label() }}</option>
                @endforeach
            </select>
            <input wire:model="recipient_phone" placeholder="Téléphone" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <select wire:model="status" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @foreach ($statuses as $statusOption)
                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                @endforeach
            </select>
            <button class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Enregistrer</button>
        </div>
        <textarea wire:model="message" rows="3" placeholder="Message" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
        @if ($errors->any())
            <p class="text-sm text-red-600">{{ $errors->first() }}</p>
        @endif
    </form>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
        <div class="border-b border-gray-100 p-4">
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Rechercher notification" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 md:max-w-md">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Colis</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Canal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Message</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($notifications as $notification)
                        <tr>
                            <td class="px-4 py-4 text-sm"><a href="{{ route('packages.show', $notification->package) }}" class="font-semibold text-blue-700">{{ $notification->package->tracking_number }}</a><br>{{ $notification->recipient_phone }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $notification->channel->label() }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $notification->message }}</td>
                            <td class="px-4 py-4"><span class="rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $notification->status->badgeClasses() }}">{{ $notification->status->label() }}</span></td>
                            <td class="px-4 py-4 text-right text-sm">
                                <button wire:click="markSent({{ $notification->id }})" class="font-medium text-emerald-700">Envoyée</button>
                                <button wire:click="markFailed({{ $notification->id }})" class="ml-3 font-medium text-red-600">Échec</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">Aucune notification.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 px-4 py-3">{{ $notifications->links() }}</div>
    </div>
</div>
