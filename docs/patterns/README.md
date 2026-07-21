# Patrones reutilizables

Catálogo vivo de convenciones del boilerplate. Cada patrón nació de código que
se repitió en proyectos reales (varios rescatados de `unimo-server` /
`unimo-pay-server`) y se generalizó aquí para no volver a copiarlo.

La **implementación canónica vive en `app/`**; estas páginas guardan el *por qué*
y el *cuándo* — la parte que se pierde cuando solo copias archivos entre proyectos.

## Regla de oro: rule of three

- Un patrón repetido **2 veces** en un proyecto → déjalo, aún no conoces su forma final.
- A la **3ª repetición** → generalízalo, súbelo a `app/` del boilerplate y crea su página aquí.
- Cuando un proyecto real mejora un patrón → el cambio **vuelve al boilerplate**, no se queda huérfano en el proyecto.

## Cómo agregar un patrón

1. Copia `_template.md` a `docs/patterns/<nombre>.md`.
2. Escribe la implementación canónica en `app/` (Trait, Rule, Service…).
3. Llena la página: problema, cuándo usarlo (y cuándo NO), código, gotchas, "mejorar cuando".
4. Agrégalo al índice de abajo.

## Índice

| Patrón | Qué resuelve | Código |
|--------|--------------|--------|
| [has-table](has-table.md) | Búsqueda, orden, filtros y paginación de tablas Livewire sin copy-paste | `app/Traits/Livewire/HasTable.php` |
| [handles-model-documents](handles-model-documents.md) | Documentos con slot nombrado (1 por colección): subir/preview/borrar/descargar | `app/Traits/Livewire/HandlesModelDocuments.php` |
| [handles-multiple-documents](handles-multiple-documents.md) | Muchos archivos en una colección con buffer anti-sobrescritura | `app/Traits/Livewire/HandlesMultipleDocuments.php` |
| [downloads-documents](downloads-documents.md) | Exportar todos los documentos de un modelo en un ZIP | `app/Traits/Livewire/DownloadsDocuments.php` |
| [obfuscated-path-generator](obfuscated-path-generator.md) | Rutas de almacenamiento ofuscadas para documentos privados | `app/Services/MediaLibrary/ObfuscatedPathGenerator.php` |
| [validation-rules](validation-rules.md) | Reglas de validación MX: RFC, NSS, moneda, fuerza de contraseña | `app/Rules/*.php` |
| [encrypted-fields](encrypted-fields.md) | Cifrado de columnas PII/financieras con búsqueda por blind index (opt-in) | `spatie/laravel-ciphersweet` |

## Origen (rescate de proyectos)

Estos patrones se rescataron de proyectos previos:

- **unimo-server / unimo-pay** → suite de documentos, reglas de validación MX, `HasTable`.
- **dayacount** → `encrypted-fields` (CipherSweet). Es opt-in: se documenta el patrón,
  la dependencia no se agrega al base.

Lo que **no** se portó por ser de negocio o inferior a lo existente: publishers de
redes sociales, horarios/materias/grupos y filtros de rappasoft de unimo (el
boilerplate usa tablas TallStackUI custom); `NotificationsService` de unimo (el del
boilerplate ya es superior); tools MCP y Listeners de dominio financiero de dayacount
(MCP ya existe en el base vía `laravel/boost`).
