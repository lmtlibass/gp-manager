<?php

namespace App\Providers;

use App\Enums\UserRoleEnum;
use App\Models\Customer;
use App\Models\Package;
use App\Models\User;
use App\Models\Withdrawal;
use App\Policies\CustomerPolicy;
use App\Policies\PackagePolicy;
use App\Policies\UserPolicy;
use App\Policies\WithdrawalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Package::class, PackagePolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Withdrawal::class, WithdrawalPolicy::class);

        Gate::define('access-admin', fn (User $user): bool => $user->role === UserRoleEnum::Admin);

        Gate::define('manage-agency', fn (User $user): bool => in_array($user->role, [
            UserRoleEnum::Admin,
            UserRoleEnum::GpManager,
        ], true));

        Gate::define('manage-users', fn (User $user): bool => in_array($user->role, [
            UserRoleEnum::Admin,
            UserRoleEnum::GpManager,
        ], true));
    }
}
