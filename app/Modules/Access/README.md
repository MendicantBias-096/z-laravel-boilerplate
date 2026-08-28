# Access

## Propósito

Identidad: usuarios, roles, permisos, perfiles y autenticación. Es el otro
módulo de plataforma que todo producto hereda (R5).

## Decisiones

**No se llama `Personal`.** El nombre anterior significaba RRHH en español y
«privado» en inglés, y contenía administración de logins. El día que un producto
tenga empleados de verdad va a necesitar ese nombre.

**`Access\Models\User` es la única excepción permanente de R8**: importable
desde cualquier módulo para relaciones Eloquent, type hints de Policy y
`auth()`. No es un privilegio, es que el framework lo exige — `config/auth.php`
lo apunta, las Policies lo reciben tipado y `Notifiable` lo devuelve.

**Sus URLs no llevan prefijo.** `/users`, no `/access/users`: el namespace
refleja la arquitectura y la URL refleja el producto, y no tienen por qué
coincidir. Los módulos de negocio sí prefijan, para no colisionar.

**Precondición conocida:** `getNameAttribute()` accede a `$this->profile?->…`,
así que `$user->name` es un N+1 si la relación `profile` no viene cargada.

<!-- arch:auto:start -->
## Contrato público

Todavía no expone contratos.

## Eventos

Ninguno todavía.

## Tablas

`users`, `profiles`, y las de Spatie: `roles`, `permissions`, `model_has_*`

## Depende de

`Platform`
<!-- arch:auto:end -->
