<?php

namespace App\Enums;

enum NotificationStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Sent => 'Envoyée',
            self::Failed => 'Échouée',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::Sent => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Failed => 'bg-red-50 text-red-700 ring-red-600/20',
        };
    }
}
