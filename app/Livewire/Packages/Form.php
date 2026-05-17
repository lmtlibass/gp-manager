<?php

namespace App\Livewire\Packages;

use App\Enums\PackageStatus;
use App\Enums\UserRoleEnum;
use App\Enums\VoiceIntentEnum;
use App\Models\Customer;
use App\Models\Package;
use App\Services\PackageNotifier;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class Form extends Component
{
    use AuthorizesRequests;

    public ?Package $package = null;

    public int|string|null $sender_id = null;
    public int|string|null $receiver_id = null;
    public string $sender_name = '';
    public string $sender_phone = '';
    public string $receiver_name = '';
    public string $receiver_phone = '';
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
            $this->authorize('update', $package);

            $this->sender_id = $package->sender_id;
            $this->receiver_id = $package->receiver_id;
            $this->sender_name = $package->sender_name ?? '';
            $this->sender_phone = $package->sender_phone ?? '';
            $this->receiver_name = $package->receiver_name ?? '';
            $this->receiver_phone = $package->receiver_phone ?? '';
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

        if (! $this->package?->exists) {
            $this->authorize('create', Package::class);
        }

        // Pre-fill from voice command stored in session
        if (! $this->package?->exists) {
            $key = VoiceIntentEnum::CreatePackage->sessionKey();
            if ($voiceData = session()->pull($key)) {
                $this->applyVoiceData($voiceData);
            }
        }
    }

    /** Pre-fill form fields from a voice command result array. */
    #[On('voice-command-applied')]
    public function onVoiceCommandApplied(string $intent, array $data): void
    {
        if ($intent === VoiceIntentEnum::CreatePackage->value) {
            $this->applyVoiceData($data);
        }
    }

    public function applyVoiceData(array $data): void
    {
        $fields = [
            'package_name', 'weight', 'quantity', 'declared_value',
            'origin_country', 'destination_country', 'description', 'notes',
            'sender_id', 'receiver_id', 'status',
            'sender_name', 'sender_phone', 'receiver_name', 'receiver_phone',
        ];

        foreach ($fields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $this->$field = $data[$field];
            }
        }
    }

    public function save(PackageNotifier $notifier): void
    {
        $data = $this->validate();
        $user = Auth::user();
        $isNew = ! $this->package?->exists;
        $this->authorize($isNew ? 'create' : 'update', $this->package ?: Package::class);

        $oldStatus = $this->package?->status?->value;

        $package = DB::transaction(function () use ($data, $user, $isNew, $oldStatus): Package {
            if (! $isNew) {
                $this->package->update($this->payload($data));

                if ($oldStatus !== $this->status) {
                    $this->package->histories()->create([
                        'user_id'    => Auth::id(),
                        'old_status' => $oldStatus,
                        'new_status' => $this->status,
                        'comment'    => 'Statut modifié depuis la fiche colis.',
                    ]);
                }

                return $this->package->fresh(['sender', 'receiver']);
            }

            $package = Package::query()->create(array_merge($this->payload($data), [
                'agency_id'       => $user->agency_id,
                'created_by'      => Auth::id(),
                'tracking_number' => Package::generateTrackingNumber(),
                'withdrawal_code' => Package::generateWithdrawalCode(),
                'received_at'     => now(),
            ]));

            $package->histories()->create([
                'user_id'    => Auth::id(),
                'old_status' => null,
                'new_status' => $package->status->value,
                'comment'    => 'Création du colis.',
            ]);

            return $package->load(['sender', 'receiver']);
        });

        if ($isNew) {
            $notifier->notifyCreated($package);
        } else {
            $notifier->notifyStatusChanged($package, $oldStatus);
        }

        session()->flash('success', $isNew ? 'Colis créé.' : 'Colis mis à jour.');
        $this->redirectRoute('packages.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.packages.form', [
            'customers' => Customer::query()->visibleFor(Auth::user())->orderBy('first_name')->orderBy('last_name')->get(),
            'statuses' => $this->availableStatuses(),
        ]);
    }

    protected function rules(): array
    {
        return [
            'sender_id' => ['nullable', 'integer'],
            'receiver_id' => ['nullable', 'integer'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['nullable', 'string', 'max:50'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:50'],
            'package_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'declared_value' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'origin_country' => ['required', 'string', 'max:120'],
            'destination_country' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::in(array_map(fn (PackageStatus $status) => $status->value, $this->availableStatuses()))],
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

    private function customerExistsRule()
    {
        $rule = Rule::exists('customers', 'id');

        if (Auth::user()->role !== UserRoleEnum::Admin) {
            $rule->where('agency_id', Auth::user()->agency_id);
        }

        return $rule;
    }
}
