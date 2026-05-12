<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div>
            <p class="page-eyebrow">Communication</p>
            <h1 class="page-heading mt-1">Notifications</h1>
            <p class="page-subtitle">Journal des notifications liées aux colis (WhatsApp, SMS).</p>
        </div>
    </div>

    {{-- Add notification --}}
    <div class="card-panel">
        <h2 class="section-title mb-5">
            <i class="fa-solid fa-plus-circle text-[#FF8F1D] mr-2"></i>Nouvelle notification
        </h2>
        <form wire:submit="create" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label class="form-label">Tracking colis</label>
                    <input wire:model="package_tracking" type="text" class="form-input" placeholder="WM-2024-000001">
                </div>
                <div>
                    <label class="form-label">Canal</label>
                    <select wire:model="channel" class="form-select">
                        @foreach ($channels as $channelOption)
                            <option value="{{ $channelOption->value }}">{{ $channelOption->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Téléphone destinataire</label>
                    <input wire:model="recipient_phone" type="tel" class="form-input" placeholder="+33 6 00 00 00 00">
                </div>
                <div>
                    <label class="form-label">Statut initial</label>
                    <select wire:model="status" class="form-select">
                        @foreach ($statuses as $statusOption)
                            <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">
                        <i class="fa-solid fa-paper-plane"></i>Enregistrer
                    </button>
                </div>
            </div>
            <div>
                <label class="form-label">Message</label>
                <textarea wire:model="message" rows="3" class="form-input"
                    placeholder="Votre colis WM-2024-XXXXXX est disponible au retrait…"></textarea>
            </div>
            @if ($errors->any())
            <p class="text-sm font-medium text-rose-600">
                <i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $errors->first() }}
            </p>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="card-panel space-y-0">

        {{-- Search --}}
        <div class="border-b border-slate-100 pb-5">
            <div class="relative max-w-md">
                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input wire:model.live.debounce.300ms="search" type="search"
                    placeholder="Rechercher une notification…"
                    class="form-input pl-10">
            </div>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="table-surface w-full">
                <thead>
                    <tr>
                        <th class="table-header">Colis</th>
                        <th class="table-header">Canal</th>
                        <th class="table-header">Message</th>
                        <th class="table-header">Statut</th>
                        <th class="table-header text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($notifications as $notification)
                    <tr class="table-row-hover">
                        <td class="px-4 py-4">
                            <a href="{{ route('packages.show', $notification->package) }}"
                                class="font-semibold text-[#0062a3] hover:text-[#004e7b]">
                                {{ $notification->package->tracking_number }}
                            </a>
                            <div class="mt-0.5 text-xs text-slate-400">{{ $notification->recipient_phone }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="badge badge-neutral">{{ $notification->channel->label() }}</span>
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600 max-w-xs">
                            <p class="truncate">{{ $notification->message }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $notification->status->badgeClasses() }}">
                                {{ $notification->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm">
                            <button wire:click="markSent({{ $notification->id }})"
                                class="font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                                Envoyée
                            </button>
                            <button wire:click="markFailed({{ $notification->id }})"
                                class="ml-4 font-semibold text-rose-500 hover:text-rose-700 transition-colors">
                                Échec
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-14 text-center">
                            <i class="fa-solid fa-bell-slash text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">Aucune notification enregistrée.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 border-t border-slate-100 pt-4">
            {{ $notifications->links() }}
        </div>

    </div>
</div>
