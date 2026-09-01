<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Copias de seguridad.
 *
 * Las tres tareas son una sola cosa y por eso van juntas: `backup:run` deja la
 * copia, `backup:clean` impide que el disco se llene hasta que la copia
 * empiece a fallar, y `backup:monitor` avisa cuando la más reciente es
 * demasiado vieja. Sin la tercera, un backup que lleva semanas fallando se
 * parece mucho a uno que funciona.
 *
 * `withoutOverlapping` porque una copia lenta no debe solaparse con la
 * siguiente sobre la misma base de datos.
 *
 * Esto solo corre si algo invoca `schedule:run` cada minuto. En un despliegue
 * sin cron —o sin `--schedule` en el contenedor— las tres callan, y callar es
 * indistinguible de funcionar.
 */
Schedule::command('backup:clean')->daily()->at('01:00')->withoutOverlapping();
Schedule::command('backup:run')->daily()->at('01:30')->withoutOverlapping();
Schedule::command('backup:monitor')->daily()->at('02:30');
