<?php

namespace App\Models;

use App\Models\Package;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['agency_id', 'first_name', 'last_name', 'phone', 'alternative_phone', 'email', 'address', 'city', 'country', 'notes'])]
class Customer extends Model
{
    use HasFactory;

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        if ($user->role->value === 'admin') {
            return $query;
        }

        return $query->where('agency_id', $user->agency_id);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim($this->first_name.' '.$this->last_name));
    }

    public function sentPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'sender_id');
    }

    public function receivedPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'receiver_id');
    }
}
