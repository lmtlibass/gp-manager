<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, UserRoleEnum::cases(), true);
    }

    public function view(User $user, Package $package): bool
    {
        return $user->role === UserRoleEnum::Admin || $package->agency_id === $user->agency_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, UserRoleEnum::cases(), true);
    }

    public function update(User $user, Package $package): bool
    {
        return $this->view($user, $package);
    }

    public function delete(User $user, Package $package): bool
    {
        return $user->role === UserRoleEnum::Admin
            || ($user->role === UserRoleEnum::GpManager && $package->agency_id === $user->agency_id);
    }
}
