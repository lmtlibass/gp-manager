<?php

namespace App\Livewire\Search;

use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GlobalSearch extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.search.global-search', [
            'packages' => Package::query()
                ->visibleFor(Auth::user())
                ->with(['sender', 'receiver', 'agency'])
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
                ->latest()
                ->paginate(10),
        ]);
    }
}
