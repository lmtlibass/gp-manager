<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case WhatsApp = 'whatsapp';
    case SMS = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::WhatsApp => 'WhatsApp',
            self::SMS => 'SMS',
        };
    }
}
