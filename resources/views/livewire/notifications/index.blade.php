<div class="space-y-6">

    {{-- Header --}}
    <div class="card-panel">
        <div>
            <p class="page-eyebrow">Communication</p>
            <h1 class="page-heading mt-1">Notifications</h1>
            <p class="page-subtitle">Envoyez des messages WhatsApp ou SMS aux clients pour leurs colis.</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
        <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
        {{ session('error') }}
    </div>
    @endif

    {{-- Send form --}}
    <div class="card-panel">
        <h2 class="section-title mb-5">
            <i class="fa-brands fa-whatsapp text-[#25D366] mr-2"></i>Nouveau message
        </h2>
        <form wire:submit="send" class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="form-label">Tracking colis</label>
                    <input wire:model="package_tracking" type="text" class="form-input" placeholder="WM-2024-000001">
                    @error('package_tracking')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
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
                    <input wire:model="recipient_phone" type="tel" class="form-input" placeholder="+22900000000">
                    @error('recipient_phone')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="send">
                            <i class="fa-solid fa-paper-plane"></i>Envoyer
                        </span>
                        <span wire:loading wire:target="send" class="flex items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin"></i>Envoi…
                        </span>
                    </button>
                </div>
            </div>
            <div>
                <label class="form-label">Message</label>
                <textarea wire:model="message" rows="3" class="form-input"
                    placeholder="Votre colis WM-2024-XXXXXX est disponible au retrait…"></textarea>
                @error('message')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Quick templates --}}
            <div class="flex flex-wrap gap-2">
                <span class="text-xs text-slate-400 self-center">Modèles :</span>
                <button type="button" wire:click="$set('message', 'Votre colis est arrivé à l\'agence. Vous pouvez venir le retirer.')"
                    class="rounded-full border border-slate-200 px-3 py-1 text-xs text-slate-600 hover:border-[#0062a3] hover:text-[#0062a3] transition-colors">
                    Colis arrivé
                </button>
                <button type="button" wire:click="$set('message', 'Votre colis est prêt pour le retrait. Présentez-vous avec votre code de retrait.')"
                    class="rounded-full border border-slate-200 px-3 py-1 text-xs text-slate-600 hover:border-[#0062a3] hover:text-[#0062a3] transition-colors">
                    Prêt au retrait
                </button>
                <button type="button" wire:click="$set('message', 'Votre colis a été expédié et est en cours d\'acheminement vers notre agence.')"
                    class="rounded-full border border-slate-200 px-3 py-1 text-xs text-slate-600 hover:border-[#0062a3] hover:text-[#0062a3] transition-colors">
                    En transit
                </button>
            </div>
        </form>
    </div>

    {{-- History table --}}
    <div class="card-panel space-y-0">

        <div class="border-b border-slate-100 pb-5">
            <div class="flex items-center justify-between gap-4">
                <h2 class="section-title">
                    <i class="fa-solid fa-clock-rotate-left text-[#0062a3] mr-2"></i>Historique
                </h2>
                <div class="relative max-w-sm w-full">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="search"
                        placeholder="Rechercher…"
                        class="form-input pl-10">
                </div>
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
                            @if ($notification->sent_at)
                                <div class="mt-0.5 text-xs text-slate-400">{{ $notification->sent_at->diffForHumans() }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if ($notification->channel === \App\Enums\NotificationChannel::WhatsApp)
                                <span class="inline-flex items-center gap-1.5 badge badge-neutral">
                                    <i class="fa-brands fa-whatsapp text-[#25D366]"></i>WhatsApp
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 badge badge-neutral">
                                    <i class="fa-solid fa-comment-sms"></i>SMS
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm text-slate-600 max-w-xs">
                            <p class="truncate">{{ $notification->message }}</p>
                            @if ($notification->failed_reason)
                                <p class="mt-0.5 truncate text-xs text-rose-500">{{ $notification->failed_reason }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $notification->status->badgeClasses() }}">
                                {{ $notification->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right text-sm whitespace-nowrap">
                            @if ($notification->status !== \App\Enums\NotificationStatus::Sent)
                                <button wire:click="retry({{ $notification->id }})"
                                    wire:loading.attr="disabled"
                                    class="font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                                    <i class="fa-solid fa-rotate-right mr-1"></i>Renvoyer
                                </button>
                            @endif
                            @if ($notification->status !== \App\Enums\NotificationStatus::Failed)
                                <button wire:click="markFailed({{ $notification->id }})"
                                    class="ml-4 font-semibold text-rose-500 hover:text-rose-700 transition-colors">
                                    Échec
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-14 text-center">
                            <i class="fa-solid fa-bell-slash text-4xl text-slate-200 mb-3 block"></i>
                            <p class="text-sm font-medium text-slate-400">Aucun message envoyé.</p>
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
