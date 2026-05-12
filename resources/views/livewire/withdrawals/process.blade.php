<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div>
            <p class="page-eyebrow">Guichet</p>
            <h1 class="page-heading mt-1">Retrait de colis</h1>
            <p class="page-subtitle">Validation au guichet par code de retrait.</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Main form --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Search by code --}}
            <div class="card-panel">
                <h2 class="section-title mb-4">
                    <i class="fa-solid fa-magnifying-glass text-[#FF8F1D] mr-2"></i>Recherche par code
                </h2>
                <form wire:submit="findPackage" class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-hashtag text-sm"></i>
                        </span>
                        <input wire:model="withdrawal_code"
                            placeholder="Saisir le code de retrait"
                            class="form-input pl-10 text-lg font-semibold tracking-widest uppercase"
                            autocomplete="off" autofocus>
                    </div>
                    <button type="submit" class="btn-primary shrink-0">
                        <i class="fa-solid fa-search"></i>Rechercher
                    </button>
                </form>
                @error('withdrawal_code')
                <p class="mt-2 text-sm font-medium text-rose-600">
                    <i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $message }}
                </p>
                @enderror
            </div>

            {{-- Package found --}}
            @if ($package)
            <div class="card-panel border-[#0062a3]/20 bg-[#f0f7ff]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-[#0062a3] mb-1">Colis identifié</p>
                        <p class="text-xl font-bold text-[#001F3D]">{{ $package->tracking_number }}</p>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ $package->package_name }}
                            <span class="mx-2 text-slate-300">·</span>
                            {{ $package->receiver->full_name }}
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-semibold ring-1 ring-inset {{ $package->status->badgeClasses() }} shrink-0">
                        {{ $package->status->label() }}
                    </span>
                </div>
            </div>

            <div class="card-panel">
                <h2 class="section-title mb-5">
                    <i class="fa-solid fa-user-check text-[#FF8F1D] mr-2"></i>Informations du retrait
                </h2>
                <form wire:submit="validateWithdrawal" class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="form-label">Nom du receveur</label>
                            <input wire:model="receiver_name" type="text"
                                class="form-input" placeholder="Nom complet">
                            @error('receiver_name') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Téléphone du receveur</label>
                            <input wire:model="receiver_phone" type="tel"
                                class="form-input" placeholder="+33 6 00 00 00 00">
                            @error('receiver_phone') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Pièce d'identité</label>
                            <input wire:model="identity_document" type="text"
                                class="form-input" placeholder="CNI, Passeport, etc.">
                        </div>
                        <div>
                            <label class="form-label">Notes</label>
                            <input wire:model="notes" type="text"
                                class="form-input" placeholder="Remarques éventuelles">
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn-success">
                            <i class="fa-solid fa-circle-check"></i>Valider le retrait
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>

        {{-- Recent withdrawals --}}
        <div class="card-panel">
            <h2 class="section-title mb-4">
                <i class="fa-solid fa-clock-rotate-left text-[#FF8F1D] mr-2"></i>Derniers retraits
            </h2>
            <div class="space-y-0">
                @forelse ($recentWithdrawals as $withdrawal)
                <div class="timeline-item pb-4">
                    <a href="{{ route('packages.show', $withdrawal->package) }}"
                        class="text-sm font-semibold text-[#0062a3] hover:underline">
                        {{ $withdrawal->package->tracking_number }}
                    </a>
                    <p class="mt-0.5 text-sm text-slate-700 font-medium">{{ $withdrawal->receiver_name }}</p>
                    <p class="text-xs text-slate-400">{{ $withdrawal->receiver_phone }}</p>
                    <p class="mt-1 text-xs text-slate-400">
                        {{ $withdrawal->validator->first_name ?? 'Agent' }}
                        &middot; {{ $withdrawal->withdrawn_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                @empty
                <div class="py-8 text-center">
                    <i class="fa-solid fa-box-archive text-3xl text-slate-200 mb-2 block"></i>
                    <p class="text-sm text-slate-400">Aucun retrait récent.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
