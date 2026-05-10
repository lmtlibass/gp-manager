<?php

namespace App\Enums;

enum PackageStatus: string
{
    case Received = 'received';
    case InTransit = 'in_transit';
    case Arrived = 'arrived';
    case ReadyForPickup = 'ready_for_pickup';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Received => 'Reçu',
            self::InTransit => 'En transit',
            self::Arrived => 'Arrivé',
            self::ReadyForPickup => 'Prêt',
            self::Delivered => 'Livré',
            self::Cancelled => 'Annulé',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Received => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            self::InTransit => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::Arrived => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
            self::ReadyForPickup => 'bg-purple-50 text-purple-700 ring-purple-600/20',
            self::Delivered => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Cancelled => 'bg-red-50 text-red-700 ring-red-600/20',
        };
    }
}
