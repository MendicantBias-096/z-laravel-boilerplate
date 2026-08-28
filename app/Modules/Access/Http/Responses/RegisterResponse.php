<?php

declare(strict_types=1);

namespace App\Modules\Access\Http\Responses;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Registration successful.',
                'user' => $request->user(),
            ], 201);
        }

        $user = $request->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect('/dashboard');
    }
}
