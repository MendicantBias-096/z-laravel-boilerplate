<?php

declare(strict_types=1);

namespace App\Modules\Access\Http\Responses;

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
