@php
    use Illuminate\Support\Facades\Auth;
    $cards = [
        ['label' => 'Total colis', 'value' => $stats['total'], 'class' => 'border-gray-200'],
        ['label' => 'Reçus', 'value' => $stats['received'], 'class' => 'border-blue-200'],
        ['label' => 'En transit', 'value' => $stats['in_transit'], 'class' => 'border-amber-200'],
        ['label' => 'Arrivés', 'value' => $stats['arrived'], 'class' => 'border-indigo-200'],
        ['label' => 'Prêts', 'value' => $stats['ready_for_pickup'], 'class' => 'border-purple-200'],
        ['label' => 'Livrés', 'value' => $stats['delivered'], 'class' => 'border-emerald-200'],
    ];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600">{{ Auth::user()->agency->name ?? 'Toutes agences' }} · {{ Auth::user()->first_name }}</p>
        </div>
        <select wire:model.live="scope" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="agency">Agence</option>
            <option value="mine">Mes colis</option>
        </select>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
        @foreach ($cards as $card)
            <div class="rounded-lg border {{ $card['class'] }} bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-gray-500">{{ $card['label'] }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h2 class="font-semibold text-gray-900">Derniers colis</h2>
                <a href="{{ route('packages.create') }}" class="rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Nouveau colis</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($latestPackages as $package)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <a href="{{ route('packages.show', $package) }}" class="font-semibold text-blue-700">{{ $package->tracking_number }}</a>
                                    <div class="text-sm text-gray-500">{{ $package->receiver->full_name }} · {{ $package->receiver->phone }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">{{ $package->destination_country }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $package->status->badgeClasses() }}">{{ $package->status->label() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-5 py-8 text-center text-sm text-gray-500">Aucun colis enregistré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="font-semibold text-gray-900">Activités récentes</h2>
            </div>
            <div class="space-y-4 p-5">
                @forelse ($recentHistories as [$package, $history])
                    <div class="border-l-2 border-blue-200 pl-4">
                        <p class="text-sm font-medium text-gray-900">{{ $package->tracking_number }}</p>
                        <p class="text-sm text-gray-600">{{ $history->old_status ?: 'Création' }} -> {{ $history->new_status }}</p>
                        <p class="text-xs text-gray-400">{{ $history->user->first_name ?? 'Système' }} · {{ $history->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Aucune activité récente.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
