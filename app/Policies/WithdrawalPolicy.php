<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\User;
use App\Models\Withdrawal;

class WithdrawalPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, UserRoleEnum::cases(), true);
    }

    public function view(User $user, Withdrawal $withdrawal): bool
    {
        return $user->role === UserRoleEnum::Admin || $withdrawal->agency_id === $user->agency_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, UserRoleEnum::cases(), true);
    }

    public function update(User $user, Withdrawal $withdrawal): bool
    {
        return $user->role === UserRoleEnum::Admin
            || ($user->role === UserRoleEnum::GpManager && $withdrawal->agency_id === $user->agency_id);
    }

    public function delete(User $user, Withdrawal $withdrawal): bool
    {
        return $user->role === UserRoleEnum::Admin
            || ($user->role === UserRoleEnum::GpManager && $withdrawal->agency_id === $user->agency_id);
    }
}
