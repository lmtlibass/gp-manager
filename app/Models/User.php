<?php

namespace App\Models;

use App\Enums\Role;
use App\Models\Agency;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'first_name', 'last_name', 'email', 'password', 'agency_id', 'role', 'phone', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function createdPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'created_by');
    }

    public function validatedWithdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class, 'validated_by');
    }

    public function packageHistories(): HasMany
    {
        return $this->hasMany(PackageHistory::class);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_active' => 'boolean',
        ];
    }
}
