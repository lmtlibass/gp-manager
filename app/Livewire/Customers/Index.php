<?php

namespace App\Livewire\Customers;

use App\Enums\VoiceIntentEnum;
use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public ?int $editingId = null;
    public bool $showForm = false;
    public string $first_name = '';
    public string $last_name = '';
    public string $phone = '';
    public string $alternative_phone = '';
    public string $email = '';
    public string $address = '';
    public string $city = '';
    public string $country = '';
    public string $notes = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Customer::class);

        // Voice command: create_customer
        $createKey = VoiceIntentEnum::CreateCustomer->sessionKey();
        if ($voiceData = session()->pull($createKey)) {
            $this->first_name = $voiceData['first_name'] ?? '';
            $this->last_name = $voiceData['last_name'] ?? '';
            $this->phone = $voiceData['phone'] ?? '';
            $this->email = $voiceData['email'] ?? '';
            $this->city = $voiceData['city'] ?? '';
            $this->country = $voiceData['country'] ?? '';
            $this->showForm = true;
        }

        // Voice command: search_customer
        $searchKey = VoiceIntentEnum::SearchCustomer->sessionKey();
        if ($voiceData = session()->pull($searchKey)) {
            $this->search = $voiceData['name'] ?? $voiceData['phone'] ?? '';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('voice-command-applied')]
    public function onVoiceCommandApplied(string $intent, array $data): void
    {
        if ($intent === VoiceIntentEnum::CreateCustomer->value) {
            $this->first_name = $data['first_name'] ?? $this->first_name;
            $this->last_name = $data['last_name'] ?? $this->last_name;
            $this->phone = $data['phone'] ?? $this->phone;
            $this->email = $data['email'] ?? $this->email;
            $this->city = $data['city'] ?? $this->city;
            $this->country = $data['country'] ?? $this->country;
            $this->showForm = true;
        } elseif ($intent === VoiceIntentEnum::SearchCustomer->value) {
            $this->search = $data['name'] ?? $data['phone'] ?? '';
        }
    }

    public function create(): void
    {
        $this->authorize('create', Customer::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $customerId): void
    {
        $customer = Customer::query()->visibleFor(Auth::user())->findOrFail($customerId);
        $this->authorize('update', $customer);

        $this->editingId = $customer->id;
        $this->first_name = $customer->first_name;
        $this->last_name = $customer->last_name ?? '';
        $this->phone = $customer->phone;
        $this->alternative_phone = $customer->alternative_phone ?? '';
        $this->email = $customer->email ?? '';
        $this->address = $customer->address ?? '';
        $this->city = $customer->city ?? '';
        $this->country = $customer->country ?? '';
        $this->notes = $customer->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();
        $customer = $this->editingId
            ? Customer::query()->visibleFor(Auth::user())->findOrFail($this->editingId)
            : null;

        $this->authorize($customer ? 'update' : 'create', $customer ?: Customer::class);

        if (! $customer) {
            $data['agency_id'] = Auth::user()->agency_id;
        }

        Customer::query()->updateOrCreate(['id' => $this->editingId], $data);

        $this->resetForm();
        session()->flash('success', 'Client enregistré.');
    }

    public function delete(int $customerId): void
    {
        $customer = Customer::query()->visibleFor(Auth::user())->withCount(['sentPackages', 'receivedPackages'])->findOrFail($customerId);
        $this->authorize('delete', $customer);

        if ($customer->sent_packages_count > 0 || $customer->received_packages_count > 0) {
            session()->flash('error', 'Impossible de supprimer un client ayant des colis.');

            return;
        }

        $customer->delete();
        session()->flash('success', 'Client supprimé.');
    }

    public function render()
    {
        return view('livewire.customers.index', [
            'customers' => Customer::query()
                ->visibleFor(Auth::user())
                ->withCount(['sentPackages', 'receivedPackages'])
                ->when($this->search !== '', function ($query): void {
                    $query->where(function ($query): void {
                        $query->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%")
                            ->orWhere('phone', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%");
                    });
                })
                ->latest()
                ->paginate(10),
        ]);
    }

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'alternative_phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'showForm', 'first_name', 'last_name', 'phone', 'alternative_phone', 'email', 'address', 'city', 'country', 'notes']);
    }
}
