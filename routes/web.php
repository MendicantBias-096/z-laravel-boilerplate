<?php

use App\Livewire\App\Dashboard;
use App\Livewire\Public\About;
use App\Livewire\Public\Home;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Módulo público
|--------------------------------------------------------------------------
*/
Route::get('/', Home::class)->name('home');

Route::prefix('')->name('public.')->group(function () {
    Route::get('/nosotros', About::class)->name('about');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth.login'))->name('login');
    Route::get('/register', fn () => view('auth.register'))->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});

// Required by Fortify when views => false + resetPasswords is active.
// In headless mode, the frontend handles the UI; this route just satisfies
// the named route requirement used in password reset notification emails.
Route::get('/reset-password/{token}', function () {
    abort(404);
})->name('password.reset');
