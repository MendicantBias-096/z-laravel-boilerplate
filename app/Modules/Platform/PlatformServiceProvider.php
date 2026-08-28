<?php

declare(strict_types=1);

namespace App\Modules\Platform;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

/**
 * Registra lo que Laravel encontraría solo si el módulo viviera en la raíz.
 *
 * Dentro de `app/Modules/` la convención de rutas deja de aplicar, así que
 * cada pieza se declara: migraciones, vistas, traducciones, rutas, config y
 * los componentes Livewire. Las Policies son la excepción —
 * `Gate::guessPolicyName()` sustituye `\Models\` por `\Policies\` y las
 * encuentra sola.
 *
 * El provider tampoco se autodescubre: eso solo ocurre con paquetes que
 * declaran `extra.laravel.providers`, así que vive en `bootstrap/providers.php`.
 */
class PlatformServiceProvider extends ServiceProvider
{
    /** El prefijo con el que se citan las vistas y los componentes. */
    private const NAMESPACE = 'platform';

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/Config/notifications.php', 'notifications');
    }

    public function boot(): void
    {
        Relation::morphMap([
            'setting' => Models\Setting::class,
        ]);

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/Resources/views', self::NAMESPACE);
        $this->loadTranslationsFrom(__DIR__.'/Resources/lang', self::NAMESPACE);

        // Los componentes se citan `platform::settings.index`. Se usa
        // addNamespace y no addLocation porque el segundo deriva el nombre
        // recortando el prefijo, así que dos módulos con la misma subcarpeta
        // producirían el mismo nombre y ganaría el registrado primero, sin
        // decir nada.
        Livewire::addNamespace(
            self::NAMESPACE,
            viewPath: __DIR__.'/Resources/views',
            classNamespace: __NAMESPACE__.'\\Livewire',
        );

        Blade::anonymousComponentNamespace(__DIR__.'/Resources/views/components', self::NAMESPACE);

        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        require __DIR__.'/Routes/breadcrumbs.php';
    }
}
