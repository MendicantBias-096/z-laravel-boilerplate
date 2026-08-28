<?php

use App\Modules\Access\AccessServiceProvider;
use App\Modules\Platform\PlatformServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,

    // Un provider de módulo no se autodescubre: eso solo ocurre con paquetes
    // que declaran `extra.laravel.providers`. Platform va antes que Access
    // porque es la base del grafo (R9).
    PlatformServiceProvider::class,
    AccessServiceProvider::class,
];
