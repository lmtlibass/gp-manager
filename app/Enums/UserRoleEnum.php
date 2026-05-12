<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case Admin = 'admin';
    case GpManager = 'gp_manager';
    case Agent = 'agent';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::GpManager => 'GP Manager',
            self::Agent => 'Agent',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Admin => 'bg-purple-50 text-purple-700 ring-purple-600/20',
            self::GpManager => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            self::Agent => 'bg-gray-50 text-gray-700 ring-gray-600/20',
        };
    }
}
