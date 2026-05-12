<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, UserRoleEnum::cases(), true);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->role === UserRoleEnum::Admin || $customer->agency_id === $user->agency_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, UserRoleEnum::cases(), true);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->view($user, $customer);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->role === UserRoleEnum::Admin
            || ($user->role === UserRoleEnum::GpManager && $customer->agency_id === $user->agency_id);
    }
}
