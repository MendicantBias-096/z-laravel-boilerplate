<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Módulo: Ventas
|--------------------------------------------------------------------------
| Prefijo: /sales             Nombre base: sales.
|
| Patrón para nuevas rutas:
|   Route::get('/customers', \App\Livewire\App\Sales\Customers\Index::class)
|       ->name('customers.index');
*/

Route::prefix('sales')->name('sales.')->group(function () {

    Route::get('/dashboard', \App\Livewire\App\Sales\Dashboard::class)
        ->name('dashboard');

    // Route::get('/customers', \App\Livewire\App\Sales\Customers\Index::class)->name('customers.index');
    // Route::get('/reports',   \App\Livewire\App\Sales\Reports\Index::class)->name('reports.index');

});
