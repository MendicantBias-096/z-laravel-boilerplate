# ObfuscatedPathGenerator

**Código canónico:** `app/Services/MediaLibrary/ObfuscatedPathGenerator.php`
**Stack:** Spatie Media Library

## Problema que resuelve

Guardar documentos privados en rutas **ofuscadas** (`md5(media->id + app.key)/`)
para que la ruta física no revele el modelo, el orden de subida ni sea adivinable.
Pensado para archivos que siempre se sirven a través de la app, nunca por URL directa.

## Cuándo usarlo

- Documentos sensibles (INE, contratos, comprobantes) en disco privado.

## Cuándo NO usarlo

- Imágenes públicas (avatares, portadas) → deja el `DefaultPathGenerator`.

## Uso

Registrar en `config/media-library.php`:

```php
'path_generator' => App\Services\MediaLibrary\ObfuscatedPathGenerator::class,
```

O por modelo, en `custom_path_generators`:

```php
'custom_path_generators' => [
    App\Models\Requisition::class => App\Services\MediaLibrary\ObfuscatedPathGenerator::class,
],
```

Sírvelos siempre vía controlador/acción con autorización, nunca exponiendo la URL del disco.

## Gotchas

- **APP_KEY como sal:** si rotas `APP_KEY`, los paths existentes quedan huérfanos
  (los archivos siguen en su carpeta vieja pero el generador calcula otra ruta).
  Marcado con `ponytail:` en el código. Si necesitas rotar la key, cambia la sal a
  una columna estable (ej. `uuid` del media) y migra los archivos.
- El id del media existe recién tras insertarlo, así que la ruta es estable post-creación.

## Mejorar cuando

- Se necesite rotar APP_KEY → migrar la sal a `uuid` y mover archivos con un comando.
