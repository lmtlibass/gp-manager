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

    public string $withdrawal_code = '';
    public ?Package $package = null;
    public string $receiver_name = '';
    public string $receiver_phone = '';
    public string $identity_document = '';
    public string $notes = '';

    public function mount(): void
    {
        $this->authorize('create', Withdrawal::class);

        // Pre-fill withdrawal code from voice command session
        $key = VoiceIntentEnum::PackageWithdrawal->sessionKey();
        if ($voiceData = session()->pull($key)) {
            $this->withdrawal_code  = $voiceData['withdrawal_code']  ?? '';
            $this->receiver_name    = $voiceData['receiver_name']    ?? '';
            $this->receiver_phone   = $voiceData['receiver_phone']   ?? '';
        }
    }

    #[On('voice-command-applied')]
    public function onVoiceCommandApplied(string $intent, array $data): void
    {
        if ($intent === VoiceIntentEnum::PackageWithdrawal->value) {
            $this->withdrawal_code = $data['withdrawal_code']  ?? $this->withdrawal_code;
            $this->receiver_name   = $data['receiver_name']    ?? $this->receiver_name;
            $this->receiver_phone  = $data['receiver_phone']   ?? $this->receiver_phone;
        }
    }

    public function findPackage(): void
    {
        $this->validate(['withdrawal_code' => ['required', 'string', 'max:20']]);

        $this->package = Package::query()
            ->visibleFor(Auth::user())
            ->with(['sender', 'receiver', 'withdrawal'])
            ->where('withdrawal_code', $this->withdrawal_code)
            ->first();

        if (! $this->package) {
            $this->addError('withdrawal_code', 'Aucun colis trouvé avec ce code.');

            return;
        }

        $this->authorize('view', $this->package);

        $this->receiver_name = $this->package->receiver->full_name;
        $this->receiver_phone = $this->package->receiver->phone;
    }

    public function validateWithdrawal(PackageNotifier $notifier): void
    {
        if (! $this->package) {
            $this->findPackage();
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

        $oldStatus = $this->package->status->value;

        DB::transaction(function () use ($oldStatus): void {
            Withdrawal::query()->create([
                'package_id'       => $this->package->id,
                'agency_id'        => $this->package->agency_id,
                'validated_by'     => Auth::id(),
                'receiver_name'    => $this->receiver_name,
                'receiver_phone'   => $this->receiver_phone,
                'withdrawal_code'  => $this->package->withdrawal_code,
                'identity_document' => $this->identity_document ?: null,
                'notes'            => $this->notes ?: null,
                'withdrawn_at'     => now(),
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
        $notifier->notifyStatusChanged($this->package, $oldStatus);

        $this->reset(['withdrawal_code', 'package', 'receiver_name', 'receiver_phone', 'identity_document', 'notes']);
        session()->flash('success', 'Retrait validé et colis livré.');
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
