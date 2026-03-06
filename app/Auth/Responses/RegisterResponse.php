<?php

namespace App\Auth\Responses;

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

        if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect('/dashboard');
    }
}
