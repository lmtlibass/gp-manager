<?php

namespace App\Livewire\Packages;

use App\Enums\PackageStatus;
use App\Models\Package;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    public function mount(): void
    {
        $this->authorize('viewAny', Package::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

    public function delete(int $packageId): void
    {
        $package = Package::query()->visibleFor(Auth::user())->findOrFail($packageId);
        $this->authorize('delete', $package);

        $package->delete();

        session()->flash('success', 'Colis supprimé.');
    }

    public function render()
    {
        $packages = Package::query()
            ->visibleFor(Auth::user())
            ->with(['sender', 'receiver', 'agency'])
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('tracking_number', 'like', "%{$this->search}%")
                        ->orWhere('withdrawal_code', 'like', "%{$this->search}%")
                        ->orWhereHas('sender', fn ($query) => $query
                            ->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%")
                            ->orWhere('phone', 'like', "%{$this->search}%"))
                        ->orWhereHas('receiver', fn ($query) => $query
                            ->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%")
                            ->orWhere('phone', 'like', "%{$this->search}%"));
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.packages.index', [
            'packages' => $packages,
            'statuses' => PackageStatus::cases(),
        ]);
    }
}
