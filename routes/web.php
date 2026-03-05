<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Módulo público
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('public.home.index'))->name('home');

Route::prefix('')->name('public.')->group(function () {
    Route::get('/nosotros', fn () => view('public.about.index'))->name('about');
});

Route::get('/locale/{locale}', function (string $locale) {
    $supported = config('app.supported_locales', ['es', 'en']);

    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::get('/register', fn () => view('auth.register'))->name('register');
});

// Required by Fortify when views => false + resetPasswords is active.
// In headless mode, the frontend handles the UI; this route just satisfies
// the named route requirement used in password reset notification emails.
Route::get('/reset-password/{token}', function () {
    abort(404);
})->name('password.reset');
