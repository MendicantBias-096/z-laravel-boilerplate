<?php

declare(strict_types=1);

namespace Tests\Feature\General;

use Illuminate\Support\Facades\Storage;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification;
use Tests\TestCase;

/**
 * Un backup configurado y un backup que funciona no son lo mismo, y el que
 * falla lo hace en silencio hasta el día que hace falta.
 *
 * El dump de la base no se ejercita aquí: depende de que `pg_dump` exista y
 * pueda autenticarse, que es distinto en cada máquina —en DDEV hace falta
 * `PGPASSWORD` porque el cliente de esa imagen ignora `PGPASSFILE`—. Ese
 * camino se probó a mano restaurando el dump en una base vacía; lo que estos
 * casos cubren es que la cadena de empaquetado y escritura funciona y que la
 * configuración no se degrada sola.
 */
class CopiasDeSeguridadTest extends TestCase
{
    public function test_el_backup_de_ficheros_produce_un_zip(): void
    {
        Storage::fake('local');

        $this->artisan('backup:run', ['--only-files' => true, '--disable-notifications' => true])
            ->assertSuccessful();

        $ficheros = Storage::disk('local')->allFiles();

        $this->assertNotEmpty($ficheros, 'El backup no dejó ningún archivo en el disco de destino.');
        $this->assertStringEndsWith('.zip', $ficheros[0]);
    }

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
