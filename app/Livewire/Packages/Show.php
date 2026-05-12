<?php

namespace App\Livewire\Packages;

use App\Enums\PackageStatus;
use App\Enums\UserRoleEnum;
use App\Models\Package;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    use AuthorizesRequests;

    public Package $package;
    public string $status = '';
    public string $comment = '';

    public function mount(Package $package): void
    {
        $this->authorize('view', $package);
        $this->package = $package;
        $this->status = $package->status->value;
    }

    public function changeStatus(): void
    {
        $this->authorize('update', $this->package);

        $this->validate([
            'status' => ['required', \Illuminate\Validation\Rule::in(array_map(fn (PackageStatus $status) => $status->value, $this->availableStatuses()))],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldStatus = $this->package->status->value;

        if ($oldStatus === $this->status) {
            return;
        }

        $this->package->update([
            'status' => $this->status,
            'arrived_at' => in_array($this->status, ['arrived', 'ready_for_pickup', 'delivered'], true) ? now() : $this->package->arrived_at,
            'delivered_at' => $this->status === 'delivered' ? now() : $this->package->delivered_at,
        ]);

        $this->package->histories()->create([
            'user_id' => Auth::id(),
            'old_status' => $oldStatus,
            'new_status' => $this->status,
            'comment' => $this->comment,
        ]);

        $this->comment = '';
        $this->package->refresh();
        session()->flash('success', 'Statut mis à jour.');
    }

    public function render()
    {
        return view('livewire.packages.show', [
            'package' => $this->package->load(['sender', 'receiver', 'agency', 'creator', 'histories.user', 'notifications', 'withdrawal.validator']),
            'statuses' => $this->availableStatuses(),
        ]);
    }

    private function availableStatuses(): array
    {
        if (Auth::user()->role !== UserRoleEnum::Agent) {
            return PackageStatus::cases();
        }

        return [
            PackageStatus::Received,
            PackageStatus::InTransit,
            PackageStatus::Arrived,
            PackageStatus::ReadyForPickup,
        ];
    }
}
