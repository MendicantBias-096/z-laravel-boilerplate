<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use TallStackUi\Facades\TallStackUi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(fn () => Password::min(8));

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        TallStackUi::personalize()
            ->form('input')
            ->block('input.base', 'dark:placeholder-dark-400 w-full rounded-md border-0 bg-transparent px-3 py-1.5 ring-0 placeholder:text-gray-400 focus:outline-hidden focus:ring-transparent sm:text-sm sm:leading-6');

        TallStackUi::personalize()
            ->card()
            ->block('footer.wrapper', 'text-secondary-700 dark:text-dark-300 dark:border-t-dark-600 rounded-lg rounded-t-none border-t border-t-secondary-200 bg-primary-50 dark:bg-primary-950/60 px-4 py-2');

    }
}
