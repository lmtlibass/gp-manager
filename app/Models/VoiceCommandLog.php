<?php

namespace App\Models;

use App\Enums\VoiceIntentEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'agency_id', 'transcription', 'intent', 'extracted_data',
    'confidence', 'status', 'error_message', 'raw_response',
    'model_used', 'tokens_used', 'response_time_ms',
])]
class VoiceCommandLog extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function intentEnum(): ?VoiceIntentEnum
    {
        return VoiceIntentEnum::tryFrom($this->intent ?? '');
    }

    protected function casts(): array
    {
        return [
            'extracted_data'   => 'array',
            'confidence'       => 'float',
            'tokens_used'      => 'integer',
            'response_time_ms' => 'integer',
        ];
    }
}
