<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Retrait colis</h1>
        <p class="mt-1 text-sm text-gray-600">Validation au guichet par code de retrait.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 space-y-5">
            <form wire:submit="findPackage" class="flex flex-col gap-3 sm:flex-row">
                <input wire:model="withdrawal_code" placeholder="Code retrait" class="flex-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Rechercher</button>
            </form>
            @error('withdrawal_code') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            @if ($package)
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $package->tracking_number }}</p>
                            <p class="text-sm text-gray-600">{{ $package->package_name }} · {{ $package->receiver->full_name }}</p>
                        </div>
                        <span class="rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $package->status->badgeClasses() }}">{{ $package->status->label() }}</span>
                    </div>
                </div>

                <form wire:submit="validateWithdrawal" class="grid gap-4 md:grid-cols-2">
                    <input wire:model="receiver_name" placeholder="Nom du receveur" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <input wire:model="receiver_phone" placeholder="Téléphone du receveur" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <input wire:model="identity_document" placeholder="Pièce d'identité" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <input wire:model="notes" placeholder="Notes" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <div class="md:col-span-2 flex justify-end">
                        <button class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Valider le retrait</button>
                    </div>
                </form>
            @endif
        </div>

        <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <h2 class="mb-4 font-semibold text-gray-900">Derniers retraits</h2>
            <div class="space-y-4">
                @forelse ($recentWithdrawals as $withdrawal)
                    <div class="border-l-2 border-emerald-200 pl-4">
                        <p class="text-sm font-semibold text-gray-900">{{ $withdrawal->package->tracking_number }}</p>
                        <p class="text-sm text-gray-600">{{ $withdrawal->receiver_name }} · {{ $withdrawal->receiver_phone }}</p>
                        <p class="text-xs text-gray-400">{{ $withdrawal->validator->first_name ?? 'Agent' }} · {{ $withdrawal->withdrawn_at->format('d/m/Y H:i') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucun retrait récent.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
