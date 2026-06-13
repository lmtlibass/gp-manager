<?php

namespace App\Console\Commands;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Services\TripService;
use Illuminate\Console\Command;

class TransitionTripsCommand extends Command
{
    protected $signature = 'trips:transition';
    protected $description = 'Transition planned trips to in_transit when departure date arrives';

    public function handle(TripService $tripService): int
    {
        $trips = Trip::query()
            ->where('status', TripStatus::Planned->value)
            ->whereDate('departure_date', '<=', today())
            ->with('packages')
            ->get();

        if ($trips->isEmpty()) {
            $this->info('No trips to transition.');
            return self::SUCCESS;
        }

        foreach ($trips as $trip) {
            $tripService->transitionToInTransit($trip);
            $this->info("Trip #{$trip->id} «{$trip->name}» → in_transit ({$trip->packages->count()} packages).");
        }

        return self::SUCCESS;
    }
}
