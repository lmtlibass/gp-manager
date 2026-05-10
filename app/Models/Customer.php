<?php

namespace App\Models;

use App\Models\Package;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['first_name', 'last_name', 'phone', 'alternative_phone', 'email', 'address', 'city', 'country', 'notes'])]
class Customer extends Model
{
    use HasFactory;

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
