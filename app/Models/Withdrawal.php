<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'package_id',
    'agency_id',
    'validated_by',
    'receiver_name',
    'receiver_phone',
    'withdrawal_code',
    'identity_document',
    'notes',
    'withdrawn_at',
])]
class Withdrawal extends Model
{
    use HasFactory;

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    protected function casts(): array
    {
        return [
            'withdrawn_at' => 'datetime',
        ];
    }
}
