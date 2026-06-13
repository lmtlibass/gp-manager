<?php

namespace App\Enums;

enum TripStatus: string
{
    case Planned = 'planned';
    case InTransit = 'in_transit';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planned   => 'Planifié',
            self::InTransit => 'En transit',
            self::Completed => 'Terminé',
            self::Cancelled => 'Annulé',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Planned   => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            self::InTransit => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::Completed => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Cancelled => 'bg-red-50 text-red-700 ring-red-600/20',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Planned   => 'fa-calendar-days',
            self::InTransit => 'fa-truck-fast',
            self::Completed => 'fa-circle-check',
            self::Cancelled => 'fa-circle-xmark',
        };
    }
}
