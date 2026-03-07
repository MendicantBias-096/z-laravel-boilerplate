<?php

use App\Enums\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Módulo público
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('public.home.index'))->name('home');

Route::post('/locale', function (Request $request) {
    $request->validate(['locale' => ['required', 'string', 'in:' . implode(',', Language::values())]]);

    return back()->withCookie(
        cookie('locale', $request->locale, 525600, '/', null, null, false, false, 'Lax')
    );
})->name('locale.update');

Route::prefix('')->name('public.')->group(function () {
    Route::get('/nosotros', fn () => view('public.about.index'))->name('about');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::get('/register', fn () => view('auth.register'))->name('register');
    Route::get('/forgot-password', fn () => view('auth.forgot-password'))->name('password.request');
    Route::get('/reset-password/{token}', fn () => view('auth.reset-password'))->name('password.reset');
});

// Required by Fortify when views => false + emailVerification is active.
// Redirects unverified users to the verify-email page.
Route::middleware('auth')->get('/email/verify', fn () => view('auth.verify-email'))
    ->name('verification.notice');
