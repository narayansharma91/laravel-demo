<?php

namespace App\Providers;

use App\Models\User;
use Config;
use Gate;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user) {
            return $user->hasRole(Config::get('constants.roles.super_admin')) ? true : null;
        });

        ResetPassword::createUrlUsing(function (User $notifiable, string $token) {
            return config('app.frontend_url')
                . "/account/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
