<?php

namespace App\Auth\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Authenticated successfully.',
                'user' => $request->user(),
            ]);
        }

        return redirect()->intended('/dashboard');
    }
}
