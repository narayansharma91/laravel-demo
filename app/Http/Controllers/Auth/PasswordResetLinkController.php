<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordResetLinkRequest;
use App\Traits\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    use ActivityLog;

    public function store(PasswordResetLinkRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->activity(log: 'Password reset fail', properties: [
                'message' => __($status),
                'email' => $request->email,
            ]);
        }

        return response()->json(['status' => __('passwords.sent')]);
    }
}
