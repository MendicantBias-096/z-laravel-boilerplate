<?php

declare(strict_types=1);

use App\Modules\Access\Http\Resources\UserResource;
use App\Modules\Access\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// El modelo no se devuelve tal cual: `$hidden` decide qué se esconde, y una
// columna nueva entra en la respuesta sin que nadie lo decida. El Resource
// nombra lo que se publica, que es la lista corta.
Route::get('/user', function (Request $request) {
    $user = $request->user();
    assert($user instanceof User);

    return new UserResource($user->load('profile'));
})->middleware('auth:sanctum');
