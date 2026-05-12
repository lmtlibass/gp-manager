<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div>
            <p class="page-eyebrow">Outil</p>
            <h1 class="page-heading mt-1">Recherche rapide</h1>
            <p class="page-subtitle">Tracking, code retrait, téléphone ou nom client.</p>
        </div>
    </div>

    {{-- Search box --}}
    <div class="card-panel">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </span>
            <input wire:model.live.debounce.250ms="search" autofocus type="search"
                placeholder="Saisir un numéro de tracking, un code retrait, un nom ou un téléphone…"
                class="form-input pl-12 py-3.5 text-base">
        </div>
    </div>

    {{-- Results --}}
    @if (strlen($search) > 1)
    <div class="card-panel space-y-0">
        <div class="border-b border-slate-100 pb-4 mb-1">
            <p class="text-sm text-slate-500">
                {{ $packages->total() }} résultat(s) pour
                <span class="font-semibold text-[#001F3D]">« {{ $search }} »</span>
            </p>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($packages as $package)
            <a href="{{ route('packages.show', $package) }}"
                class="flex flex-col gap-3 px-1 py-4 transition-colors hover:bg-slate-50/70 sm:flex-row sm:items-center sm:justify-between rounded-xl">
                <div>
                    <p class="font-semibold text-[#0062a3]">{{ $package->tracking_number }}</p>
                    <p class="mt-0.5 text-sm text-slate-600">
                        <i class="fa-solid fa-key text-[#FF8F1D] mr-1.5 text-xs"></i>{{ $package->withdrawal_code }}
                        <span class="mx-2 text-slate-300">·</span>
                        {{ $package->receiver->full_name }}
                        <span class="mx-2 text-slate-300">·</span>
                        {{ $package->receiver->phone }}
                    </p>
                </div>
                <span class="w-fit inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }} shrink-0">
                    {{ $package->status->label() }}
                </span>
            </a>
            @empty
            <div class="py-12 text-center">
                <i class="fa-solid fa-search text-4xl text-slate-200 mb-3 block"></i>
                <p class="text-sm font-medium text-slate-400">Aucun résultat pour « {{ $search }} ».</p>
                <p class="mt-1 text-xs text-slate-300">Vérifiez l'orthographe ou essayez un autre terme.</p>
            </div>
            @endforelse
        </div>
        @if ($packages->hasPages())
        <div class="border-t border-slate-100 pt-4 mt-2">
            {{ $packages->links() }}
        </div>
        @endif
    </div>
    @endif

</div>
