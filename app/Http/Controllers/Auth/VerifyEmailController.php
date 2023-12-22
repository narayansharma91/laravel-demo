<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class VerifyEmailController extends Controller
{
    public function __construct()
    {
        Auth::loginUsingId(request()->route('id'));
    }

    public function __invoke(
        EmailVerificationRequest $request
    ): RedirectResponse {
        $url = config('app.frontend_url');
        $path = config('frontend.verified_email_redirect');
        $user = $request->user();
        $param = Arr::query(['user_name' => $user->first_name]);
        $redirect = redirect()->intended($url . $path . "?$param");
        if ($request->query('no-redirect')) {
            $redirect = redirect()->back();
        }
        if ($user->hasVerifiedEmail()) {
            Auth::logout();

            return $redirect;
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        Auth::logout();

        return $redirect;
    }
}
