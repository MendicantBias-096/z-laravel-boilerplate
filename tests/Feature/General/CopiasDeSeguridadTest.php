<?php

declare(strict_types=1);

namespace Tests\Feature\General;

use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification;
use Tests\TestCase;

/**
 * Un backup configurado y un backup que funciona no son lo mismo, y el que
 * falla lo hace en silencio hasta el día que hace falta.
 *
 * Lo que estos casos cubren es la configuración, que es lo que se degrada solo
 * cuando alguien reactiva un aviso o cambia un destino sin mirar el otro.
 *
 * **Lo que no cubren, dicho para que nadie lo dé por hecho:** ejecutar el
 * backup de verdad. Se probó a mano y entero —`backup:run --only-db`, y el
 * dump restaurado en una base vacía devolvió los usuarios y los roles—, pero
 * no está automatizado: el dump necesita que `pg_dump` pueda autenticarse, y
 * eso cambia por máquina (en DDEV hace falta `PGPASSWORD` porque el cliente de
 * esa imagen ignora `PGPASSFILE`). El empaquetado de ficheros sí se intentó
 * automatizar y se retiró: pasa en local y falla en el runner del CI con
 * `ZipArchive::close(): Invalid argument`, y un caso que solo es rojo en un
 * sitio enseña a ignorar el rojo.
 */
class CopiasDeSeguridadTest extends TestCase
{
    /**
     * Avisar de cada copia correcta enseña a ignorar el aviso, y entonces el
     * que importa —el de la copia que falló— llega a un buzón donde ya nadie
     * mira. Solo se notifica lo que exige actuar.
     */
    public function test_solo_se_notifica_lo_que_exige_actuar(): void
    {
        $notificaciones = config('backup.notifications.notifications');

        foreach ([
            BackupWasSuccessfulNotification::class,
            HealthyBackupWasFoundNotification::class,
            CleanupWasSuccessfulNotification::class,
        ] as $rutinaria) {
            $this->assertSame([], $notificaciones[$rutinaria] ?? null);
        }

        $this->assertNotEmpty(
            $notificaciones[BackupHasFailedNotification::class] ?? [],
            'Sin aviso de fallo nadie se entera de que hace semanas que no hay copia.',
        );
    }

    /**
     * `backup:monitor` es lo que distingue un backup que lleva semanas
     * fallando de uno que funciona, así que mira el mismo destino que escribe.
     */
    public function test_el_monitor_vigila_el_mismo_destino_que_se_escribe(): void
    {
        $this->assertSame(
            config('backup.backup.destination.disks'),
            config('backup.monitor_backups.0.disks'),
        );
    }

    /**
     * Un destinatario nulo no deja el aviso sin enviar: tira
     * `package:discover`, y con él `composer install`. Lo descubrió el CI, que
     * no tiene `.env`, y es exactamente lo que le pasaría a un proyecto hijo
     * recién clonado.
     */
    public function test_la_instalacion_no_depende_de_variables_de_entorno_de_correo(): void
    {
        // La clave presente y vacía es el caso que se escapó la primera vez:
        // `env('X', 'default')` devuelve `''`, no el default, y `''` tampoco es
        // un correo válido para spatie.
        putenv('BACKUP_NOTIFICATION_EMAIL=');
        putenv('MAIL_FROM_ADDRESS=');

        $destinatario = require config_path('backup.php');
        $destinatario = $destinatario['notifications']['mail']['to'];

        $this->assertIsString($destinatario);
        $this->assertNotSame('', $destinatario);
    }
}
