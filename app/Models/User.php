<?php

namespace App\Models;

use App\Enums\UserRoleEnum;
use App\Models\Agency;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if (! $user->name && ($user->first_name || $user->last_name)) {
                $user->name = trim($user->first_name.' '.$user->last_name);
            }
        });
    }

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

    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim($this->first_name.' '.$this->last_name) ?: $this->name);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRoleEnum::class,
            'is_active' => 'boolean',
        ];
    }
}
