<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $customerId): void
    {
        $customer = Customer::query()->findOrFail($customerId);
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

        Customer::query()->updateOrCreate(['id' => $this->editingId], $data);

        $this->resetForm();
        session()->flash('success', 'Client enregistré.');
    }

    public function delete(int $customerId): void
    {
        $customer = Customer::query()->withCount(['sentPackages', 'receivedPackages'])->findOrFail($customerId);

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
                ->withCount(['sentPackages', 'receivedPackages'])
                ->when($this->search !== '', function ($query): void {
                    $query->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
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
