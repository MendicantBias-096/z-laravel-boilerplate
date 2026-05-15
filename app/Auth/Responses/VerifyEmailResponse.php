<?php

declare(strict_types=1);

namespace App\Auth\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    public function toResponse($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Email verified successfully.']);
        }

        return redirect('/dashboard')->with('toast', [
            'type' => 'success',
            'title' => __('public.email_verified_title'),
            'message' => __('public.email_verified_message'),
        ]);
    }
}
