<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Recherche rapide</h1>
        <p class="mt-1 text-sm text-gray-600">Tracking, code retrait, téléphone ou nom client.</p>
    </div>

    <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
        <div class="border-b border-gray-100 p-4">
            <input wire:model.live.debounce.250ms="search" autofocus type="search" placeholder="Saisir votre recherche" class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <div class="divide-y divide-gray-100">
            @forelse ($packages as $package)
                <a href="{{ route('packages.show', $package) }}" class="block p-4 hover:bg-gray-50">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-blue-700">{{ $package->tracking_number }}</p>
                            <p class="text-sm text-gray-600">Code {{ $package->withdrawal_code }} · {{ $package->receiver->full_name }} · {{ $package->receiver->phone }}</p>
                        </div>
                        <span class="w-fit rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $package->status->badgeClasses() }}">{{ $package->status->label() }}</span>
                    </div>
                </a>
            @empty
                <p class="p-8 text-center text-sm text-gray-500">Aucun résultat.</p>
            @endforelse
        </div>
        <div class="border-t border-gray-100 px-4 py-3">{{ $packages->links() }}</div>
    </div>
</div>
