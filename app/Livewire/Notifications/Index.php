<?php

namespace App\Livewire\Notifications;

use App\Enums\NotificationChannel;
use App\Enums\NotificationStatus;
use App\Models\Package;
use App\Models\PackageNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public string $package_tracking = '';
    public string $channel = 'whatsapp';
    public string $recipient_phone = '';
    public string $message = '';
    public string $status = 'pending';

    public function mount(): void
    {
        $this->authorize('viewAny', Package::class);
    }

    public function create(): void
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

        PackageNotification::query()->create([
            'package_id' => $package->id,
            'channel' => $data['channel'],
            'recipient_phone' => $data['recipient_phone'],
            'message' => $data['message'],
            'status' => $data['status'],
            'sent_at' => $data['status'] === NotificationStatus::Sent->value ? now() : null,
        ]);

        $this->reset(['package_tracking', 'recipient_phone', 'message', 'status']);
        $this->status = NotificationStatus::Pending->value;
        session()->flash('success', 'Notification enregistrée.');
    }

    public function markSent(int $notificationId): void
    {
        $notification = $this->notificationQuery()->findOrFail($notificationId);
        $notification->update(['status' => NotificationStatus::Sent, 'sent_at' => now(), 'failed_reason' => null]);
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
            'statuses' => NotificationStatus::cases(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'package_tracking' => ['required', 'string', 'max:40'],
            'channel' => ['required', Rule::enum(NotificationChannel::class)],
            'recipient_phone' => ['required', 'string', 'max:40'],
            'message' => ['required', 'string', 'max:2000'],
            'status' => ['required', Rule::enum(NotificationStatus::class)],
        ];
    }

    private function notificationQuery()
    {
        return PackageNotification::query()
            ->with('package.receiver')
            ->whereHas('package', fn ($query) => $query->visibleFor(Auth::user()));
    }
}
