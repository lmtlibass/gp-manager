<?php

namespace App\Livewire\Trips;

use App\Enums\PackageStatus;
use App\Enums\TripStatus;
use App\Models\Package;
use App\Models\Trip;
use App\Services\TripService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    // --- Filters ---
    public string $filterStatus = '';

    // --- Create / Edit form ---
    public bool $showForm = false;
    public ?int $editingTripId = null;
    public string $name = '';
    public string $departure_date = '';
    public string $origin = '';
    public string $destination = '';
    public string $driver_name = '';
    public string $notes = '';

    // --- Groupage panel ---
    public ?int $selectedTripId = null;
    public string $packageSearch = '';
    public array $packageSuggestions = [];

    public function mount(): void
    {
        $this->authorize('manage-agency');
    }

    // --- Form ---

    public function openCreateForm(): void
    {
        $this->reset(['editingTripId', 'name', 'departure_date', 'origin', 'destination', 'driver_name', 'notes']);
        $this->showForm = true;
    }

    public function editTrip(int $id): void
    {
        $trip = Trip::query()->visibleFor(Auth::user())->findOrFail($id);

        $this->editingTripId  = $id;
        $this->name           = $trip->name;
        $this->departure_date = $trip->departure_date->format('Y-m-d');
        $this->origin         = $trip->origin;
        $this->destination    = $trip->destination;
        $this->driver_name    = $trip->driver_name ?? '';
        $this->notes          = $trip->notes ?? '';
        $this->showForm       = true;
    }

    public function saveTrip(): void
    {
        $rules = [
            'name'           => ['required', 'string', 'max:200'],
            'departure_date' => ['required', 'date'],
            'origin'         => ['required', 'string', 'max:100'],
            'destination'    => ['required', 'string', 'max:100'],
            'driver_name'    => ['nullable', 'string', 'max:150'],
            'notes'          => ['nullable', 'string', 'max:1000'],
        ];

        if (! $this->editingTripId) {
            $rules['departure_date'][] = 'after_or_equal:today';
        }

        $this->validate($rules);

        $data = [
            'name'           => $this->name,
            'departure_date' => $this->departure_date,
            'origin'         => $this->origin,
            'destination'    => $this->destination,
            'driver_name'    => $this->driver_name ?: null,
            'notes'          => $this->notes ?: null,
        ];

        if ($this->editingTripId) {
            Trip::query()->visibleFor(Auth::user())->findOrFail($this->editingTripId)->update($data);
            session()->flash('success', 'Voyage mis à jour.');
        } else {
            $trip = Trip::query()->create(array_merge($data, [
                'agency_id' => Auth::user()->agency_id,
                'status'    => TripStatus::Planned,
            ]));
            $this->selectedTripId = $trip->id;
            session()->flash('success', 'Voyage planifié — ajoutez les colis au groupage.');
        }

        $this->reset(['showForm', 'editingTripId', 'name', 'departure_date', 'origin', 'destination', 'driver_name', 'notes']);
    }

    public function cancelForm(): void
    {
        $this->reset(['showForm', 'editingTripId', 'name', 'departure_date', 'origin', 'destination', 'driver_name', 'notes']);
    }

    // --- Groupage ---

    public function toggleTrip(int $id): void
    {
        $this->selectedTripId = $this->selectedTripId === $id ? null : $id;
        $this->packageSearch      = '';
        $this->packageSuggestions = [];
    }

    public function updatedPackageSearch(): void
    {
        $this->packageSuggestions = [];

        if (! $this->selectedTripId || mb_strlen(trim($this->packageSearch)) < 2) {
            return;
        }

        $term        = trim($this->packageSearch);
        $existingIds = Trip::query()
            ->visibleFor(Auth::user())
            ->findOrFail($this->selectedTripId)
            ->packages()
            ->pluck('packages.id')
            ->toArray();

        $this->packageSuggestions = Package::query()
            ->visibleFor(Auth::user())
            ->with(['sender', 'receiver'])
            ->whereNotIn('id', $existingIds)
            ->where('status', PackageStatus::Received->value)
            ->where(function ($q) use ($term) {
                $q->where('withdrawal_code', 'like', "%{$term}%")
                  ->orWhere('tracking_number', 'like', "%{$term}%")
                  ->orWhereHas('sender', fn ($q2) => $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]))
                  ->orWhereHas('receiver', fn ($q2) => $q2->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]));
            })
            ->take(5)
            ->get()
            ->map(fn (Package $p) => [
                'id'              => $p->id,
                'tracking_number' => $p->tracking_number,
                'package_name'    => $p->package_name,
                'sender_name'     => $p->sender?->full_name,
                'receiver_name'   => $p->receiver?->full_name,
            ])
            ->toArray();
    }

    public function addPackage(int $packageId, TripService $tripService): void
    {
        $trip    = Trip::query()->visibleFor(Auth::user())->findOrFail($this->selectedTripId);
        $package = Package::query()->visibleFor(Auth::user())->findOrFail($packageId);

        $tripService->addPackage($trip, $package);

        $this->packageSearch      = '';
        $this->packageSuggestions = [];
    }

    public function removePackage(int $packageId, TripService $tripService): void
    {
        $trip    = Trip::query()->visibleFor(Auth::user())->findOrFail($this->selectedTripId);
        $package = Package::query()->visibleFor(Auth::user())->findOrFail($packageId);

        $tripService->removePackage($trip, $package);
    }

    public function startTrip(int $id, TripService $tripService): void
    {
        $trip = Trip::query()->visibleFor(Auth::user())->findOrFail($id);

        if ($trip->status !== TripStatus::Planned) {
            session()->flash('error', 'Ce voyage n\'est pas en statut planifié.');
            return;
        }

        $tripService->transitionToInTransit($trip, Auth::id());
        session()->flash('success', 'Voyage démarré — tous les colis sont maintenant en transit.');
    }

    public function deleteTrip(int $id): void
    {
        $trip = Trip::query()->visibleFor(Auth::user())->findOrFail($id);

        if ($trip->status !== TripStatus::Planned) {
            session()->flash('error', 'Impossible de supprimer un voyage déjà démarré.');
            return;
        }

        $trip->delete();

        if ($this->selectedTripId === $id) {
            $this->selectedTripId = null;
        }

        session()->flash('success', 'Voyage supprimé.');
    }

    public function render()
    {
        $trips = Trip::query()
            ->visibleFor(Auth::user())
            ->withCount('packages')
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy('departure_date')
            ->get();

        $selectedTrip = $this->selectedTripId
            ? Trip::query()
                ->visibleFor(Auth::user())
                ->with(['packages.sender', 'packages.receiver'])
                ->find($this->selectedTripId)
            : null;

        return view('livewire.trips.index', compact('trips', 'selectedTrip'));
    }
}
