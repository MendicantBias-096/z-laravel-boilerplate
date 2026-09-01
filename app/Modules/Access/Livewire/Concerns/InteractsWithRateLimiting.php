<?php

declare(strict_types=1);

namespace App\Modules\Access\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Límite de intentos para las acciones de autenticación.
 *
 * Vive aquí y no en un middleware porque una acción de Livewire viaja a
 * `/livewire/update`: el `throttle:` de la ruta protege la pantalla, no el
 * método que de verdad prueba credenciales o manda correo.
 *
 * Qué entra en la clave depende del ataque que se frena, y equivocarse deja el
 * límite decorativo:
 *
 *   Adivinar la contraseña de alguien  →  correo + IP. Solo la IP castiga a una
 *                                         oficina entera por un usuario torpe.
 *   Crear cuentas o mandar correos     →  solo IP. Con el correo en la clave,
 *                                         cambiarlo en cada intento esquiva el
 *                                         límite sin coste, que es justo lo que
 *                                         hace quien abusa del alta.
 */
trait InteractsWithRateLimiting
{
    /**
     * ¿Se agotaron los intentos? Registra el intento cuando todavía quedan.
     *
     * Devuelve los segundos que faltan, o `null` si se puede seguir. Quien
     * llama decide si eso se dice en pantalla: en el login sí, y en la
     * recuperación de contraseña no, porque el mensaje distinguiría un correo
     * registrado de uno que no lo está.
     */
    protected function rateLimitExceeded(string $action, ?string $identifier, int $maxAttempts): ?int
    {
        $key = $this->rateLimitKey($action, $identifier);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return RateLimiter::availableIn($key);
        }

        RateLimiter::hit($key);

        return null;
    }

    protected function clearRateLimit(string $action, ?string $identifier): void
    {
        RateLimiter::clear($this->rateLimitKey($action, $identifier));
    }

    private function rateLimitKey(string $action, ?string $identifier): string
    {
        $parts = [$action, request()->ip()];

        if ($identifier !== null) {
            $parts[] = Str::lower($identifier);
        }

        return implode('|', $parts);
    }
}
