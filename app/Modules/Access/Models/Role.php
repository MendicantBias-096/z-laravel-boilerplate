<?php

declare(strict_types=1);

namespace App\Modules\Access\Models;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property string $display_name
 * @property bool $is_protected
 */
class Role extends SpatieRole implements AuditableContract
{
    use Auditable;

    /**
     * Un rol protegido es el que declara `Access/Config/permissions.php`.
     *
     * El seeder lo devuelve a lo que dice el config en cada `db:seed`, así que
     * la interfaz no debe ofrecer cambios que se van a deshacer solos.
     */
    protected function casts(): array
    {
        return ['is_protected' => 'boolean'];
    }
}
