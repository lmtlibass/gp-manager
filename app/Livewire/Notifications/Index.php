<?php

namespace App\Livewire\Notifications;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Enums\VoiceIntentEnum;
use App\Models\Package;
use App\Models\PackageNotification;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public string $package_tracking = '';
    public string $channel = 'whatsapp';
    public string $recipient_phone = '';
    public string $message = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Package::class);

        $sendKey = VoiceIntentEnum::SendNotification->sessionKey();
        if ($voiceData = session()->pull($sendKey)) {
            $this->package_tracking = $voiceData['tracking_number'] ?? $voiceData['withdrawal_code'] ?? '';
            $this->search = $voiceData['customer_name'] ?? '';

            if (isset($voiceData['message_type'])) {
                $this->message = match ($voiceData['message_type']) {
                    'arrived' => 'Votre colis est arrivé à l\'agence. Vous pouvez venir le retirer.',
                    'ready_for_pickup' => 'Votre colis est prêt pour le retrait. Présentez-vous avec votre code de retrait.',
                    default => '',
                };
            }
        }
    }

    #[On('voice-command-applied')]
    public function onVoiceCommandApplied(string $intent, array $data): void
    {
        if ($intent === VoiceIntentEnum::SendNotification->value) {
            $this->package_tracking = $data['tracking_number'] ?? $data['withdrawal_code'] ?? $this->package_tracking;
            $this->search = $data['customer_name'] ?? $this->search;

            if (isset($data['message_type'])) {
                $this->message = match ($data['message_type']) {
                    'arrived' => 'Votre colis est arrivé à l\'agence. Vous pouvez venir le retirer.',
                    'ready_for_pickup' => 'Votre colis est prêt pour le retrait. Présentez-vous avec votre code de retrait.',
                    default => $this->message,
                };
            }
        }
    }

    public function send(WhatsAppService $whatsapp): void
    {
        $data = $this->validate();

        $package = Package::query()
            ->visibleFor(Auth::user())
            ->where('tracking_number', $data['package_tracking'])
            ->first();

        if (! $package) {
            $this->addError('package_tracking', 'Colis introuvable.');

            return;
        }

        $this->authorize('view', $package);

        $channel = NotificationChannel::from($data['channel']);
        $status = NotificationStatus::Pending;
        $failedReason = null;
        $sentAt = null;

        try {
            $whatsapp->send($channel, $data['recipient_phone'], $data['message']);
            $status = NotificationStatus::Sent;
            $sentAt = now();
        } catch (Throwable $e) {
            $status = NotificationStatus::Failed;
            $failedReason = $e->getMessage();
        }

        PackageNotification::query()->create([
            'package_id'      => $package->id,
            'channel'         => $channel,
            'recipient_phone' => $data['recipient_phone'],
            'message'         => $data['message'],
            'status'          => $status,
            'sent_at'         => $sentAt,
            'failed_reason'   => $failedReason,
        ]);

        $this->reset(['package_tracking', 'recipient_phone', 'message']);

        if ($status === NotificationStatus::Sent) {
            session()->flash('success', 'Message envoyé avec succès.');
        } else {
            session()->flash('error', 'Échec de l\'envoi : ' . $failedReason);
        }
    }

    public function retry(int $notificationId, WhatsAppService $whatsapp): void
    {
        $notification = $this->notificationQuery()->findOrFail($notificationId);

        try {
            $whatsapp->send($notification->channel, $notification->recipient_phone, $notification->message);
            $notification->update(['status' => NotificationStatus::Sent, 'sent_at' => now(), 'failed_reason' => null]);
            session()->flash('success', 'Message renvoyé avec succès.');
        } catch (Throwable $e) {
            $notification->update(['failed_reason' => $e->getMessage()]);
            session()->flash('error', 'Nouvel échec : ' . $e->getMessage());
        }
    }

    public function markFailed(int $notificationId): void
    {
        $notification = $this->notificationQuery()->findOrFail($notificationId);
        $notification->update(['status' => NotificationStatus::Failed, 'failed_reason' => 'Échec manuel']);
    }

    public function render()
    {
        return view('livewire.notifications.index', [
            'notifications' => $this->notificationQuery()
                ->when($this->search !== '', function ($query): void {
                    $query->where('recipient_phone', 'like', "%{$this->search}%")
                        ->orWhere('message', 'like', "%{$this->search}%")
                        ->orWhereHas('package', fn ($query) => $query->where('tracking_number', 'like', "%{$this->search}%"));
                })
                ->latest()
                ->paginate(10),
            'channels' => NotificationChannel::cases(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'package_tracking' => ['required', 'string', 'max:40'],
            'channel'          => ['required', Rule::enum(NotificationChannel::class)],
            'recipient_phone'  => ['required', 'string', 'max:40'],
            'message'          => ['required', 'string', 'max:2000'],
        ];
    }

    private function notificationQuery()
    {
        return PackageNotification::query()
            ->with('package.receiver')
            ->whereHas('package', fn ($query) => $query->visibleFor(Auth::user()));
    }
}
