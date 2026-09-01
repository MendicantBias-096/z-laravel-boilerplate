<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use RuntimeException;
use TallStackUi\Facades\TallStackUi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[\Override]
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

        // Un `fill()` con una clave que no está en `$fillable` la descarta sin
        // decir nada: el `save()` responde true y el dato no se guardó. En
        // dayacount eso dejó cuentas verificadas después de cambiar el correo,
        // porque el atributo que las desverificaba se caía en silencio.
        //
        // Activo también en producción a propósito. Fallar ruidosamente al
        // escribir mal es preferible a guardar el registro a medias, que es lo
        // que hay que descubrir semanas después leyendo filas incoherentes.
        Model::preventSilentlyDiscardingAttributes();

        $this->refuseToBootWithDebugInProduction();

        // Aquí vivía un `Gate::before` que devolvía true para el rol admin.
        // Un `before` que devuelve true corta antes de toda Policy y no se
        // puede sobrescribir desde ella, así que R39 —«la autorización se
        // decide en la Policy»— era falsa justo para el actor capaz del daño
        // irreversible. No daba acceso a nada: el seeder ya le asigna al admin
        // todos los permisos, y `AdminTienePermisosTest` lo mantiene cierto.
        // Lo que hacía era esconder el día que una Policy dejara de correr.

        if (config('app.debug')) {
            $this->app->booted(fn () => $this->validateMenuRoutes());
        }

        TallStackUi::customize()
            ->form('input')
            ->block('input.base', 'dark:placeholder-dark-400 w-full rounded-md border-0 bg-transparent px-3 py-1.5 ring-0 placeholder:text-gray-400 focus:outline-hidden focus:ring-transparent sm:text-sm sm:leading-6');

        TallStackUi::customize()
            ->card()
            ->block('footer.wrapper', 'text-secondary-700 dark:text-dark-300 dark:border-t-dark-600 rounded-lg rounded-t-none border-t border-t-secondary-200 bg-primary-50 dark:bg-primary-950/60 px-4 py-2');

    }

    /**
     * `APP_DEBUG=true` en producción publica la traza completa de cada error
     * —conexiones, claves, rutas del disco— y enciende debugbar, porque
     * `config/debugbar.php` no tiene default propio y cae en `app.debug`.
     *
     * Caer al arrancar es lo que se quiere: `.env.example` trae `APP_DEBUG=true`
     * porque es la plantilla de desarrollo, y es la misma que copia quien
     * despliega con prisa. Un recordatorio en un checklist no lo impide; esto
     * sí, y lo hace antes de servir la primera petición.
     */
    private function refuseToBootWithDebugInProduction(): void
    {
        if ($this->app->isProduction() && config('app.debug') === true) {
            throw new RuntimeException(
                'APP_DEBUG=true con APP_ENV=production: expone la traza de cada error y enciende debugbar. Pon APP_DEBUG=false.'
            );
        }
    }

    private function validateMenuRoutes(): void
    {
        $items = config('menu.menu', []);
        $check = function (array $items) use (&$check): void {
            foreach ($items as $item) {
                if (isset($item['route']) && ! Route::has($item['route'])) {
                    Log::warning("[menu] Ruta '{$item['route']}' definida en config/menu.php no existe.");
                }
                if (isset($item['items'])) {
                    $check($item['items']);
                }
            }
        };
        $check($items);
    }
}
