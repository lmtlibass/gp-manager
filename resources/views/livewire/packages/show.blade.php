<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $package->tracking_number }}</h1>
            <p class="mt-1 text-sm text-gray-600">Code retrait: {{ $package->withdrawal_code }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('packages.edit', $package) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Modifier</a>
            <a href="{{ route('packages.index') }}" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Retour</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Détails colis</h2>
                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $package->status->badgeClasses() }}">{{ $package->status->label() }}</span>
                </div>
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">Nom</dt><dd class="font-medium text-gray-900">{{ $package->package_name }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Agence</dt><dd class="font-medium text-gray-900">{{ $package->agency->name }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Expéditeur</dt><dd class="font-medium text-gray-900">{{ $package->sender->full_name }} · {{ $package->sender->phone }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Destinataire</dt><dd class="font-medium text-gray-900">{{ $package->receiver->full_name }} · {{ $package->receiver->phone }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Trajet</dt><dd class="font-medium text-gray-900">{{ $package->origin_country }} / {{ $package->destination_country }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Poids / quantité</dt><dd class="font-medium text-gray-900">{{ $package->weight ?? '-' }} kg · {{ $package->quantity }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Valeur déclarée</dt><dd class="font-medium text-gray-900">{{ $package->declared_value ?? '-' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">Créé par</dt><dd class="font-medium text-gray-900">{{ $package->creator->first_name ?? 'N/A' }}</dd></div>
                </dl>
                @if ($package->description || $package->notes)
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div><p class="text-sm text-gray-500">Description</p><p class="mt-1 text-sm text-gray-800">{{ $package->description ?: '-' }}</p></div>
                        <div><p class="text-sm text-gray-500">Notes</p><p class="mt-1 text-sm text-gray-800">{{ $package->notes ?: '-' }}</p></div>
                    </div>
                @endif
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="mb-5 font-semibold text-gray-900">Historique</h2>
                <div class="space-y-5">
                    @forelse ($package->histories->sortByDesc('created_at') as $history)
                        <div class="border-l-2 border-blue-200 pl-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $history->old_status ?: 'Création' }} -> {{ $history->new_status }}</p>
                            <p class="text-sm text-gray-600">{{ $history->comment ?: 'Aucun commentaire' }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $history->user->first_name ?? 'Système' }} · {{ $history->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Aucun historique.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <form wire:submit="changeStatus" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 space-y-4">
                <h2 class="font-semibold text-gray-900">Changer le statut</h2>
                <select wire:model="status" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($statuses as $statusOption)
                        <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                    @endforeach
                </select>
                <textarea wire:model="comment" rows="3" placeholder="Commentaire optionnel" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                <button class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Mettre à jour</button>
            </form>

            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h2 class="mb-4 font-semibold text-gray-900">Notifications</h2>
                <div class="space-y-3">
                    @forelse ($package->notifications->sortByDesc('created_at') as $notification)
                        <div class="rounded-md border border-gray-100 p-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900">{{ $notification->channel->label() }}</span>
                                <span class="rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $notification->status->badgeClasses() }}">{{ $notification->status->label() }}</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">{{ $notification->message }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Aucune notification.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
