@php
use Illuminate\Support\Facades\Auth;

$statItems = [
    ['label' => 'Total colis',    'value' => $stats['total'],            'icon' => 'fa-box',             'accent' => '#FF8F1D', 'dark' => true],
    ['label' => 'Reçus',          'value' => $stats['received'],         'icon' => 'fa-inbox',           'accent' => '#0062a3', 'dark' => false],
    ['label' => 'En transit',     'value' => $stats['in_transit'],       'icon' => 'fa-truck-moving',    'accent' => '#FF8F1D', 'dark' => false],
    ['label' => 'Arrivés',        'value' => $stats['arrived'],          'icon' => 'fa-map-pin',         'accent' => '#0062a3', 'dark' => false],
    ['label' => 'Prêts',          'value' => $stats['ready_for_pickup'], 'icon' => 'fa-hand-holding-box','accent' => '#7c3aed', 'dark' => false],
    ['label' => 'Livrés',         'value' => $stats['delivered'],        'icon' => 'fa-circle-check',    'accent' => '#166534', 'dark' => false],
];
@endphp

<div class="space-y-6">

    {{-- Page header --}}
    <div class="card-panel">
        <div class="panel-header">
            <div>
                <p class="page-eyebrow">Tableau de bord</p>
                <h1 class="page-heading mt-1">Performance logistique</h1>
                <p class="page-subtitle">
                    <i class="fa-solid fa-building-columns text-[#FF8F1D] mr-1.5"></i>
                    {{ Auth::user()->agency->name ?? 'Toutes agences' }}
                    <span class="mx-2 text-slate-300">·</span>
                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                </p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <select wire:model.live="scope"
                    class="form-select rounded-xl border-slate-200 text-sm max-w-[180px]">
                    <option value="agency">Vue agence</option>
                    <option value="mine">Mes colis</option>
                </select>
                <a href="{{ route('packages.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus"></i>Créer un colis
                </a>
            </div>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
        @foreach ($statItems as $item)
        <div class="stat-card {{ $item['dark'] ? 'bg-[#001F3D] border-[#001F3D]' : '' }} flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl {{ $item['dark'] ? 'bg-white/10' : 'bg-slate-50' }}">
                    <i class="fa-solid {{ $item['icon'] }} text-sm" style="color: {{ $item['accent'] }}"></i>
                </span>
                <span class="text-xs font-semibold uppercase tracking-[0.18em] {{ $item['dark'] ? 'text-slate-400' : 'text-slate-400' }}">
                    {{ $item['label'] }}
                </span>
            </div>
            <p class="text-4xl font-bold tracking-tight {{ $item['dark'] ? 'text-white' : 'text-[#001F3D]' }}">
                {{ $item['value'] }}
            </p>
        </div>
        @endforeach
    </div>

    {{-- Main content grid --}}
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Latest packages table --}}
        <div class="lg:col-span-2 card-panel">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h2 class="section-title">Derniers colis</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Mouvements récents de votre agence.</p>
                </div>
                <a href="{{ route('packages.index') }}" class="btn-secondary text-xs">
                    Voir tous <i class="fa-solid fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="table-surface w-full">
                    <thead>
                        <tr>
                            <th class="table-header rounded-tl-xl">Numéro</th>
                            <th class="table-header">Destinataire</th>
                            <th class="table-header">Destination</th>
                            <th class="table-header rounded-tr-xl">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestPackages as $package)
                        <tr class="table-row-hover">
                            <td class="px-4 py-3.5">
                                <a href="{{ route('packages.show', $package) }}"
                                    class="font-semibold text-[#0062a3] hover:text-[#004e7b] transition-colors">
                                    {{ $package->tracking_number }}
                                </a>
                                <div class="mt-0.5 text-xs text-slate-400">{{ optional($package->sender)->full_name ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-slate-700">
                                {{ $package->receiver->full_name }}
                                <div class="text-xs text-slate-400">{{ $package->receiver->phone }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-sm text-slate-600">{{ $package->destination_country }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }}">
                                    {{ $package->status->label() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center">
                                <i class="fa-solid fa-box-open text-3xl text-slate-200 block mb-2"></i>
                                <span class="text-sm text-slate-400">Aucun colis enregistré.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Activity feed --}}
        <div class="card-panel">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="section-title">Activités récentes</h2>
                <p class="mt-0.5 text-sm text-slate-500">Dernières mises à jour de statut.</p>
            </div>
            <div class="mt-4 space-y-0">
                @forelse ($recentHistories as [$package, $history])
                <div class="timeline-item pb-4">
                    <a href="{{ route('packages.show', $package) }}"
                        class="text-sm font-semibold text-[#0062a3] hover:underline">
                        {{ $package->tracking_number }}
                    </a>
                    <p class="mt-0.5 text-sm text-slate-600">
                        {{ $history->old_status ?: 'Création' }}
                        <i class="fa-solid fa-arrow-right mx-1 text-[#FF8F1D] text-xs"></i>
                        {{ $history->new_status }}
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        {{ $history->user->first_name ?? 'Système' }} &middot;
                        {{ $history->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                @empty
                <p class="py-6 text-center text-sm text-slate-400">Aucune activité récente.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
