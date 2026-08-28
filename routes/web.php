<?php

declare(strict_types=1);

use App\Modules\Platform\Enums\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Módulo público
 */
Route::view('/', 'platform::public.home.index')->name('home');

Route::post('/locale', function (Request $request) {
    $request->validate(['locale' => ['required', 'string', 'in:'.implode(',', Language::values())]]);

    return back()->withCookie(
        cookie('locale', $request->locale, 525600, '/', null, null, false, false, 'Lax')
    );
})->name('locale.update');

Route::prefix('')->name('platform::public.')->group(function (): void {
    Route::view('/nosotros', 'platform::public.about.index')->name('about');
});

Route::middleware('guest')->group(function (): void {
    Route::view('/login', 'access::auth.login')->name('login');
    Route::view('/register', 'access::auth.register')->name('register');
    Route::view('/forgot-password', 'access::auth.forgot-password')->name('password.request');
    Route::view('/reset-password/{token}', 'access::auth.reset-password')->name('password.reset');
});

Route::view('/email/verify', 'access::auth.verify-email')
    ->middleware('auth')
    ->name('verification.notice');
