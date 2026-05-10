<?php

namespace App\Livewire\Packages;

use App\Enums\PackageStatus;
use App\Models\Customer;
use App\Models\Package;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?Package $package = null;

    public int|string|null $sender_id = null;
    public int|string|null $receiver_id = null;
    public string $package_name = '';
    public string $description = '';
    public string $weight = '';
    public int $quantity = 1;
    public string $declared_value = '';
    public string $origin_country = '';
    public string $destination_country = '';
    public string $status = 'received';
    public string $notes = '';

    public function mount(?Package $package = null): void
    {
        if ($package?->exists) {
            $this->package = $package;
            $this->authorizePackage($package);

            $this->sender_id = $package->sender_id;
            $this->receiver_id = $package->receiver_id;
            $this->package_name = $package->package_name;
            $this->description = $package->description ?? '';
            $this->weight = $package->weight ?? '';
            $this->quantity = $package->quantity;
            $this->declared_value = $package->declared_value ?? '';
            $this->origin_country = $package->origin_country;
            $this->destination_country = $package->destination_country;
            $this->notes = $package->notes ?? '';
            $this->status = $package->status->value;
        }
    }

    public function save(): void
    {
        $data = $this->validate();
        $user = Auth::user();

        DB::transaction(function () use ($data, $user): void {
            if ($this->package?->exists) {
                $oldStatus = $this->package->status->value;
                $this->package->update($this->payload($data));

                if ($oldStatus !== $this->status) {
                    $this->package->histories()->create([
                        'user_id' => Auth::id(),
                        'old_status' => $oldStatus,
                        'new_status' => $this->status,
                        'comment' => 'Statut modifié depuis la fiche colis.',
                    ]);
                }

                return;
            }

            $package = Package::query()->create(array_merge($this->payload($data), [
                'agency_id' => $user->agency_id,
                'created_by' => Auth::id(),
                'tracking_number' => Package::generateTrackingNumber(),
                'withdrawal_code' => Package::generateWithdrawalCode(),
                'received_at' => now(),
            ]));

            $package->histories()->create([
                'user_id' => Auth::id(),
                'old_status' => null,
                'new_status' => $package->status->value,
                'comment' => 'Création du colis.',
            ]);
        });

        session()->flash('success', $this->package?->exists ? 'Colis mis à jour.' : 'Colis créé.');
        $this->redirectRoute('packages.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.packages.form', [
            'customers' => Customer::query()->orderBy('first_name')->orderBy('last_name')->get(),
            'statuses' => PackageStatus::cases(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'sender_id' => ['required', 'integer', Rule::exists('customers', 'id')],
            'receiver_id' => ['required', 'integer', Rule::exists('customers', 'id'), 'different:sender_id'],
            'package_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'declared_value' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'origin_country' => ['required', 'string', 'max:120'],
            'destination_country' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::enum(PackageStatus::class)],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function payload(array $data): array
    {
        return array_merge($data, [
            'weight' => $data['weight'] === '' ? null : $data['weight'],
            'declared_value' => $data['declared_value'] === '' ? null : $data['declared_value'],
            'arrived_at' => in_array($this->status, ['arrived', 'ready_for_pickup', 'delivered'], true) ? now() : $this->package?->arrived_at,
            'delivered_at' => $this->status === 'delivered' ? now() : $this->package?->delivered_at,
        ]);
    }

    private function authorizePackage(Package $package): void
    {
        if (Auth::user()->role->value !== 'admin' && $package->agency_id !== Auth::user()->agency_id) {
            abort(403);
        }
    }
}
