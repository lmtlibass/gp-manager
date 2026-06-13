<?php

namespace App\Models;

use App\Enums\PackageStatus;
use App\Models\PackageHistory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'agency_id',
    'created_by',
    'sender_id',
    'receiver_id',
    'tracking_number',
    'withdrawal_code',
    'package_name',
    'description',
    'weight',
    'quantity',
    'declared_value',
    'origin_country',
    'destination_country',
    'status',
    'received_at',
    'arrived_at',
    'delivered_at',
    'notes',
])]
class Package extends Model
{
    use HasFactory, SoftDeletes;

    public static function generateTrackingNumber(): string
    {
        $year = now()->format('Y');

        do {
            $sequence = str_pad((string) (self::withTrashed()->whereYear('created_at', $year)->count() + 1 + random_int(0, 9)), 6, '0', STR_PAD_LEFT);
            $trackingNumber = "WM-{$year}-{$sequence}";
        } while (self::withTrashed()->where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    public static function generateWithdrawalCode(): string
    {
        do {
            $code = (string) random_int(1000, 9999);
        } while (self::withTrashed()->where('withdrawal_code', $code)->exists());

        return $code;
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'receiver_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(PackageHistory::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(PackageNotification::class);
    }

    public function withdrawal(): HasOne
    {
        return $this->hasOne(Withdrawal::class);
    }

    public function trips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class, 'trip_packages')->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'status' => PackageStatus::class,
            'weight' => 'decimal:2',
            'declared_value' => 'decimal:2',
            'received_at' => 'datetime',
            'arrived_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }
}
