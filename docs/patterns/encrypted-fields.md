# Campos cifrados (CipherSweet)

**Dependencia (opt-in):** `spatie/laravel-ciphersweet` — **no** viene en el base.
**Stack:** Eloquent · ParagonIE CipherSweet
**Origen:** rescatado de `dayacount` (app financiera; cifra CLABE, beneficiario, límites, notas).

## Problema que resuelve

Cifrado de columnas sensibles **en reposo** dentro de la BD, con opción de
**búsqueda exacta** vía blind index (sin descifrar toda la tabla). Para PII y datos
financieros que no deben quedar en claro en la base ni en backups.

## Cuándo usarlo

- El modelo guarda PII o datos financieros (CLABE, RFC, cuentas, montos, notas).
- Requisito de cumplimiento: datos ilegibles en la BD / dumps.

## Cuándo NO usarlo

- Datos que necesitas **filtrar/ordenar con SQL** (`where`, `ilike`, `orderBy`,
  rangos). Un campo cifrado **no** se puede buscar con `HasTable` ni comparar en SQL;
  solo igualdad exacta vía blind index.
- Apps sin PII: el overhead de gestión de llave no se justifica.

## Instalación (por proyecto)

```bash
ddev composer require spatie/laravel-ciphersweet
ddev exec php artisan vendor:publish --tag="ciphersweet-config"
ddev exec php artisan ciphersweet:generate-key   # imprime la llave
```

En `.env` (y documéntala en `.env.example` sin valor):

```
CIPHERSWEET_KEY=<llave-hex-de-256-bits>
```

Config relevante (`config/ciphersweet.php`): `backend = nacl`, `provider = string`,
la llave sale de `CIPHERSWEET_KEY`.

## Uso

```php
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;

class CreditAccount extends Model implements CipherSweetEncrypted
{
    use UsesCipherSweet;

    public static function configureCipherSweet(EncryptedRow $row): void
    {
        $row
            ->addField('credit_limit')                                   // cifrado, NO buscable
            ->addBlindIndex('clabe', new BlindIndex('clabe_index'));     // cifrado + buscable (igualdad)
    }
}
```

Búsqueda por blind index (única forma de consultar un campo cifrado):

```php
CreditAccount::whereBlind('clabe', 'clabe_index', $clabe)->first();
```

## Gotchas

- **Columnas:** los campos cifrados deben ser `TEXT`/`BLOB` en la migración (el
  ciphertext es más largo que el claro). El blind index vive en su **propia columna**.
- **No filtrable con `HasTable`:** un campo cifrado no entra en `searchable()`/`filterable()`
  (usan `ilike`/SQL). Si necesitas búsqueda parcial, no lo cifres o guarda un derivado.
- **Rotación de llave:** cambiar `CIPHERSWEET_KEY` invalida todo lo cifrado; requiere
  re-cifrar con `artisan ciphersweet:encrypt` / comando de rotación. Respalda la llave.
- **Cifrar existentes:** al agregar cifrado a datos ya guardados, corre
  `ddev exec php artisan ciphersweet:encrypt "App\Models\CreditAccount"`.
- El blind index solo hace **igualdad exacta**, no `LIKE` ni rangos.

## Mejorar cuando

- Se repita el mismo set de campos cifrados en varios modelos → extraer un trait
  `EncryptsPii` con un `configureCipherSweet()` base.
