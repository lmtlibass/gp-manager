@php
use Illuminate\Support\Facades\Auth;

$statItems = [
    [
        'label'   => 'Total colis',
        'value'   => $stats['total'],
        'icon'    => 'fa-box',
        'bg'      => 'bg-[#001F3D]',
        'icon_bg' => 'bg-[#FF8F1D]',
        'icon_c'  => 'text-white',
        'val_c'   => 'text-white',
        'lbl_c'   => 'text-slate-300',
    ],
    [
        'label'   => 'Reçus',
        'value'   => $stats['received'],
        'icon'    => 'fa-inbox',
        'bg'      => 'bg-white',
        'icon_bg' => 'bg-blue-100',
        'icon_c'  => 'text-blue-600',
        'val_c'   => 'text-[#001F3D]',
        'lbl_c'   => 'text-slate-500',
    ],
    [
        'label'   => 'En transit',
        'value'   => $stats['in_transit'],
        'icon'    => 'fa-truck-moving',
        'bg'      => 'bg-white',
        'icon_bg' => 'bg-amber-100',
        'icon_c'  => 'text-amber-600',
        'val_c'   => 'text-[#001F3D]',
        'lbl_c'   => 'text-slate-500',
    ],
    [
        'label'   => 'Arrivés',
        'value'   => $stats['arrived'],
        'icon'    => 'fa-map-pin',
        'bg'      => 'bg-white',
        'icon_bg' => 'bg-violet-100',
        'icon_c'  => 'text-violet-600',
        'val_c'   => 'text-[#001F3D]',
        'lbl_c'   => 'text-slate-500',
    ],
    [
        'label'   => 'Prêts',
        'value'   => $stats['ready_for_pickup'],
        'icon'    => 'fa-hand-holding-box',
        'bg'      => 'bg-white',
        'icon_bg' => 'bg-orange-100',
        'icon_c'  => 'text-orange-600',
        'val_c'   => 'text-[#001F3D]',
        'lbl_c'   => 'text-slate-500',
    ],
    [
        'label'   => 'Livrés',
        'value'   => $stats['delivered'],
        'icon'    => 'fa-circle-check',
        'bg'      => 'bg-white',
        'icon_bg' => 'bg-emerald-100',
        'icon_c'  => 'text-emerald-600',
        'val_c'   => 'text-[#001F3D]',
        'lbl_c'   => 'text-slate-500',
    ],
];
@endphp

<div class="space-y-7">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h1 class="page-heading">Tableau de bord</h1>
            <p class="page-subtitle">
                <i class="fa-solid fa-building-columns text-[#FF8F1D] mr-1.5"></i>
                {{ Auth::user()->agency->name ?? 'Toutes agences' }}
                <span class="mx-2 text-slate-300">·</span>
                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
            </p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <select wire:model.live="scope"
                class="form-select max-w-[200px] py-2.5 text-sm">
                <option value="agency">Vue agence</option>
                <option value="mine">Mes colis</option>
            </select>
            <a href="{{ route('packages.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus"></i>Nouveau colis
            </a>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 xl:grid-cols-6">
        @foreach ($statItems as $item)
        <div class="stat-card {{ $item['bg'] }} border-slate-200/60 flex flex-col gap-4">
            <div class="stat-icon {{ $item['icon_bg'] }} {{ $item['icon_c'] }}">
                <i class="fa-solid {{ $item['icon'] }}"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold tracking-tight {{ $item['val_c'] }}">
                    {{ $item['value'] }}
                </p>
                <p class="mt-0.5 text-xs font-medium {{ $item['lbl_c'] }}">
                    {{ $item['label'] }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Main content grid --}}
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Latest packages --}}
        <div class="lg:col-span-2 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-800">
                    <i class="fa-solid fa-boxes-stacked text-[#FF8F1D] mr-2"></i>Derniers colis
                </h2>
                <a href="{{ route('packages.index') }}" class="text-sm font-medium text-[#0062a3] hover:text-[#004e7b] transition-colors">
                    Voir tous <i class="fa-solid fa-arrow-right text-xs ml-1"></i>
                </a>
            </div>

            @forelse ($latestPackages as $package)
            <a href="{{ route('packages.show', $package) }}" class="pkg-card flex items-center gap-4 no-underline block">
                <div class="stat-icon bg-slate-100 text-slate-500 shrink-0">
                    <i class="fa-solid fa-box text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-[#001F3D] text-sm">{{ $package->tracking_number }}</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }}">
                            {{ $package->status->label() }}
                        </span>
                    </div>
                    <div class="mt-0.5 flex flex-wrap items-center gap-x-3 text-xs text-slate-500">
                        <span>{{ optional($package->sender)->full_name ?? '—' }}</span>
                        <i class="fa-solid fa-arrow-right text-[#FF8F1D]"></i>
                        <span class="font-medium text-slate-700">{{ $package->receiver->full_name }}</span>
                        <span class="text-slate-400">{{ $package->destination_country }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-300 shrink-0"></i>
            </a>
            @empty
            <div class="card-panel flex flex-col items-center justify-center py-12 text-center">
                <div class="stat-icon bg-slate-100 text-slate-300 mb-3">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <p class="text-sm text-slate-400">Aucun colis enregistré.</p>
            </div>
            @endforelse
        </div>

        {{-- Activity feed --}}
        <div class="card-panel">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-4 mb-4">
                <div class="stat-icon bg-[#FF8F1D]/10 text-[#FF8F1D] shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Activités récentes</h2>
                    <p class="text-xs text-slate-400">Dernières mises à jour</p>
                </div>
            </div>

            <div class="space-y-0">
                @forelse ($recentHistories as [$package, $history])
                <div class="timeline-item pb-4">
                    <a href="{{ route('packages.show', $package) }}"
                        class="text-sm font-semibold text-[#0062a3] hover:underline">
                        {{ $package->tracking_number }}
                    </a>
                    <p class="mt-0.5 text-xs text-slate-600">
                        <span class="text-slate-400">{{ $history->old_status ?: 'Création' }}</span>
                        <i class="fa-solid fa-arrow-right mx-1 text-[#FF8F1D] text-[10px]"></i>
                        <span class="font-medium text-slate-700">{{ $history->new_status }}</span>
                    </p>
                    <p class="mt-0.5 text-xs text-slate-400">
                        {{ $history->user->first_name ?? 'Système' }} · {{ $history->created_at->format('d/m H:i') }}
                    </p>
                </div>
                @empty
                <div class="flex flex-col items-center py-8 text-center">
                    <i class="fa-solid fa-bell-slash text-2xl text-slate-200 mb-2"></i>
                    <p class="text-sm text-slate-400">Aucune activité récente.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
