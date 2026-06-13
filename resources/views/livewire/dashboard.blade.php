@php
use Illuminate\Support\Facades\Auth;

$statItems = [
    [
        'label'   => 'Total colis',
        'value'   => $stats['total'],
        'icon'    => 'fa-box',
        'bg'      => 'bg-[#001F3D] dark:bg-[#002d5a]',
        'icon_bg' => 'bg-[#FF8F1D]',
        'icon_c'  => 'text-white',
        'val_c'   => 'text-white',
        'lbl_c'   => 'text-slate-300',
    ],
    [
        'label'   => 'Reçus',
        'value'   => $stats['received'],
        'icon'    => 'fa-inbox',
        'bg'      => '',
        'icon_bg' => 'bg-blue-100 dark:bg-blue-900/40',
        'icon_c'  => 'text-blue-600 dark:text-blue-400',
        'val_c'   => 'text-[#001F3D] dark:text-white',
        'lbl_c'   => 'text-slate-500 dark:text-slate-400',
    ],
    [
        'label'   => 'En transit',
        'value'   => $stats['in_transit'],
        'icon'    => 'fa-truck-moving',
        'bg'      => '',
        'icon_bg' => 'bg-amber-100 dark:bg-amber-900/40',
        'icon_c'  => 'text-amber-600 dark:text-amber-400',
        'val_c'   => 'text-[#001F3D] dark:text-white',
        'lbl_c'   => 'text-slate-500 dark:text-slate-400',
    ],
    [
        'label'   => 'Arrivés',
        'value'   => $stats['arrived'],
        'icon'    => 'fa-map-pin',
        'bg'      => '',
        'icon_bg' => 'bg-violet-100 dark:bg-violet-900/40',
        'icon_c'  => 'text-violet-600 dark:text-violet-400',
        'val_c'   => 'text-[#001F3D] dark:text-white',
        'lbl_c'   => 'text-slate-500 dark:text-slate-400',
    ],
    [
        'label'   => 'Prêts',
        'value'   => $stats['ready_for_pickup'],
        'icon'    => 'fa-hand-holding-box',
        'bg'      => '',
        'icon_bg' => 'bg-orange-100 dark:bg-orange-900/40',
        'icon_c'  => 'text-orange-600 dark:text-orange-400',
        'val_c'   => 'text-[#001F3D] dark:text-white',
        'lbl_c'   => 'text-slate-500 dark:text-slate-400',
    ],
    [
        'label'   => 'Livrés',
        'value'   => $stats['delivered'],
        'icon'    => 'fa-circle-check',
        'bg'      => '',
        'icon_bg' => 'bg-emerald-100 dark:bg-emerald-900/40',
        'icon_c'  => 'text-emerald-600 dark:text-emerald-400',
        'val_c'   => 'text-[#001F3D] dark:text-white',
        'lbl_c'   => 'text-slate-500 dark:text-slate-400',
    ],
];

$staggerClasses = ['stagger-1','stagger-2','stagger-3','stagger-4','stagger-5','stagger-6'];
@endphp

<div class="space-y-7">

    {{-- Page header --}}
    <div class="page-header animate-fade-in">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-chart-pie text-[#FF8F1D] mr-2" aria-hidden="true"></i>Tableau de bord
            </h1>
            <p class="page-subtitle">
                <i class="fa-solid fa-building-columns text-[#FF8F1D] mr-1.5" aria-hidden="true"></i>
                {{ Auth::user()->agency->name ?? 'Toutes agences' }}
                <span class="mx-2 text-slate-300 dark:text-slate-600">·</span>
                {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
            </p>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <div class="select-wrap max-w-[200px]">
                <select wire:model.live="scope" class="form-select py-2.5 text-sm" aria-label="Scope des statistiques">
                    <option value="agency">Vue agence</option>
                    <option value="mine">Mes colis</option>
                </select>
                <span class="select-chevron"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></span>
            </div>
            <a href="{{ route('packages.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>Nouveau colis
            </a>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="grid gap-4 grid-cols-2 sm:grid-cols-3 xl:grid-cols-6" role="list" aria-label="Statistiques des colis">
        @foreach ($statItems as $i => $item)
        <div class="stat-card {{ $item['bg'] }} animate-fade-in-up {{ $staggerClasses[$i] ?? '' }}"
             role="listitem">
            <div class="stat-icon {{ $item['icon_bg'] }} {{ $item['icon_c'] }}" aria-hidden="true">
                <i class="fa-solid {{ $item['icon'] }}"></i>
            </div>
            <div>
                <p class="text-3xl font-extrabold tracking-tight {{ $item['val_c'] }}" aria-label="{{ $item['value'] }} {{ $item['label'] }}">
                    {{ $item['value'] }}
                </p>
                <p class="mt-0.5 text-xs font-medium {{ $item['lbl_c'] }}">
                    {{ $item['label'] }}
                </p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Main grid --}}
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Latest packages --}}
        <section class="lg:col-span-2 space-y-3" aria-label="Derniers colis enregistrés">
            <div class="flex items-center justify-between animate-fade-in stagger-1">
                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-boxes-stacked text-[#FF8F1D] mr-2" aria-hidden="true"></i>Derniers colis
                </h2>
                <a href="{{ route('packages.index') }}"
                   class="text-sm font-medium text-[#0062a3] hover:text-[#004e7b] transition-colors dark:text-[#4da6ff] dark:hover:text-[#70b8ff]">
                    Voir tous <i class="fa-solid fa-arrow-right text-xs ml-1" aria-hidden="true"></i>
                </a>
            </div>

            @forelse ($latestPackages as $i => $package)
            <a href="{{ route('packages.show', $package) }}"
               class="pkg-card flex items-center gap-4 no-underline block animate-fade-in-up {{ $staggerClasses[$i] ?? 'stagger-6' }}"
               aria-label="Colis {{ $package->tracking_number }}, statut {{ $package->status->label() }}">
                <div class="stat-icon bg-slate-100 text-slate-500 shrink-0 dark:bg-slate-700 dark:text-slate-400"
                     style="width:2.5rem;height:2.5rem;border-radius:0.875rem;font-size:0.875rem;"
                     aria-hidden="true">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-[#001F3D] dark:text-white text-sm">{{ $package->tracking_number }}</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }}">
                            {{ $package->status->label() }}
                        </span>
                    </div>
                    <div class="mt-0.5 flex flex-wrap items-center gap-x-3 text-xs text-slate-500 dark:text-slate-400">
                        <span>{{ optional($package->sender)->full_name ?? '—' }}</span>
                        <i class="fa-solid fa-arrow-right text-[#FF8F1D]" aria-label="vers" aria-hidden="true"></i>
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $package->receiver->full_name }}</span>
                        <span class="text-slate-400">{{ $package->destination_country }}</span>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-xs text-slate-300 shrink-0 dark:text-slate-600" aria-hidden="true"></i>
            </a>
            @empty
            <div class="card-panel flex flex-col items-center justify-center py-14 text-center animate-scale-in">
                <div class="stat-icon bg-slate-100 text-slate-300 mb-4 dark:bg-slate-700 dark:text-slate-600"
                     style="width:3.5rem;height:3.5rem;border-radius:1rem;font-size:1.25rem;"
                     aria-hidden="true">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <p class="text-base font-semibold text-slate-400 dark:text-slate-500">Aucun colis enregistré.</p>
                <p class="mt-1 text-sm text-slate-300 dark:text-slate-600">Créez votre premier colis pour commencer.</p>
                <a href="{{ route('packages.create') }}" class="btn-primary mt-4">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i>Créer un colis
                </a>
            </div>
            @endforelse
        </section>

        {{-- Activity feed --}}
        <section class="card-panel animate-fade-in-up stagger-3" aria-label="Activités récentes">
            <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-700 pb-4 mb-4">
                <div class="stat-icon bg-[#FF8F1D]/10 text-[#FF8F1D] shrink-0"
                     style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;"
                     aria-hidden="true">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Activités récentes</h2>
                    <p class="text-xs text-slate-400">Dernières mises à jour</p>
                </div>
            </div>

            <div class="space-y-0">
                @forelse ($recentHistories as [$package, $history])
                <div class="timeline-item pb-4">
                    <a href="{{ route('packages.show', $package) }}"
                        class="text-sm font-semibold text-[#0062a3] hover:underline dark:text-[#4da6ff]">
                        {{ $package->tracking_number }}
                    </a>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-400">
                        <span class="text-slate-400 dark:text-slate-500">{{ $history->old_status ?: 'Création' }}</span>
                        <i class="fa-solid fa-arrow-right mx-1 text-[#FF8F1D] text-[10px]" aria-label="vers" aria-hidden="true"></i>
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $history->new_status }}</span>
                    </p>
                    <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                        {{ $history->user->first_name ?? 'Système' }} · {{ $history->created_at->format('d/m H:i') }}
                    </p>
                </div>
                @empty
                <div class="flex flex-col items-center py-8 text-center">
                    <i class="fa-solid fa-bell-slash text-2xl text-slate-200 dark:text-slate-700 mb-2" aria-hidden="true"></i>
                    <p class="text-sm text-slate-400 dark:text-slate-500">Aucune activité récente.</p>
                </div>
                @endforelse
            </div>
        </section>

    </div>
</div>
