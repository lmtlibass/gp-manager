<div class="space-y-6">

    {{-- Page header --}}
    <div class="page-header">
        <div>
            <h1 class="page-heading">
                <i class="fa-solid fa-hand-holding-box text-accent mr-2"></i>Retrait de colis
            </h1>
            <p class="page-subtitle">Validation au guichet par code de retrait.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Search hero --}}
            <div class="card-panel">
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="stat-icon bg-amber-50 text-amber-500 shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                        <i class="fa-solid fa-hashtag"></i>
                    </span>
                    <div>
                        <h2 class="section-title">Code de retrait</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Saisissez le code fourni au client</p>
                    </div>
                </div>
                <form wire:submit="findPackage" class="flex flex-col gap-3 sm:flex-row">
                    <div class="field-wrap flex-1">
                        <span class="field-icon text-lg"><i class="fa-solid fa-hashtag"></i></span>
                        <input wire:model="withdrawal_code"
                            placeholder="EX : RET-00001"
                            class="input-hero pl-14"
                            autocomplete="off" autofocus>
                    </div>
                    <button type="submit" class="btn-accent shrink-0 px-6 text-base" style="padding-top:1rem;padding-bottom:1rem;">
                        <i class="fa-solid fa-magnifying-glass"></i>Rechercher
                    </button>
                </form>
                @error('withdrawal_code')
                <p class="mt-3 flex items-center gap-2 text-sm font-medium text-rose-600">
                    <i class="fa-solid fa-circle-exclamation"></i>{{ $message }}
                </p>
                @enderror
            </div>

            {{-- Package found --}}
            @if ($package)
            <div class="rounded-2xl border-2 border-[#0062a3]/30 bg-blue-50 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="stat-icon bg-[#0062a3] text-white shrink-0" style="width:2.75rem;height:2.75rem;border-radius:0.875rem;">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-[#0062a3] mb-0.5">Colis identifié</p>
                            <p class="text-lg font-bold text-primary">{{ $package->tracking_number }}</p>
                            <p class="text-sm text-slate-600 mt-0.5">
                                {{ $package->package_name }}
                                <span class="mx-1.5 text-slate-300">·</span>
                                {{ $package->receiver->full_name }}
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }} shrink-0">
                        {{ $package->status->label() }}
                    </span>
                </div>
            </div>

            {{-- Withdrawal form --}}
            <div class="card-panel">
                <div class="flex items-center gap-2.5 mb-5">
                    <span class="stat-icon bg-emerald-50 text-emerald-500 shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                        <i class="fa-solid fa-user-check"></i>
                    </span>
                    <div>
                        <h2 class="section-title">Informations du retrait</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Identité de la personne qui récupère le colis</p>
                    </div>
                </div>
                <form wire:submit="validateWithdrawal" class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="form-label">Nom du receveur</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-user"></i></span>
                                <input wire:model="receiver_name" type="text"
                                    class="form-input pl-10" placeholder="Nom complet">
                            </div>
                            @error('receiver_name') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Téléphone</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-phone"></i></span>
                                <input wire:model="receiver_phone" type="tel"
                                    class="form-input pl-10" placeholder="+33 6 00 00 00 00">
                            </div>
                            @error('receiver_phone') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Pièce d'identité</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-id-card"></i></span>
                                <input wire:model="identity_document" type="text"
                                    class="form-input pl-10" placeholder="CNI, Passeport…">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Notes</label>
                            <div class="field-wrap">
                                <span class="field-icon"><i class="fa-solid fa-pen-to-square"></i></span>
                                <input wire:model="notes" type="text"
                                    class="form-input pl-10" placeholder="Remarques éventuelles">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn-success px-6">
                            <i class="fa-solid fa-circle-check"></i>Valider le retrait
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>

        {{-- Recent withdrawals --}}
        <div class="card-panel">
            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-4 mb-4">
                <div class="stat-icon bg-slate-100 text-slate-500 shrink-0" style="width:2.25rem;height:2.25rem;border-radius:0.75rem;font-size:0.875rem;">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h2 class="section-title">Derniers retraits</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Historique du guichet</p>
                </div>
            </div>
            <div class="space-y-0">
                @forelse ($recentWithdrawals as $withdrawal)
                <div class="timeline-item pb-4">
                    <a href="{{ route('packages.show', $withdrawal->package) }}"
                        class="text-sm font-semibold text-[#0062a3] hover:underline">
                        {{ $withdrawal->package->tracking_number }}
                    </a>
                    <p class="mt-0.5 text-sm font-medium text-slate-700">{{ $withdrawal->receiver_name }}</p>
                    <p class="text-xs text-slate-400">
                        <i class="fa-solid fa-phone mr-1"></i>{{ $withdrawal->receiver_phone }}
                    </p>
                    <p class="mt-0.5 text-xs text-slate-400">
                        {{ $withdrawal->validator->first_name ?? 'Agent' }}
                        · {{ $withdrawal->withdrawn_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                @empty
                <div class="flex flex-col items-center py-10 text-center">
                    <div class="stat-icon bg-slate-100 text-slate-300 mb-3" style="width:2.75rem;height:2.75rem;border-radius:0.875rem;">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <p class="text-sm text-slate-400">Aucun retrait récent.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
