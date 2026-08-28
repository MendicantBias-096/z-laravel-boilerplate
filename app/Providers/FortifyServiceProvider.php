<?php

namespace App\Providers;

use App\Modules\Access\Actions\Fortify\CreateNewUser;
use App\Modules\Access\Actions\Fortify\ResetUserPassword;
use App\Modules\Access\Http\Responses\LoginResponse;
use App\Modules\Access\Http\Responses\LogoutResponse;
use App\Modules\Access\Http\Responses\RegisterResponse;
use App\Modules\Access\Http\Responses\VerifyEmailResponse;
use App\Modules\Access\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
    public function register(): void
    {
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
        $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);
        $this->app->singleton(VerifyEmailResponseContract::class, VerifyEmailResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::withTrashed()
                ->where('email', $request->email)
                ->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return null;
            }

            if ($user->trashed()) {
                throw ValidationException::withMessages([
                    Fortify::username() => [__('access::auth.account_deleted')],
                ]);
            }

            if (! $user->is_active) {
                throw ValidationException::withMessages([
                    Fortify::username() => [__('access::auth.account_deactivated')],
                ]);
            }

            return $user;
        });

        Fortify::verifyEmailView(fn (): Factory|\Illuminate\Contracts\View\View => view('access::auth.verify-email'));
        Fortify::twoFactorChallengeView(fn (): Factory|\Illuminate\Contracts\View\View => view('access::auth.two-factor-challenge'));

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));
    }
}
