<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRoleEnum::Admin, UserRoleEnum::GpManager], true);
    }

    public function view(User $user, User $model): bool
    {
        if ($user->role === UserRoleEnum::Admin) {
            return true;
        }

        return $user->role === UserRoleEnum::GpManager
            && $model->agency_id === $user->agency_id
            && $model->role !== UserRoleEnum::Admin;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRoleEnum::Admin, UserRoleEnum::GpManager], true);
    }

    public function update(User $user, User $model): bool
    {
        if ($user->role === UserRoleEnum::Admin) {
            return true;
        }

        return $user->role === UserRoleEnum::GpManager
            && $model->agency_id === $user->agency_id
            && $model->role === UserRoleEnum::Agent;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->role === UserRoleEnum::Admin) {
            return true;
        }

        return $user->role === UserRoleEnum::GpManager
            && $model->agency_id === $user->agency_id
            && $model->role === UserRoleEnum::Agent;
    }
}
