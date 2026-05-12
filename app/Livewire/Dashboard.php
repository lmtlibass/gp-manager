<?php

namespace App\Livewire;

use App\Enums\PackageStatus;
use App\Models\Package;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    use AuthorizesRequests;

    public string $scope = 'agency';

    public function mount(): void
    {
        $this->authorize('viewAny', Package::class);
    }

    public function render()
    {
        $user = Auth::user();
        $query = Package::query()->visibleFor($user);

        if ($this->scope === 'mine') {
            $query->where('created_by', Auth::id());
        }

        $packages = (clone $query)->with(['sender', 'receiver', 'creator'])->latest();
        $stats = [
            'total' => (clone $query)->count(),
            'received' => (clone $query)->where('status', PackageStatus::Received->value)->count(),
            'in_transit' => (clone $query)->where('status', PackageStatus::InTransit->value)->count(),
            'arrived' => (clone $query)->where('status', PackageStatus::Arrived->value)->count(),
            'ready_for_pickup' => (clone $query)->where('status', PackageStatus::ReadyForPickup->value)->count(),
            'delivered' => (clone $query)->where('status', PackageStatus::Delivered->value)->count(),
        ];

        return view('livewire.dashboard', [
            'stats' => $stats,
            'latestPackages' => $packages->take(8)->get(),
            'recentHistories' => Package::query()
                ->visibleFor($user)
                ->with(['histories' => fn ($query) => $query->latest()->with('user'), 'sender', 'receiver'])
                ->whereHas('histories')
                ->latest('updated_at')
                ->take(6)
                ->get()
                ->flatMap(fn (Package $package) => $package->histories->map(fn ($history) => [$package, $history]))
                ->sortByDesc(fn ($item) => $item[1]->created_at)
                ->take(6),
        ]);
    }
}
