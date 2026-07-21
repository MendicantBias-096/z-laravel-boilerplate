# Reglas de validación MX

**Código canónico:** `app/Rules/RFCFormat.php`, `NSSFormat.php`, `CurrencyFormat.php`, `PasswordStrength.php`
**Stack:** Illuminate ValidationRule
**Traducciones:** llaves `validation.rfc|nss|currency|password_strength` en `lang/{es,en}/validation.php`

## Problema que resuelve

Validaciones que se repetían proyecto tras proyecto (formatos fiscales mexicanos,
fuerza de contraseña). Reglas puras, sin dependencias, mensajes traducibles.

## Cuándo usarlo

- `RFCFormat` — RFC de persona física (4 letras) o moral (3 letras) + fecha + homoclave. Valida case-insensitive.
- `NSSFormat` — Número de Seguridad Social IMSS (11 dígitos, subdelegación 01-99).
- `CurrencyFormat` — monto con hasta 2 decimales, hasta 15 enteros.
- `PasswordStrength` — mínimo 8 chars, una mayúscula, un número.

## Cuándo NO usarlo

- `PasswordStrength` no reemplaza las reglas de Fortify; **complementa**. Si solo
  quieres longitud/compromiso, usa `Password::defaults()` de Laravel.
- `CurrencyFormat` valida formato, no rango ni moneda; combínalo con `min`/`max`.

## Uso

```php
use App\Rules\RFCFormat;
use App\Rules\CurrencyFormat;

// FormRequest
public function rules(): array
{
    return [
        'rfc' => ['required', 'string', new RFCFormat],
        'nss' => ['nullable', new NSSFormat],
        'monto' => ['required', new CurrencyFormat],
        'password' => ['required', new PasswordStrength],
    ];
}
```

Los mensajes salen de `lang/{locale}/validation.php` vía `->translate()`; edita ahí
el texto por idioma.

## Gotchas

- `RFCFormat` valida el formato, **no** el dígito verificador ni que el RFC exista en el SAT.
- `NSSFormat` no valida el dígito verificador del IMSS (solo estructura de 11 dígitos).
- El mensaje no interpola `:attribute` (se traduce con llave fija); edítalo en `validation.php`.

## Mejorar cuando

- Se necesite CURP → agregar `CURPFormat` siguiendo el mismo patrón.
- Se requiera validar el dígito verificador del RFC/NSS → extender con el algoritmo oficial.
