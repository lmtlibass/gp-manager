<?php

namespace App\Services;

use App\Enums\PackageStatus;
use App\Enums\TripStatus;
use App\Models\Package;
use App\Models\Trip;
use Illuminate\Support\Facades\DB;

class TripService
{
    public function __construct(private PackageNotifier $notifier) {}

    public function addPackage(Trip $trip, Package $package): void
    {
        if ($trip->packages()->where('package_id', $package->id)->exists()) {
            return;
        }

        $trip->packages()->attach($package->id);
    }

    public function removePackage(Trip $trip, Package $package): void
    {
        $trip->packages()->detach($package->id);
    }

    public function transitionToInTransit(Trip $trip, ?int $byUserId = null): void
    {
        $packages = $trip->packages()
            ->with(['sender', 'receiver'])
            ->where('status', PackageStatus::Received->value)
            ->get();

        DB::transaction(function () use ($trip, $packages, $byUserId): void {
            foreach ($packages as $package) {
                $oldStatus = $package->status->value;

                $package->update(['status' => PackageStatus::InTransit]);

                $package->histories()->create([
                    'user_id'    => $byUserId,
                    'old_status' => $oldStatus,
                    'new_status' => PackageStatus::InTransit->value,
                    'comment'    => "Mis en transit via le voyage : {$trip->name}.",
                ]);
            }

            $trip->update(['status' => TripStatus::InTransit]);
        });

        foreach ($packages as $package) {
            $this->notifier->notifyStatusChanged(
                $package->fresh(['sender', 'receiver']),
                PackageStatus::Received->value
            );
        }
    }
}
