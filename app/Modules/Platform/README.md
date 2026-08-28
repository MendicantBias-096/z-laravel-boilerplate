# Platform

## Propósito

Lo que todo producto hereda y no pertenece a ningún negocio: configuración de
la aplicación, notificaciones, las piezas de interfaz compartidas y las reglas
de validación que se usan en más de un sitio.

Es la base del grafo de dependencias (R9): **no depende de ningún otro módulo**.
Lo que necesite de arriba se invierte con un contrato que Platform define y otro
módulo implementa.

## Decisiones

**Es un shared kernel, no un bounded context.** No tiene lenguaje de negocio
propio y no se extrae: se copia. Por eso R7 lo gobierna por reutilización —
nada entra hasta tener tres consumidores— y no por pertenencia semántica.

La cicatriz que lo justifica: `DownloadsDocuments`, `HandlesModelDocuments` y
`HandlesMultipleDocuments` sumaban 269 líneas con cero usos, tan invisibles que
PHPStan las reportaba como `used zero times and is not analysed`. Se borraron y
su código vive en `docs/patterns/`, que es donde vive un patrón que se copia
cuando aparece la necesidad.

<!-- arch:auto:start -->
## Contrato público

Todavía no expone contratos.

## Eventos

Emite: `NewNotification`

## Tablas

`settings`, `notifications`

## Depende de

Nada. Es la raíz del grafo.
<!-- arch:auto:end -->
