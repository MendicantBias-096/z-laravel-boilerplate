<?php

declare(strict_types=1);

use App\Enums\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Módulo público
|--------------------------------------------------------------------------
*/
Route::view('/', 'public.home.index')->name('home');

Route::post('/locale', function (Request $request) {
    $request->validate(['locale' => ['required', 'string', 'in:'.implode(',', Language::values())]]);

    return back()->withCookie(
        cookie('locale', $request->locale, 525600, '/', null, null, false, false, 'Lax')
    );
})->name('locale.update');

Route::prefix('')->name('public.')->group(function (): void {
    Route::view('/nosotros', 'public.about.index')->name('about');
});

Route::middleware('guest')->group(function (): void {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::view('/reset-password/{token}', 'auth.reset-password')->name('password.reset');
});

Route::view('/email/verify', 'auth.verify-email')
    ->middleware('auth')
    ->name('verification.notice');
