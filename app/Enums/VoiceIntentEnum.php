<?php

namespace App\Enums;

enum VoiceIntentEnum: string
{
    case CreatePackage        = 'create_package';
    case SearchPackage        = 'search_package';
    case UpdatePackageStatus  = 'update_package_status';
    case CreateCustomer       = 'create_customer';
    case SearchCustomer       = 'search_customer';
    case PackageWithdrawal    = 'package_withdrawal';
    case Unknown              = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::CreatePackage       => 'Créer un colis',
            self::SearchPackage       => 'Rechercher un colis',
            self::UpdatePackageStatus => 'Modifier un statut',
            self::CreateCustomer      => 'Créer un client',
            self::SearchCustomer      => 'Rechercher un client',
            self::PackageWithdrawal   => 'Retrait de colis',
            self::Unknown             => 'Inconnu',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CreatePackage       => 'fa-box-open',
            self::SearchPackage       => 'fa-magnifying-glass',
            self::UpdatePackageStatus => 'fa-arrows-rotate',
            self::CreateCustomer      => 'fa-user-plus',
            self::SearchCustomer      => 'fa-address-book',
            self::PackageWithdrawal   => 'fa-hand-holding-box',
            self::Unknown             => 'fa-circle-question',
        };
    }

    public function targetRoute(): ?string
    {
        return match ($this) {
            self::CreatePackage       => 'packages.create',
            self::SearchPackage       => 'packages.index',
            self::UpdatePackageStatus => 'packages.index',
            self::CreateCustomer      => 'customers.index',
            self::SearchCustomer      => 'customers.index',
            self::PackageWithdrawal   => 'withdrawals.process',
            self::Unknown             => null,
        };
    }

    public function sessionKey(): string
    {
        return 'voice_command_' . $this->value;
    }
}
