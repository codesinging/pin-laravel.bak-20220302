<?php

namespace App\Providers;

use App\Support\Model\AuthModel;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // 超级管理员拥有所有权限
        Gate::before(function (AuthModel $user, $ability){
            return $user->isSuper() ? true : null;
        });

        Gate::after(function (AuthModel $user, $ability){
            return $user->isSuper();
        });
    }
}
