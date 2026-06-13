<?php

namespace App\Models;

use App\Enums\TripStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'name',
        'departure_date',
        'origin',
        'destination',
        'status',
        'driver_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status'         => TripStatus::class,
            'departure_date' => 'date',
        ];
    }

    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        if ($user->role->value === 'admin') {
            return $query;
        }

        return $query->where('agency_id', $user->agency_id);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'trip_packages')->withTimestamps();
    }
}
