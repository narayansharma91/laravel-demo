<?php

namespace App\Services;

use App\Jobs\VerifyEmailJob;
use App\Models\User;
use Config;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(array $userInfo): User
    {
        return User::create(
            [
                'first_name' => $userInfo['first_name'],
                'last_name' => $userInfo['last_name'] ?? null,
                'email' => $userInfo['email'],
                'password' => Hash::make($userInfo['password']),
            ]
        );
    }

    public function register(array $userInfo): array
    {
        $user = $this->create($userInfo);
        $user->assignRole(Config::get('constants.roles.user'));
        dispatch(new VerifyEmailJob($user))->onQueue('default');
        return $user->toArray();
    }

    public function emailReVerification(User $user): void
    {
        $user->email_verified_at = null;
        $user->sendEmailVerificationNotification();
    }

    public function expireTokens(User $user): void
    {
        $user->tokens()->update(['expires_at' => now()]);
    }
}
