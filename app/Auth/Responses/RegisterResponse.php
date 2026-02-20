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

        return redirect('/dashboard');
    }
}
