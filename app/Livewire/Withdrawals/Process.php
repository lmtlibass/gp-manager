<?php

namespace App\Livewire\Withdrawals;

use App\Enums\PackageStatus;
use App\Enums\VoiceIntentEnum;
use App\Models\Package;
use App\Models\Withdrawal;
use App\Services\PackageNotifier;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Process extends Component
{
    use AuthorizesRequests;

    public string $search = '';
    public array $suggestions = [];
    public bool $showSuggestions = false;

    public ?Package $package = null;
    public string $receiver_name = '';
    public string $receiver_phone = '';
    public string $identity_document = '';
    public string $notes = '';

    public function mount(): void
    {
        $this->authorize('create', Withdrawal::class);

        $key = VoiceIntentEnum::PackageWithdrawal->sessionKey();
        if ($voiceData = session()->pull($key)) {
            $this->search         = $voiceData['withdrawal_code'] ?? '';
            $this->receiver_name  = $voiceData['receiver_name']  ?? '';
            $this->receiver_phone = $voiceData['receiver_phone'] ?? '';

            if ($this->search) {
                $this->findPackage();
            }
        }
    }

    #[On('voice-command-applied')]
    public function onVoiceCommandApplied(string $intent, array $data): void
    {
        if ($intent === VoiceIntentEnum::PackageWithdrawal->value) {
            $this->search         = $data['withdrawal_code'] ?? $this->search;
            $this->receiver_name  = $data['receiver_name']  ?? $this->receiver_name;
            $this->receiver_phone = $data['receiver_phone'] ?? $this->receiver_phone;
        }
    }

    public function updatedSearch(): void
    {
        $this->package = null;
        $this->suggestions = [];
        $this->showSuggestions = false;

        $term = trim($this->search);

        if (mb_strlen($term) < 2) {
            return;
        }

        $this->suggestions = Package::query()
            ->visibleFor(Auth::user())
            ->with(['sender', 'receiver'])
            ->whereDoesntHave('withdrawal')
            ->where('status', '!=', PackageStatus::Delivered->value)
            ->where('status', '!=', PackageStatus::Cancelled->value)
            ->where(function ($q) use ($term) {
                $q->where('withdrawal_code', 'like', "%{$term}%")
                  ->orWhere('tracking_number', 'like', "%{$term}%")
                  ->orWhereHas('sender', fn ($q2) => $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]))
                  ->orWhereHas('receiver', fn ($q2) => $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]));
            })
            ->take(6)
            ->get()
            ->map(fn (Package $p) => [
                'id'              => $p->id,
                'tracking_number' => $p->tracking_number,
                'withdrawal_code' => $p->withdrawal_code,
                'package_name'    => $p->package_name,
                'sender_name'     => $p->sender?->full_name,
                'receiver_name'   => $p->receiver?->full_name,
                'status'          => $p->status->label(),
                'status_classes'  => $p->status->badgeClasses(),
            ])
            ->toArray();

        $this->showSuggestions = count($this->suggestions) > 0;
    }

    public function selectSuggestion(int $packageId): void
    {
        $pkg = Package::query()
            ->visibleFor(Auth::user())
            ->with(['sender', 'receiver', 'withdrawal'])
            ->find($packageId);

        if (! $pkg) {
            return;
        }

        $this->authorize('view', $pkg);

        $this->package        = $pkg;
        $this->search         = $pkg->withdrawal_code;
        $this->receiver_name  = $pkg->receiver?->full_name ?? '';
        $this->receiver_phone = $pkg->receiver?->phone ?? '';
        $this->suggestions    = [];
        $this->showSuggestions = false;
    }

    public function findPackage(): void
    {
        $this->suggestions    = [];
        $this->showSuggestions = false;

        $term = trim($this->search);

        if (! $term) {
            $this->addError('search', 'Veuillez saisir un code ou un nom.');
            return;
        }

        $pkg = Package::query()
            ->visibleFor(Auth::user())
            ->with(['sender', 'receiver', 'withdrawal'])
            ->where(function ($q) use ($term) {
                $q->where('withdrawal_code', $term)
                  ->orWhere('tracking_number', $term)
                  ->orWhereHas('sender', fn ($q2) => $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]))
                  ->orWhereHas('receiver', fn ($q2) => $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]));
            })
            ->first();

        if (! $pkg) {
            $this->addError('search', 'Aucun colis trouvé. Vérifiez le code ou le nom.');
            return;
        }

        $this->authorize('view', $pkg);

        $this->package        = $pkg;
        $this->receiver_name  = $pkg->receiver?->full_name ?? '';
        $this->receiver_phone = $pkg->receiver?->phone ?? '';
    }

    public function clearPackage(): void
    {
        $this->reset(['package', 'search', 'receiver_name', 'receiver_phone', 'identity_document', 'notes', 'suggestions', 'showSuggestions']);
    }

    public function validateWithdrawal(PackageNotifier $notifier): void
    {
        if (! $this->package) {
            $this->findPackage();
        }

        if (! $this->package) {
            return;
        }

        $this->authorize('create', Withdrawal::class);
        $this->authorize('update', $this->package);

        $this->validate([
            'receiver_name'     => ['required', 'string', 'max:255'],
            'receiver_phone'    => ['required', 'string', 'max:40'],
            'identity_document' => ['nullable', 'string', 'max:120'],
            'notes'             => ['nullable', 'string', 'max:2000'],
        ]);

        if ($this->package->withdrawal) {
            session()->flash('error', 'Ce colis a déjà été retiré.');
            return;
        }

        $oldStatus    = $this->package->status->value;
        $receiverName = $this->receiver_name;

        DB::transaction(function () use ($oldStatus): void {
            Withdrawal::query()->create([
                'package_id'        => $this->package->id,
                'agency_id'         => $this->package->agency_id,
                'validated_by'      => Auth::id(),
                'receiver_name'     => $this->receiver_name,
                'receiver_phone'    => $this->receiver_phone,
                'withdrawal_code'   => $this->package->withdrawal_code,
                'identity_document' => $this->identity_document ?: null,
                'notes'             => $this->notes ?: null,
                'withdrawn_at'      => now(),
            ]);

            $this->package->update([
                'status'       => PackageStatus::Delivered,
                'delivered_at' => now(),
            ]);

            $this->package->histories()->create([
                'user_id'    => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => PackageStatus::Delivered->value,
                'comment'    => 'Retrait validé au guichet.',
            ]);
        });

        $this->package->refresh()->load(['sender', 'receiver']);
        $notifier->notifyWithdrawal($this->package, $receiverName);

        $this->reset(['search', 'package', 'receiver_name', 'receiver_phone', 'identity_document', 'notes', 'suggestions', 'showSuggestions']);
        session()->flash('success', 'Retrait validé — WhatsApp envoyé à l\'expéditeur.');
    }

    public function render()
    {
        return view('livewire.withdrawals.process', [
            'recentWithdrawals' => Withdrawal::query()
                ->with(['package.receiver', 'validator'])
                ->whereHas('package', fn ($query) => $query->visibleFor(Auth::user()))
                ->latest()
                ->take(8)
                ->get(),
        ]);
    }
}
