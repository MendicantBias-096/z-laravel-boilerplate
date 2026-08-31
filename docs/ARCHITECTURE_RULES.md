# Reglas de arquitectura

Monolito modular. Se despliega como una sola aplicación, y cada módulo debe
poder extraerse a un servicio independiente **sin reescribirlo**. Esa
extractabilidad es el criterio de diseño, aunque nunca se ejerza.

No es hipotético: `LDT` (Livewire Dayatables) y `DUI` (Daya UI) ya están
planeados como extracciones de este repositorio.

## Cómo se cita

Cada regla tiene un ID: `R1`, `R2`, … En code review se cita el ID («esto viola
R13»).

**La numeración se congela al taggear la versión** (R36). A partir de ese
momento los IDs no se reordenan ni se reciclan: una regla retirada se marca
`RETIRADA` y su número queda quemado para siempre. Mientras el documento sea
borrador sin tag, se renumera libremente.

## Cómo se lee una regla

```
R{n} — Enunciado en una frase.

> Enforcement: <herramienta> · <comando> · Severidad: error | warning | guideline
> Escape: <mecanismo de excepción>

Motivo. Por qué existe, y la cicatriz que la originó.
Ejemplo correcto y contraejemplo.
```

**`error`** rompe el build. **`warning`** avisa y se puede silenciar con
anotación. **`guideline`** no se verifica: es prosa, y se sabe que lo es.

De las 58 reglas, **41 son `error`**, 10 son `warning` y 7 son `guideline`.

Ese reparto no mide calidad, mide herramienta disponible: una regla es `error`
solo si existe hoy algo que la compruebe sin falsos positivos. Las que
describen un criterio que ninguna máquina puede decidir son `guideline`, y su
línea de Enforcement lo dice con un guion en vez de nombrar un verificador que
no va a existir. Un check que pasa mientras la regla se viola es peor que no
tenerlo: deja el build en verde y la conclusión equivocada escrita.

## Las cuatro válvulas de escape

Toda regla se puede exceptuar. Ninguna excepción es invisible.

**Permanente** — vive en la configuración de reglas (`phpstan.neon`), no en el
código. Modificar ese archivo salta en el diff y requiere revisión humana.
Ejemplo: `Access\Models\User` es importable desde cualquier módulo (R8).

**Puntual** — comentario con formato fijo, **con fecha de caducidad**:

```php
// arch-exception: R13 · Livewire necesita el modelo · @arturo · 2026-12-31
```

`scripts/arch-lint.sh` valida el formato y **falla cuando la fecha pasa**. Sin
fecha no compila. Con fecha vencida, tampoco.

**Aceptada** — una decisión revisada que no va a cambiar: un archivo que se
lee mejor entero, un `exists:` contra una tabla que nadie va a reescribir.
Mismo formato, sin fecha, y por eso con dueño obligatorio:

```php
// arch-accepted: R52 · tabla de conversiones, no se parte · @arturo
```

La diferencia con la anterior no es de trámite. Una excepción con fecha dice
«esto se arregla»; una sin fecha dice «esto es así». Ponerle fecha a lo segundo
no gestiona nada: enseña a correr fechas, que es el archivo de 1806 líneas otra
vez, ahora con formato.

**Deuda existente** — baseline de PHPStan, con fecha de vencimiento global
(R55).

**El agente nunca se pone de dueño.** Si necesita una excepción, se detiene y
pregunta. Es el único punto de estas reglas donde debe parar.

## Dónde corre cada verificación

| Capa | Presupuesto | Qué corre |
|---|---|---|
| `pre-commit` | ~1 s | Pint `--dirty` + `arch-lint.sh --files=<staged>` |
| `pre-push` | ~20 s | PHPStan (con PHPat y disallowed-calls) + `arch:check` + tests |
| `make check` | ~60 s | todo lo anterior + Rector `--dry-run` + `bun run build` |
| CI | ~2 min | todo, más los checks de esquema, y es la única fuente de verdad |

Los verificadores propios están en **tres capas**, y no por gusto: no hay PHP
en el host, así que cualquier comando de Laravel arranca en ~1 s
(`ddev exec` 0.73 s + bootstrap 0.3 s) y no cabe en el pre-commit junto a Pint.

- **`scripts/arch-lint.sh`** — bash y `grep`. Los checks **textuales**. 0 s, no
  necesita DDEV levantado. Acepta `--rule=R25` y `--files=`.
- **`php artisan arch:check`** — los que necesitan **entender el código**, no
  solo su texto: sincronía del README, ciclos del grafo, AST.
- **`php artisan arch:check` tras `migrate`** — los que preguntan por el
  **esquema**: FKs cruzadas (R27), tipo de clave (R30), pivotes (R32). Solo
  corren en el job que tiene Postgres, y por eso no están en el pre-push.

La línea entre las dos primeras: *si el check necesita entender el código y no
solo su texto, va a PHP.* La tercera se separa porque leer las migraciones para
adivinar el esquema es reimplementar Laravel, y preguntárselo a Postgres son
dos consultas exactas.

Válvula: `--no-verify` en los hooks locales, `--admin` en el merge. Las dos
dejan rastro; ninguna es el camino por defecto.

---

# §1 · Vocabulario y estructura

## R1 — «Módulo» es un bounded context; «pantalla» es una vista con su componente Livewire.

> Enforcement: — · — · Severidad: guideline
> Escape: no aplica

**Qué significa.** Dos palabras que en este proyecto significan cosas
distintas. Un **módulo** es un área completa del negocio (facturación,
inventario) con todo lo suyo dentro. Una **pantalla** es una vista que el
usuario ve. Antes ambas se llamaban «módulo» y por eso había confusión.

Antes de esta regla, «dominio» significaba una carpeta de UI (`General`,
`Personal`) y «módulo» significaba una pantalla (`Users`, `Dashboard`). La
literatura de monolito modular usa «módulo» para lo primero. Esa colisión hace
que «los módulos no se importan entre sí» se aplique al nivel equivocado.

| Término | Significa | Ejemplo |
|---|---|---|
| **Módulo** | bounded context, carpeta en `app/Modules/` | `Access`, `Billing` |
| **Pantalla** | una vista con su componente Livewire | `Users/Index`, `Dashboard` |
| **Plataforma** | los módulos que todo producto hereda | `Access`, `Platform` |
| **Negocio** | los módulos que agrega el producto | `Billing`, `Inventory` |

La palabra «dominio» no se usa. Significaba dos cosas y ya no significa
ninguna.

## R2 — Un módulo es una carpeta bajo `app/Modules/{Contexto}/`.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: permanente

**Qué significa.** Cada área del negocio vive en su propia carpeta dentro de
`app/Modules/`. Todo lo que necesita —sus datos, su lógica, sus pantallas—
está ahí dentro y no repartido por el proyecto.

Un solo `composer.json`, un solo autoload PSR-4. La frontera la vigila el
análisis estático, no el autoloader — que es lo que ocurre también con paquetes
separados, así que la ceremonia operativa de los path repositories no compra
una frontera que no tengamos ya.

Extraer un módulo después es mover la carpeta y escribirle un `composer.json`.

## R3 — El mínimo de un módulo es su ServiceProvider y su `README.md`.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: permanente

**Qué significa.** Para que una carpeta cuente como módulo solo hacen falta
dos archivos: uno que la registra en la aplicación y un README que explique
para qué existe. Todo lo demás es opcional: hay módulos que ni siquiera tienen
pantallas.

Rutas, vistas y componentes Livewire son opcionales. Un módulo puede ser solo
jobs, eventos y un contrato.

Dos archivos, pero el ServiceProvider no está vacío: Laravel descubre por
convención de rutas, y dentro de un módulo esa convención no aplica. Registra
siete piezas, y por eso se genera desde un stub en vez de escribirse a mano:

```php
$this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
$this->loadViewsFrom(__DIR__.'/Resources/views', 'billing');
$this->loadTranslationsFrom(__DIR__.'/Resources/lang', 'billing');
$this->loadRoutesFrom(__DIR__.'/Routes/web.php');
require __DIR__.'/Routes/breadcrumbs.php';
$this->mergeConfigFrom(__DIR__.'/Config/permissions.php', 'billing.permissions');
Livewire::addNamespace('billing', __NAMESPACE__.'\\Livewire', __DIR__.'/Resources/views');
```

Las Policies son la única pieza que no se registra: `Gate::guessPolicyName()`
sustituye `\Models\` por `\Policies\` y encuentra sola a
`Modules\Billing\Policies\InvoicePolicy`.

El provider tampoco se autodescubre —eso solo aplica a paquetes con
`extra.laravel.providers`— así que se añade a `bootstrap/providers.php`. Es el
segundo paso remoto del proyecto, junto al menú de R41.

Cicatriz: `create-module` obligaba a generar un Dashboard al crear un dominio.
El resultado fue `General/Dashboard/Index.php`, 27 líneas, con una vista
placeholder — una pantalla que existió porque la estructura la exigía. Un
humano la borra por vergüenza; un agente la deja, porque la regla decía que
fuera.

## R4 — El README de un módulo tiene una zona generada desde el código, y el build falla si está desincronizada.

> Enforcement: script propio · `php artisan arch:check` · Severidad: error
> Escape: puntual

**Qué significa.** Parte del README de cada módulo la escribe un comando
automáticamente leyendo el código: qué expone, qué eventos usa, qué tablas
tiene. Si alguien cambia el código y no actualiza el README, el build avisa.
Así la documentación no puede mentir.

```markdown
# Billing

## Propósito                        ← a mano
## Decisiones                       ← a mano: el porqué, las cicatrices

<!-- arch:auto:start -->
## Contrato público                 ← generado desde Contracts/
## Eventos                          ← generado: emite / escucha
## Tablas                           ← generado desde sus migraciones
## Depende de                       ← generado desde los imports reales
<!-- arch:auto:end -->
```

`php artisan module:sync-docs` regenera la zona marcada; `arch:check` la compara.

Motivo: documentación obligatoria que nadie verifica se convierte en
documentación que miente, y **agrega autoridad a la mentira**. Ya hay cuatro
pruebas de que pasa, en los dos archivos que un agente lee primero y siempre:

```
README.md:1   "Laravel 12 Boilerplate"       → es 13
CLAUDE.md     "spatie/laravel-permission v7" → es ^8.0
CLAUDE.md     "tallstackui v3"               → v4 disponible
CLAUDE.md     "dedoc/scramble v0.13"         → no está instalado
```

Es el mismo principio que R51 aplica a los métodos: **lo que puede derivarse
del código se deriva; lo escrito a mano se limita a lo que el código no dice.**

Ya está medio construido: `app/Mcp/Tools/GetModuleStructureTool.php` (208
líneas) hace esta introspección para el MCP server.

Efecto lateral: la sección **«Depende de»** es una segunda verificación de R9
en formato legible. Abrir el README de `Billing` y leer
`Depende de: Platform, Access(público)` es la arquitectura declarada y
verificada en la misma línea.

Riesgo conocido: un agente puede escribir dentro de la zona generada y el
siguiente `sync-docs` lo borra. El check lo detecta antes del commit.

## R5 — La plataforma son exactamente dos módulos: `Access` y `Platform`.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: permanente

**Qué significa.** Hay dos módulos que vienen con el boilerplate y todo
producto hereda: **Access** (usuarios, roles y permisos) y **Platform**
(configuración, notificaciones y piezas de interfaz compartidas). Los módulos
que tú escribas van aparte.

`Access` — identidad, usuarios, roles, permisos, perfiles, autenticación.
`Platform` — configuración, notificaciones, traits de UI, layouts, reglas de
validación compartidas.

Ningún módulo de negocio puede llamarse igual ni añadirse a esta lista sin
tocar la configuración de reglas.

**`Access` no se llama `Personal`.** El nombre anterior significaba RRHH en
español y «privado» en inglés, y contenía administración de logins. El día que
un producto tenga empleados de verdad va a necesitar ese nombre.

**Alcance del renombre.** El namespace refleja la arquitectura; la URL refleja
el producto, y no tienen por qué coincidir:

| | Antes | Ahora |
|---|---|---|
| Namespace | `App\Livewire\App\Personal\User` | `App\Modules\Access\Livewire\Users` |
| Route name | `personal.usuarios.index` | `access.users.index` |
| URL | `/personal/usuarios` | `/users` |

Los módulos de **plataforma no prefijan sus URLs** — `/access/users` expone un
nombre de arquitectura a un humano, y `CLAUDE.md:214` ya declaraba `/users`
como convención. Los de **negocio sí**: `/billing/invoices`, para no colisionar.

Cicatriz que esto resuelve: hoy hay tres formas para la misma cosa —
`Personal\User` (singular), `views/app/personal/users` (plural),
`/personal/usuarios` (español).

## R6 — Dentro de un módulo, las carpetas son por tipo, no por capa.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: permanente

**Qué significa.** Dentro de un módulo, cada tipo de archivo tiene su carpeta:
los modelos en `Models/`, los casos de uso en `Actions/`, las pantallas en
`Livewire/`. No hay que decidir nada: si escribes una notificación, va en
`Notifications/`.

```
app/Modules/Billing/
├── Contracts/              ← público (R8): interfaces, sus DTOs y sus excepciones
├── Events/                 ← público (R8)
├── Models/
├── Actions/
├── Data/                   ← DTOs internos, no publicados
├── Enums/
├── Exceptions/             ← las que no salen por un contrato
├── Http/                   ← middleware y responses propios del módulo
├── Listeners/
├── Observers/
├── Rules/
├── Services/               ← lo que aún no se ha partido en Actions
├── Traits/
├── Notifications/
├── Policies/
├── Jobs/
├── Console/
├── Livewire/
├── Database/{Migrations,Seeders,Factories}
├── Tests/{Unit,Feature}    ← autoload-dev + exclude-from-classmap
├── Resources/views/        ← namespace: view('billing::invoices.index')
├── Resources/lang/         ← namespace: __('billing::invoices.title')
├── Config/permissions.php
├── Routes/{web,breadcrumbs}.php
├── BillingServiceProvider.php
└── README.md
```

**El DTO de un contrato vive dentro de `Contracts/`, y su excepción también.**
No es cosmética: R8 dice que lo público es `Contracts/` y `Events/` y nada más,
así que un `ItemSummary` en `Data/` sería imposible de importar por quien
consume el contrato que lo devuelve, y el `@throws` de R51 no tendría `catch`
escribible. Lo que un contrato menciona en su firma es parte del contrato.

`Data/` es para lo que no cruza la frontera. `Exceptions/` igual.

Las factories necesitan una línea más: el resolver de Eloquent busca
`Database\Factories\…` a partir de `App\`, y para un modelo de módulo apunta a
una clase que no existe. Como `Factory::guessFactoryNamesUsing()` es un static
global y no admite una versión por módulo, cada modelo declara su
`newFactory()`. Es greppable, que es lo que lo hace verificable.

Todo en PascalCase: dos convenciones de nombre en el mismo nivel es la clase de
detalle que un agente resuelve distinto cada vez.

Motivo de no usar capas (`Domain/`, `Application/`, `Infrastructure/`): obligan
a clasificar cada archivo en categorías discutibles. ¿Una notificación es
Application o Infrastructure? Esa pregunta no tiene respuesta correcta, se
responde distinto cada vez, y un agente la responde distinto **cada vez que la
encuentra**. «Una notificación va en `Notifications/`» no tiene alternativa.

`Tests/` va en `autoload-dev` con `exclude-from-classmap` para que no entre al
classmap de producción, y aun así viaja con el módulo cuando se extrae.

## R7 — Nada entra a `Platform` hasta tener tres consumidores.

> Enforcement: script propio · `php artisan arch:check` · Severidad: warning
> Escape: puntual

**Qué significa.** Antes de mover algo a la carpeta compartida, espera a que
tres módulos lo necesiten de verdad. Copiar código dos veces está bien;
adivinar qué será reutilizable casi siempre sale mal.

Es la *rule of three* de `docs/patterns/README.md`, aplicada a la ubicación.
El código que emite un generador (`create-crud`) **cuenta como consumidor**:
si el generador lo escribe en cada módulo, tiene consumidores por construcción.

Cicatriz: `Platform` es el nodo base y todos pueden importarlo sin ceremonia,
así que es el camino de menor resistencia — la misma física que convirtió
`General` en un cajón de sastre. Y ya había empezado: `DownloadsDocuments`,
`HandlesModelDocuments` y `HandlesMultipleDocuments` sumaban 269 líneas con
**cero usos**, tan invisibles que PHPStan las reportaba como
`used zero times and is not analysed` — nadie sabía siquiera si compilaban.

Se borraron. Su código sigue en `docs/patterns/`, que es donde vive un patrón
que se copia cuando aparece la necesidad.

**Sí:** el módulo lo escribe para sí. Al tercer módulo que lo necesita, sube.
**No:** «esto seguro sirve en otros lados» → `Platform`.

---

# §2 · Fronteras entre módulos

## R8 — La API pública de un módulo es `Contracts/` y `Events/`, y nada más.

> Enforcement: PHPat · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: permanente (lista blanca en `phpstan.neon`)

**Qué significa.** Un módulo solo deja que los demás usen dos carpetas suyas:
`Contracts/` (lo que promete hacer) y `Events/` (lo que anuncia que pasó). El
resto es privado, como los cables por dentro de la pared.

Única excepción permanente: **`Access\Models\User`**, importable desde
cualquier módulo para relaciones Eloquent, type hints de Policy y `auth()`.
No es un privilegio: `config/auth.php` lo apunta, las Policies lo reciben
tipado y `Notifiable` lo devuelve. El framework lo exige.

Motivo: un modelo Eloquent no es una clase, es un **grafo navegable**. Publicar
uno publica su clausura entera:

```php
$invoice->item->supplier->contactPerson->user->profile->photo;
//        └ Inventory ┘ └── Suppliers ──┘ └──── Access ────┘
```

Un solo import permitido da acceso transitivo a todo lo que ese modelo toca, y
las relaciones son públicas por construcción. Así es como se deshacen los
monolitos modulares: no por una decisión mala, por una permitida que se propaga.

Se descartó que cada módulo declarara su propia superficie (`PublicApi.php` o
un namespace `Public/`). Con un agente eso tiene un modo de fallo concreto:
cuando el análisis rechaza un import, la salida más barata es **añadir la clase
a la lista**, y a diferencia de `--no-verify`, eso queda enterrado en el diff.
Ampliar la frontera debe requerir tocar `phpstan.neon`.

**Sí:** `use App\Modules\Inventory\Contracts\Catalog;`
**No:** `use App\Modules\Inventory\Models\Item;`

## R9 — El grafo de dependencias es Negocio → Access(público) → Platform → ∅.

> Enforcement: PHPat · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: permanente

**Qué significa.** Quién puede usar a quién. Tus módulos pueden usar Access y
Platform; Platform no puede usar a nadie. Es una escalera: se mira hacia
abajo, nunca hacia arriba.

```
Negocio    →  Negocio (solo R8, sin ciclos: R10)
Negocio    →  Access (solo R8)  →  Platform
Negocio    →  Platform
Platform   →  (nada)
app/ glue  →  todos
```

La primera arista es la que hace falta declarar, y es fácil de olvidar porque
el resto del grafo se lee como una escalera. `Billing` puede consumir
`Inventory\Contracts\Catalog` — es el ejemplo canónico de R8, de R10 y de R12.
Lo que no puede es importar nada más de `Inventory`, ni cerrar un ciclo con él.

`Platform` es la base y no depende de nadie. Cicatriz: `NotificationsService`
vivía en `Platform` y hacía `User::permission($p)->get()` — la base
preguntándole a la capa de encima. Esa consulta es negocio de `Access`, así que
se invirtió: `Platform` define `NotificationAudience`, `Access` la implementa y
la registra.

Son ~25 líneas, y son el ejemplo canónico de inversión de dependencia que cada
módulo de negocio va a copiar.

**Segunda cicatriz, y la que enseña por qué PHPat no basta.** Seis de los ocho
componentes de `Settings` —perfil, cuenta, contraseña, 2FA, verificación,
idioma— vivían en `Platform` y leían `auth()->user()->profile`. Es la misma
violación: la base preguntándole a la capa de encima. PHPat no la veía porque
**no había ningún import**: la dependencia viajaba dentro de una relación de
Eloquent, resuelta por nombre en tiempo de ejecución.

La destapó subir PHPStan a level 8. Al exigir que `auth()->user()` no fuera
nulo hubo que tiparlo, y tiparlo obligaba a nombrar `Access\Models\User` desde
`Platform` — que es cuando la dependencia se vuelve visible y PHPat la habría
cazado. Los seis componentes se movieron a `Access`; en `Platform` queda
`SystemForm`, que administra `platform_settings` y es suyo.

Lo mismo con las notificaciones: `Platform` las leía con
`auth()->user()->notifications()`. Ahora consulta su propia tabla por el id del
actor, que además es lo que pide R29.

La lección para el enforcement: **un análisis de tipos solo ve las dependencias
que alguien escribió como tipo.** Las que viajan por una relación, un string de
vista o una clave de configuración son invisibles hasta que algo obliga a
nombrarlas. Subir el nivel del analizador es una de las pocas cosas que lo
obliga.

## R10 — El grafo de `Contracts/` es acíclico; los eventos están exentos.

> Enforcement: script propio · `php artisan arch:check` · Severidad: error
> Escape: permanente

**Qué significa.** Dos módulos no pueden depender uno del otro en círculo,
porque entonces ninguno se puede separar del otro. Los avisos (eventos) sí
pueden ir en ambos sentidos, porque quien avisa no espera respuesta.

Un ciclo importa por una sola razón: si A y B se necesitan mutuamente, no se
puede extraer ninguno. Pero las dos direcciones no son iguales:

| Arista | Al extraer `Billing` |
|---|---|
| `Billing → Inventory\Contracts` | se reimplementa como cliente HTTP; `Billing` no cambia |
| `Inventory ← Billing\Events` | el evento pasa a un broker; nadie cambia |

El evento apunta al revés del flujo de control: el emisor no sabe quién
escucha. El ciclo que mata es `Contracts ↔ Contracts` — dos llamadas síncronas
mutuas, con latencia acumulada y riesgo de recursión.

Efecto secundario deliberado: esto convierte «¿contrato o evento?» en una
decisión **con consecuencia mecánica**. Elegir contrato donde tocaba evento
rompe el build por ciclo.

Quien lo comprueba no es PHPat: sus aserciones son sobre pares de conjuntos de
clases y no incluyen aciclicidad, así que expresarla obligaría a escribir una
regla por cada par ordenado de módulos y a añadir dos más con cada módulo
nuevo. El grafo de imports ya hay que construirlo para la sección «Depende de»
del README (R4), y detectar ciclos sobre él es un recorrido en profundidad de
veinte líneas que además sabe ignorar las aristas de `Events/`.

## R11 — Lecturas por contrato, hechos por evento, órdenes nunca.

> Enforcement: — · — · Severidad: guideline
> Escape: no aplica

**Qué significa.** Tres formas de hablar entre módulos, y solo dos permitidas.
**Preguntar** un dato: contrato. **Avisar** que pasó algo: evento. **Mandar**
que otro haga algo: nunca — avisa y deja que el otro decida.

**Contrato** = necesito un dato ahora y sé de quién. Pregunta síncrona.
**Evento** = pasó un hecho y no sé a quién le importa. Declaración.

La trampa es el tercer caso: cuando A quiere que B *haga* algo.

```php
// mal: Billing conoce el negocio de Inventory
$this->inventory->reserveStock($itemId, $qty);

// bien: Billing solo declara lo que pasó; Inventory decide si le importa
event(new OrderPlaced($orderId, $lines));
```

Con la segunda forma, agregar `Analytics` y `Fraud` no toca `Billing`.

Notificar **no** es una orden: es usar un servicio de plataforma, como
`Log::info()`. Va por contrato (R13).

Es `guideline` y no `error` por una razón concreta: para un analizador
estático, `$this->inventory->reserveStock($id, $qty)` y
`$this->inventory->summariesFor($ids)` son la misma cosa —dos llamadas a
métodos de una interfaz de `Contracts/`, las dos permitidas—. Lo que separa una
orden de una pregunta es lo que significa el verbo, y eso no se decide sin una
lista de verbos prohibidos, que es adivinar. Marcarla `error` daría un build en
verde mientras la regla se viola, que es peor que no comprobarla: deja escrita
la conclusión equivocada.

Lo que sí queda mecánico es la consecuencia, y ya está en R10: elegir contrato
donde tocaba evento acaba en un ciclo, y el ciclo sí rompe el build.

## R12 — Todo contrato de lectura cruzada expone forma en lote.

> Enforcement: script propio · `php artisan arch:check` · Severidad: warning
> Escape: puntual

**Qué significa.** Cuando un módulo pide datos a otro, los pide todos de golpe
y no de uno en uno. Pedir 50 veces lo que cabe en una pregunta es lo que hace
lenta una pantalla sin que nadie sepa por qué.

```php
interface Catalog
{
    /** @param list<string> $itemIds  @return array<string, ItemSummary> */
    public function summariesFor(array $itemIds): array;   // obligatorio

    public function summaryFor(string $itemId): ?ItemSummary;  // opcional
}
```

Si solo existe la forma singular, el N+1 es cuestión de tiempo: un agente
escribiendo un listado usa lo que haya. Y un N+1 **a través de la frontera** es
peor que uno normal — el día que el otro módulo sea un servicio, son 250
llamadas HTTP en vez de 250 consultas.

Es `warning` porque lo comprobable es la forma de la interfaz —que exista el
método en lote— y no el uso: nada impide llamar a la variante singular dentro
de un `foreach`, que es exactamente el N+1 que la regla quiere evitar. Un
`error` sobre la mitad fácil daría por cubierto el problema entero.

## R13 — Un contrato devuelve DTOs, nunca modelos Eloquent ni `array` desnudo.

> Enforcement: PHPat · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: permanente

**Qué significa.** Lo que un módulo devuelve a otro es una caja de datos
simple, nunca el objeto de base de datos. El objeto arrastra consigo todas sus
conexiones; la caja solo lleva lo que hace falta.

Un contrato que devuelve un modelo no es un contrato: es un import disfrazado,
y arrastra la conexión de base, las relaciones y los casts al otro lado de la
frontera. Es la regla que hace que R8 se sostenga.

## R14 — Un evento entre módulos implementa `ShouldDispatchAfterCommit`, y su listener `ShouldQueue`.

> Enforcement: PHPat + PHPUnit · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: puntual

**Qué significa.** Cuando un módulo avisa a otro, el aviso no se entrega al
instante: se deja en una cola y otro proceso lo recoge. Y solo se deja ahí si
la operación terminó bien, para no avisar de algo que al final no ocurrió.

Un evento encolado **ya es** un mensaje en un bus: se serializa, se guarda,
otro proceso lo consume. El día de la extracción se cambia el driver por un
broker y no se toca una línea. Un evento síncrono entre módulos es una llamada
a función con otro nombre.

Y `afterCommit` evita el fantasma:

```php
DB::transaction(function () {
    $invoice = Invoice::create([...]);
    event(new InvoiceIssued($invoice->id));   // se despacha aquí
    $this->ledger->post($invoice);            // esto falla
});                                            // rollback
```

El listener ya mandó el correo sobre una factura **que nunca existió**. Con la
cola es peor: el worker puede tomar el job antes del commit y no encontrar el
registro — el clásico «el job dice que no existe pero yo lo veo en la base».

Cicatrices que esta regla corrige: `BaseNotification` usaba el trait
`Queueable` **sin** implementar `ShouldQueue`, así que parecía encolada y era
síncrona; y `NewNotification` era `ShouldBroadcastNow` **dentro de un bucle**
sobre los destinatarios — con 50 admins, 50 llamadas HTTP a Reverb bloqueando
el request de quien guardaba un formulario.

Se comprueba en dos sitios porque son dos afirmaciones. Que un evento de
`Events/` implemente `ShouldDispatchAfterCommit` es una aserción sobre un tipo,
y la hace PHPat. Que su listener sea `ShouldQueue` no lo es: en Laravel el
vínculo evento–listener se resuelve en runtime, así que hay que preguntárselo a
la aplicación ya arrancada —recorrer los listeners registrados de todo evento
en `Modules\*\Events`— y eso es un test.

**Lo que esta regla no cubre, y hay que saberlo antes de escribir el primer
listener:** una cola es *at-least-once*. El listener puede ejecutarse dos
veces, puede agotar sus reintentos y caer en `failed_jobs`, y con varios
workers puede procesar `InvoiceCancelled` antes que `InvoiceIssued`. Nada de
eso lo arregla `ShouldQueue`. Un listener entre módulos que escribe tiene que
poder correr dos veces sin duplicar el efecto, y el módulo dice en su README
qué pasa cuando falla del todo (R4 ya tiene esa sección). No es una regla
verificable, es la condición de trabajar con colas, y omitirla era prometer que
«se cambia el driver por un broker y no se toca una línea» sin la letra
pequeña.

## R15 — El payload de un evento entre módulos es inmutable y nunca contiene un modelo.

> Enforcement: PHPat · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: puntual

**Qué significa.** Un aviso lleva datos sueltos —o un DTO si son varios—, no
el objeto entero. Si lleva el objeto, quien lo reciba verá cómo está **ahora**,
no cómo estaba cuando pasó la cosa. Un aviso es una foto, no una ventana.

Un evento describe un **hecho pasado**. Si transporta el modelo, el consumidor
lee el estado **presente**:

```php
event(new InvoiceIssued($invoice));   // se emitió la factura
// ...3 segundos después, en el worker:
$invoice->status;                      // → 'cancelled'
```

El listener de «se emitió una factura» está mirando una factura cancelada. El
evento no mintió sobre el dato: llegó tarde a un objeto que siguió cambiando.
Un evento es una foto, no una ventana.

Tu propio código ya lo había descubierto en un caso: `UserDeletedNotification`
recibe `string $userName`, capturado **antes** del `delete()`, porque después
el modelo ya no servía. Esta regla generaliza ese hallazgo.

No aplica a eventos internos de un módulo: no cruzan frontera, no se serializan
si son síncronos, y el acoplamiento es consigo mismo.

**No hay umbral de cuántos datos obligan a un DTO.** Lo hubo —«escalares hasta
dos, DTO desde tres»— y era el mismo test por conteo que R31 descarta con
argumento: mide el presente de algo que se define por su uso. Peor aquí, porque
con R14 estos eventos van serializados en una cola: el día que aparece el
tercer dato, cambiar la firma rompe la deserialización de todos los jobs en
vuelo. Convertía un cambio aditivo en un despliegue incompatible, por una
frontera que nadie podía justificar.

## R16 — Dato que debe congelarse en el instante del hecho se copia; dato que debe reflejar el presente se pide por contrato.

> Enforcement: — · — · Severidad: guideline
> Escape: no aplica

**Qué significa.** Si el dato tiene que quedar congelado (el precio que tenía
la factura al emitirla), se copia. Si tiene que estar al día (el nombre del
usuario), se pregunta. Una factura del año pasado no debe cambiar porque
alguien renombre un producto hoy.

```php
InvoiceLine {
    item_id,           // referencia
    item_name,         // como se llamaba al emitir
    unit_price_cents,  // lo que costaba al emitir
}
```

Esto **no es denormalización por rendimiento**: es corrección. Una factura de
2024 debe mostrar el precio de 2024. Si resolvieras el nombre al mostrar,
tendrías facturas que se reescriben solas cuando alguien renombra un producto.

Un tablero de tareas es el caso opuesto: el nombre del usuario asignado debe
ser el actual, y va por contrato en lote (R12).

La decisión se anota en el README del módulo (R4). No es verificable: es
semántica de negocio, y la máquina no la conoce.

---

# §3 · Interior del módulo

## R17 — Dentro de un módulo, `Models` no importa `Actions`, nadie importa `Livewire`, y `Contracts` no importa `Models`.

> Enforcement: PHPat · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: puntual

**Qué significa.** Dentro de un módulo también hay orden: la pantalla puede
usar todo, el caso de uso puede usar los datos, y los datos no pueden usar
nada de arriba. Evita que la lógica se esconda en sitios donde nadie la busca.

| Desde ↓ / Hacia → | `Models` | `Actions` | `Contracts` | `Events` | `Livewire` |
|---|---|---|---|---|---|
| `Livewire` | sí | sí | sí | sí | sí |
| `Actions` | sí | sí | sí | sí | no |
| `Models` | sí | no | sí | sí | no |
| `Contracts` | no | no | sí | sí | no |

`Models ↛ Actions` porque si el modelo llama al caso de uso, la lógica vuelve a
esconderse dentro del modelo. `* ↛ Livewire` porque la UI es la punta, y es lo
que permite que un módulo no la tenga (R3). `Contracts ↛ Models` es R13.

## R18 — `Actions/` y `Models/` no usan `auth()`, `request()`, `session()` ni `cookie()`.

> Enforcement: phpstan-disallowed-calls · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: aceptada

**Qué significa.** Los casos de uso y los modelos no pueden preguntar «¿quién
está conectado?». Tienen que recibirlo como dato. Si no, dejan de funcionar
cuando corren en segundo plano, donde no hay nadie conectado.

Quien actúa se recibe como parámetro.

```php
// mal: en el worker esto es null, escribe null y sigue, sin error
public function handle(string $userId): void { $actor = auth()->id(); }

// bien: funciona igual desde Livewire, un job, artisan o un test
public function handle(string $userId, string $actorId): void
```

R14 hace que todo lo que cruza módulos pase por un **worker**: otro proceso,
sin sesión, sin request. Un `auth()` ahí no lanza excepción — devuelve `null` y
lo escribe. Es el peor tipo de bug: silencioso, correcto en desarrollo (donde
probaste desde el navegador), roto en producción.

En la capa de UI —Livewire, Blade, rutas— es correcto y no se restringe.

## R19 — El modelo no contiene reglas de negocio.

> Enforcement: — · — · Severidad: guideline
> Escape: no aplica

**Qué significa.** El modelo guarda y lee datos; no decide reglas del negocio.
Si «un usuario protegido no se puede borrar» vive dentro del modelo, no hay
forma de explicar por qué falló, y la regla acaba copiada en dos sitios.

| Puede vivir en el modelo | No puede |
|---|---|
| relaciones, casts, scopes | reglas de negocio |
| accessors de presentación | escrituras en otras tablas |

Cicatriz: la regla «un usuario protegido no se puede borrar» vivía en **dos
lugares a la vez**:

```php
// User.php — el return false no puede decir por qué
public function delete(): ?bool {
    if ($this->is_protected) { return false; }
    return parent::delete();
}

// User/Table.php — la misma regla, otra vez, para poder dar un mensaje
if ($user->is_protected) { $this->toast()->error(...)->send(); return; }
```

Estaba duplicada **porque tuvo que estarlo**: `delete()` devuelve `?bool`
porque es una operación de persistencia, no un caso de uso, y cualquier regla
metida ahí queda limitada a ese canal.

La sustitución es un Action que lanza excepción (R21). La UI la captura y
muestra el motivo.

Los métodos heredados de paquetes (`syncRoles()`, `syncPermissions()`) no
cuentan como violación: la regla aplica a lo que escribes tú.

Nota sobre accessors: `getNameAttribute()` accede a `$this->profile?->…`, así
que `$user->name` es un N+1 si `profile` no viene cargado. Es legítimo, y se
anota en el README de `Access` como precondición.

Es `guideline` porque «regla de negocio» no es una categoría que una máquina
sepa reconocer: la primera columna de la tabla es sintáctica y la segunda es
semántica, y decidir si un `if` dentro de un accessor es presentación o negocio
es justo el juicio que no se puede automatizar. Sus dos mitades verificables ya
rompen el build por su cuenta: que el modelo no importe `Actions/` es R17, y
que una guarda devuelva `false` mudo es R20.

## R20 — Una guarda de invariante lanza excepción; nunca devuelve `false`.

> Enforcement: script propio · `php artisan arch:check` · Severidad: error
> Escape: puntual

**Qué significa.** Cuando una comprobación existe para evitar un desastre
irreversible, puede repetirse como red de seguridad. Pero tiene que **lanzar
un error que explique qué pasó**, no devolver un «no» mudo que nadie sabe
interpretar.

Es la única excepción estructural a R19: para invariantes cuya violación deja
el sistema **inutilizable** —no las que solo rompen una operación— vale una red
de seguridad en un Observer, aunque duplique la comprobación del Action.

`is_protected` es el caso: existe para que no te quedes sin acceso al sistema.
Si un Action futuro olvida comprobarlo, el daño es irreversible.

Pero el defecto real de `User::delete()` nunca fue tener la guarda — fue **cómo
comunicaba el fallo**. Ese `return false` mudo es lo que obligó a duplicar la
regla en la UI, porque la UI necesitaba dar un mensaje y el modelo no podía
decirle cuál. Un Observer que hiciera `return false` en `deleting` tendría
exactamente el mismo defecto, y eso sí es detectable.

```php
// mal: el llamador se entera de que no pasó, no de por qué
public function deleting(User $user): bool { return ! $user->is_protected; }

// bien
public function deleting(User $user): void
{
    if ($user->is_protected) { throw new UserIsProtectedException($user->id); }
}
```

El criterio de **cuándo** poner guarda no es verificable: se limita a
invariantes que dejan el sistema inutilizable, y no se lee como «duplica todas
las invariantes». Lo que sí se verifica es la forma: un método de `Observers/`
cuyo nombre sea un evento de Eloquent y devuelva `bool` es la firma del `return
false` mudo, y eso es un árbol sintáctico, no una arista de dependencia.

## R21 — Un caso de uso es una clase `final` en `Actions/` con un método `handle()`.

> Enforcement: PHPat · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: permanente

**Qué significa.** Cada cosa que la aplicación hace —guardar un usuario,
emitir una factura— es una clase con un solo método. Un archivo, una acción,
un test. Así se encuentra buscando por su nombre.

```php
final class SaveUser
{
    public function __construct(private Notifier $notifier) {}

    /** @throws UserIsProtectedException */
    public function handle(UserData $data, ?User $user = null): User
    {
        return DB::transaction(fn () => /* ... */);
    }
}
```

`handle()` y no `__invoke()` porque es **greppable**: `grep -r "handle("
Modules/Billing/Actions` lista los casos de uso de un módulo que no conoces.
`final` porque un caso de uso se compone, no se extiende. Recibe un DTO para
que se invoque igual desde Livewire, desde consola, desde un listener encolado
y desde un test — que con R18 ya era necesario.

Se descartó un Service por agregado. `NotificationsService` era exactamente eso:
5 métodos estáticos, 3 sin valor (`sendToUsers()` era un alias de la facade),
ninguno testeable en aislamiento. Un Service crece hasta que nadie sabe qué
contiene.

Coste declarado: un CRUD pasa de 2 clases a 6 archivos. Los escribe el
generador.

## R22 — Un Action es el límite transaccional: dos o más escrituras van dentro de `DB::transaction()`.

> Enforcement: PHPUnit · `php artisan test --filter=ActionsAreTransactional` · Severidad: error
> Escape: puntual

**Qué significa.** Si una operación escribe en dos o más tablas, todo se
guarda junto o no se guarda nada. Sin esto puede quedar un usuario a medio
crear, y lo peor es que se ve normal en la lista.

Cicatriz: guardar un usuario estaba repartido entre `UserForm::store()` (tres
escrituras) y `Form::save()` (dos más, la foto y los permisos). Cinco
escrituras en `users`, `profiles`, `model_has_roles`, `media` y
`model_has_permissions`, **sin transacción** — y no era descuido: no existía
ningún objeto que representara la operación completa donde ponerla.

Si `profile()->updateOrCreate()` fallaba, quedaba un usuario sin perfil. Y era
invisible: `getNameAttribute()` hace fallback a `username`, así que el usuario
roto se veía normal en la tabla.

Esa cicatriz también dice cómo hay que verificarla. Las cinco escrituras
estaban repartidas entre **dos archivos**, y `syncRoles()`, `addMedia()` o un
Action que llama a otro esconden las suyas detrás de una llamada: contar
escrituras leyendo el árbol sintáctico de un archivo no ve ninguno de esos
casos, que son todos los casos. Se comprueba ejecutando —un `DB::listen` que
afirma `DB::transactionLevel() > 0` en cada escritura del Action— y así no hay
falsos positivos ni falsos negativos, sino la propiedad exacta.

## R23 — Un componente Livewire lee libremente dentro de su módulo, escribe solo por Action, y no muta modelos para presentación.

> Enforcement: script propio · `php artisan arch:check` · Severidad: error
> Escape: puntual

**Qué significa.** La pantalla puede consultar datos de su propio módulo con
libertad, pero para guardar siempre llama a un caso de uso. Y no le inventa
campos al modelo para pintarlos: eso se hace en la vista.

`render()` no tiene efectos secundarios.

```php
// mal: atributos que no existen en la tabla
$users->getCollection()->transform(function (User $user) {
    $user->role = $user->roles->first()?->display_name ?? '—';
    $user->status = $user->trashed() ? ... : ...;
    return $user;
});

// bien: scopes para la query, accessors o la vista para lo derivado
User::manageableBy($actor)->matching($this->search)->with(['roles', 'profile.media'])
```

Motivo del `transform`: PHPStan ya lo marcaba, y revienta el día que se active
`Model::preventSilentlyDiscardingAttributes()`.

Motivo de la asimetría lectura/escritura: **no tienen el mismo riesgo.** Una
escritura mal ubicada corrompe datos y se propaga; una lectura mal ubicada,
dentro del propio módulo, solo es fea. La ceremonia va donde está el daño — por
eso no se exigen query objects.

El ejemplo usa `manageableBy($actor)` para acotar el listado, y ahí hay una
tensión que conviene nombrar: ese scope es una decisión de autorización escrita
como filtro, y R39 dice que esas se toman en la Policy. Se acepta porque
`Gate::allows` por fila es un N+1, pero el criterio del scope se deriva del de
la Policy y no se escribe dos veces. Cuando divergen, el listado enseña filas
que el detalle luego niega.

## R24 — Un DTO es una `final readonly class` de PHP.

> Enforcement: PHPat · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: permanente

**Qué significa.** Las cajas de datos son clases sencillas que no se pueden
modificar una vez creadas. Seis líneas, sin librerías externas.

```php
final readonly class InvoiceData
{
    public function __construct(
        public string $customerId,
        public int $totalCents,
        /** @var list<InvoiceLineData> */
        public array $lines,
    ) {}
}
```

Seis líneas, cero dependencias, inmutable por construcción, y PHPStan lo
entiende completo. Se descartó `spatie/laravel-data`: la mitad de lo que aporta
—casting multiformato, resources de API, tipos TypeScript— resuelve problemas
que este proyecto no tiene, y trae cinco dependencias que heredarían todos los
productos instanciados.

Es reversible: migrar a Spatie Data es añadir `extends Data` y borrar el
constructor. No se sobrepiensa.

---

# §4 · Datos

## R25 — Toda tabla de un módulo lleva el prefijo de su módulo.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: permanente (lista de tablas exentas)

**Qué significa.** Cada tabla lleva delante el nombre de su módulo:
`billing_invoices`, `access_profiles`. Así se sabe de quién es cada tabla solo
con mirarla, y el resto de reglas de datos se pueden comprobar solas.

`access_profiles`, `platform_settings`, `billing_invoices`.

**Exentas**, por lista explícita: las de paquetes e infraestructura —`users`,
`roles`, `permissions`, `model_has_*`, `role_has_permissions`, `media`,
`audits`, `passkeys`, `personal_access_tokens`, `notifications`, `sessions`,
`cache*`, `jobs*`, `failed_jobs`, `password_reset_tokens`.

Esa lista vive **una sola vez**, en `config/arch.php`, y R30 lee la misma. Dos
copias de la misma lista es lo que hace que una se quede corta: la de R30 no
incluía `media`, `passkeys` ni `failed_jobs`, y el check habría marcado tres
tablas de paquete el día que se escribiera.

Esto no es orden visual: es lo que hace verificable **todo el resto de esta
sección**. Sin prefijo, «ninguna FK cruza módulos» y «ningún módulo lee tablas
ajenas» son frases que nadie puede comprobar. Con prefijo son un `grep` sobre
las migraciones.

Coste medido al adoptarla: dos renombres (`profiles`, `settings`).

## R26 — Las migraciones de un módulo viven dentro del módulo.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: puntual

**Qué significa.** Las migraciones (los archivos que crean y cambian tablas)
viven dentro del módulo al que pertenecen. Si un día el módulo se separa, se
lleva su base de datos con él.

`Modules/{X}/Database/Migrations/`, cargadas con `loadMigrationsFrom()` desde
su ServiceProvider. El orden de ejecución sigue siendo global por timestamp:
`Migrator::getMigrationFiles()` junta todos los paths y ordena por nombre, sin
importar la carpeta.

Trampa que la regla vigila: `php artisan make:migration` escribe en
`database/migrations/` por defecto. Sin `--path` la migración queda en el lugar
equivocado, funciona igual, y nadie lo nota hasta que se intenta extraer. El
check verifica que ninguna migración central toque una tabla con prefijo de
módulo.

Segunda trampa, peor porque es silenciosa. `Migrator::getMigrationFiles()`
indexa por **nombre de archivo sin ruta** antes de ordenar:

```php
->keyBy(fn ($file) => $this->getMigrationName($file))
->sortBy(fn ($file, $key) => $key)
```

Dos módulos con `2026_09_01_000000_create_settings_table.php` colapsan en una
sola entrada del mapa. La segunda gana, la primera **nunca se ejecuta**, y no
hay error, ni warning, ni traza. Se descubre cuando algo consulta una tabla que
no existe, lejos de aquí.

El prefijo de R25 lo hace imposible por construcción, así que el check es el
mismo: el nombre de una migración de módulo empieza por el prefijo de su tabla
—`create_billing_settings_table`— y dos módulos ya no pueden coincidir.

## R27 — Una FK puede apuntar a plataforma pero no a otro módulo de negocio, y nunca lleva `cascadeOnDelete` cruzando frontera.

> Enforcement: script propio · `php artisan arch:check` (tras `migrate`) · Severidad: error
> Escape: puntual

**Qué significa.** Una tabla tuya puede apuntar a las tablas de usuarios, pero
no a las tablas de otro módulo de negocio. Y nunca con borrado en cascada: que
borrar un usuario borre sus facturas es lo contrario de lo que quiere
cualquier contador.

Una FK es la dependencia más difícil de deshacer que existe: un import se borra
en un commit; una FK entre tablas en bases distintas no se «arregla», es
imposible.

Pero el argumento más inmediato es otro: **la FK cruzada impone una semántica
de ciclo de vida que el negocio casi nunca quiere.**

```php
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

Eso dice: *si se borra el usuario, se borran sus facturas.* Lo contrario de lo
que dirá cualquier contador. Y sin `cascade` dice: *no puedes borrar al usuario
mientras tenga facturas* — que es imponer una regla de `Billing` sobre `Access`
desde el esquema, sin que nadie la haya escrito en código.

Cuando el ciclo de vida sí deba propagarse, se hace con un listener del evento
de borrado, en el módulo que decide qué significa ese borrado para él.

Al esquema se le pregunta con SQL. Leer las migraciones obligaría a
reimplementar la inferencia de Laravel —`constrained()` deduce la tabla por
convención, `foreign()->references()->on()` es otra forma, y un `Schema::table`
posterior puede quitar la FK que otra puso—; ocho de las veinte migraciones
actuales son `ALTER` sobre tablas de este mismo repositorio, así que el caso no
es teórico. El check corre después de `migrate` y lee `pg_constraint`, donde
`confdeltype = 'c'` es exactamente `cascadeOnDelete`. Dos consultas, sin falsos
positivos. Lo mismo vale para el tipo de clave de R30, que está en el esquema y
no repartido entre el texto de una migración y el trait del modelo.

## R28 — Sin FK cruzada, la existencia se valida por contrato al escribir y se mantiene por evento al borrar.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: aceptada

**Qué significa.** Como no hay conexión forzada entre tablas de módulos
distintos, al guardar se comprueba que el dato exista preguntando al módulo
dueño, y cuando algo se borra, el dueño avisa.

```php
'item_id' => ['required', 'uuid', 'exists:inventory_items,id'],   // mal
```

`exists:` hace un `SELECT` directo contra la tabla de otro módulo. Es un acceso
cruzado escrito en una cadena de texto — **invisible para PHPat**, que analiza
clases. Esta es una de las pocas reglas cuya verificación es textual y no
estructural, y aplica igual a `unique:`, del que nadie se acuerda.

La forma correcta es una `Rule` propia que consulte el contrato en lote (R12),
para que la validación siga siendo declarativa y sobreviva a la extracción.

## R29 — Ningún módulo consulta tablas, modelos ni reglas de validación de otro.

> Enforcement: phpstan-disallowed-calls + script propio · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: puntual

**Qué significa.** Ningún módulo consulta las tablas de otro por su cuenta. Ni
con SQL, ni con el modelo ajeno, ni con una regla de validación. Leer parece
inofensivo y es justo lo que crea dependencias que el otro módulo no sabe que
tiene.

Ni `DB::table('inventory_items')`, ni `join`, ni el modelo ajeno.

Se descartó permitir «lectura sí, escritura no», que suele elegirse por parecer
moderada: **el acoplamiento de lectura es el que no se ve.** Nadie escribe en
la tabla de otro módulo por accidente; leerla pasa todos los días, y crea una
dependencia que el módulo dueño ni sabe que tiene. Es el peor tipo de contrato:
uno que solo una de las partes conoce.

Se verifica en tres piezas porque son tres cosas distintas. **El modelo ajeno**
ya lo rechaza R8 con PHPat, sin escribir nada nuevo — y con la misma excepción
permanente de `Access\Models\User`, que un grep textual no conocería y marcaría
en cada módulo de negocio. **`DB::table`, `join`, `Rule::exists` y
`Rule::unique`** van a `phpstan-disallowed-calls`, que es AST y no dispara
dentro de comentarios ni de los ejemplos de un README. **La forma en cadena
—`'exists:inventory_items,id'`—** es la única que sí es textual, y es la que se
queda en el `grep`.

Válvula documentada para cuando aparezca un reporte cruzado real: una **vista
de Postgres publicada y mantenida por el módulo dueño**, definida en una
migración suya. Es un contrato a nivel de datos y ahorra el `join` a mano, pero
**no sobrevive a separar las bases**: una vista es un `join` resuelto dentro de
un motor, y el día que las tablas viven en dos servidores deja de compilar,
igual que la FK que R27 llama imposible de arreglar. No se construye antes de
tener el primer consumidor.

Lo que sí sobrevive, y es la salida para ordenar o filtrar un listado por un
campo de otro módulo —que `summariesFor()` no resuelve, porque hidrata una
página ya paginada—, es copiar el campo al escribir y refrescarlo con el evento
del dueño. Es R16 aplicada a lectura.

## R30 — Un modelo escrito en este repositorio se identifica por UUID; la lista de tablas con clave entera es explícita y cerrada.

> Enforcement: script propio · `php artisan arch:check` (tras `migrate`) · Severidad: error
> Escape: permanente

**Qué significa.** Los identificadores de lo que tú crees son códigos largos y
aleatorios (UUID) en vez de 1, 2, 3. Así nadie puede adivinar cuántos
registros hay ni recorrerlos cambiando el número en la URL.

```php
$table->uuid('id')->primary();   // migración
use HasUuids;                    // modelo
public ?string $id = null;       // DTO / Form object
```

Las tres cambian juntas o no cambian. `HasUuids` emite UUID v7, ordenado por
tiempo, así que no fragmenta el índice como haría un v4.

Enteras: **la misma lista de exentas de R25**, en `config/arch.php`. Cambiarlas
rompe paquetes a cambio de estética.

**Y hay una precondición que no es opcional.** Las columnas polimórficas de los
paquetes son enteras, no solo sus claves primarias:

```
media.model_id                  ← $table->morphs('model')
audits.auditable_id             ← $table->morphs('auditable')
notifications.notifiable_id     ← $table->morphs('notifiable')
model_has_{roles,permissions}.model_id  ← unsignedBigInteger
```

Un modelo con UUID que use `InteractsWithMedia`, `Auditable` o `HasRoles` no
puede escribir en ellas: el `INSERT` revienta en Postgres. El caso vive hoy en
el repositorio —`Profile` usa media y auditoría—, así que R25 renombrándolo a
`access_profiles` y R30 poniéndole UUID lo rompen entre las dos.

La contrapartida es una migración de plataforma que pase esas cuatro columnas a
`uuidMorphs`, y va **antes** que el primer modelo UUID con media. Si no se
hace, el modelo se queda en entero y se anota en el README de su módulo: es una
de las pocas excepciones que no se descubren hasta producción.

La evidencia no es de gusto: en dayacount, **55 de 65 tablas usan UUID**, y las
10 enteras son exactamente las heredadas de este boilerplate. Un default que se
sobrescribe en su primer uso no es un default, es un peaje.

Riesgo aceptado y anotado: los ids de `users` viajan en la URL
(`/users/1/edit`) y son enumerables. Está mitigado por permisos. Si algún día
importa, la solución barata es una columna `uuid` pública con
`getRouteKeyName()`, sin tocar ningún paquete.

## R31 — Si la relación tiene nombre propio en el lenguaje del negocio, es una entidad, aunque hoy solo tenga dos claves foráneas.

> Enforcement: script propio · `php artisan arch:check` · Severidad: warning
> Escape: aceptada

**Qué significa.** Si al describir una tabla que une dos cosas usas un
sustantivo —«inscripción», «reserva»— entonces no es una simple unión: es una
cosa con nombre propio y merece su propio modelo. Equivocarse hacia este lado
es barato; hacia el otro cuesta migrar datos en producción.

Inscripción, Asignación, Reserva, Membresía. Si al describirle la tabla a
alguien de negocio dices un sustantivo en vez de «la relación entre X e Y», ya
te respondió.

Se descartó el test de columnas —«si tiene algo además de las dos FK, es
entidad»— porque **mide el presente de algo que se define por su futuro**:

```
Mes 0:  curso_alumno(curso_id, alumno_id)          ← el test dice «pivote»
Mes 3:  + fecha_baja
Mes 5:  + calificacion_final, estado               ← esto es una Inscripción
```

En el mes 0 todos la llamaban «inscripción». El concepto existía completo antes
que sus columnas.

El argumento que cierra la discusión es la **asimetría del coste del error**:

| Error | Cuesta |
|---|---|
| Pivote pura tratada como entidad | sobra un modelo de 10 líneas |
| Entidad tratada como pivote | migrar `attach()`/`sync()` **con datos en producción** |

No hace falta acertar siempre. Hace falta que el error que se comete sea el
barato. El test del nombre falla hacia el lado barato.

`withTimestamps()` no convierte por sí solo, pero es señal: si importa
*cuándo*, ya se está tratando como un hecho, y los hechos tienen identidad.

## R32 — Una tabla pivote no cruza frontera de módulo.

> Enforcement: script propio · `php artisan arch:check` (tras `migrate`) · Severidad: error
> Escape: puntual

**Qué significa.** Una tabla que une dos módulos distintos no existe. Se
modela como algo con nombre propio dentro del módulo donde ocurre el hecho.

La relación se modela como entidad en el módulo **donde ocurre el hecho**, con
el id del otro sin FK (R27).

```
mal:  billing_invoice_x_inventory_item
bien: billing_invoice_lines (invoice_id → billing_invoices, item_id sin FK)
```

Señal de que la pivote cruzada estaba mal desde el principio: no se podía
nombrar. `invoice_x_item` no significa nada. Que un nombre no exista suele
indicar que la tabla tampoco debería.

## R33 — Una pivote se llama `{modulo}_{a}_{b}`; una entidad se llama `{modulo}_{sustantivo_plural}`.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: warning
> Escape: puntual

**Qué significa.** Cómo se llaman las tablas de unión y las que tienen nombre
propio. Si algo tiene nombre de negocio, ese nombre va en la tabla.

`access_role_user` · `billing_invoice_lines` · `academy_enrollments`.

Nunca `a_b` para una entidad: si tiene nombre, el nombre va en la tabla.

Coste conocido: el prefijo rompe la inferencia de Laravel, que espera
`role_user`. Hay que declararlo — `belongsToMany(Role::class, 'access_role_user')`.
Un argumento extra por relación, a cambio de que toda tabla del esquema tenga
dueño identificable.

---

# §5 · Migraciones y versiones

## R34 — Una migración se edita mientras el PR no esté mergeado; después, nunca.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: no aplica

**Qué significa.** Mientras tu cambio no esté aprobado y unido al proyecto,
puedes editar la migración cuantas veces quieras. Después, nunca: se hace una
nueva. Editar antes evita el archivo extra que casi siempre acaba apareciendo
horas después.

La segunda mitad ya se cumplía sin estar escrita: el historial de las 20
migraciones confirma que ninguna se modificó tras mergearse.

La primera mitad es la que faltaba, y previene el problema en origen. Cicatriz:
`create_notifications_table` (03-06 23:53) y `add_deleted_at_to_notifications`
(03-07 02:22) — **dos horas y media de diferencia**. La tabla y su columna se
pensaron en la misma sesión y quedaron en dos archivos porque el primero ya
estaba escrito. Ocho de veinte migraciones son `ALTER` sobre tablas creadas en
este mismo repositorio.

Verificación: ningún PR contiene `create_X` y `alter_X` de la misma tabla.

Una excepción, y es la única: **añadir un `down()` que faltaba**. R34 existe
porque editar una migración ya aplicada deja las bases desincronizadas, y un
`down()` ausente nunca se ejecutó en ninguna parte, así que no hay nada que
desincronizar. Se hizo una vez, en `create_media_table`, para que R37 pudiera
existir.

## R35 — El boilerplate consolida sus migraciones al cerrar versión; un producto instanciado congela todo lo anterior al último tag desplegado.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: warning
> Escape: puntual

**Qué significa.** Cada cierto tiempo las migraciones acumuladas se juntan en
una sola por tabla. En el boilerplate se puede hacer libremente; en un
producto ya instalado solo se toca lo que aún no se ha desplegado.

Dos regímenes porque las situaciones son distintas: **el boilerplate se
instancia con base vacía**, así que nadie tiene datos producidos por estas
migraciones y consolidar no tiene riesgo. Un producto en producción sí.

La línea es concreta y comprobable con `git tag` + `git diff`: ninguna
migración anterior al último tag desplegado se modifica.

La consolidación es manual por módulo. `schema:dump --prune` no sirve, pero no
por lo que parece: solo borra `database/migrations`, y las de los módulos
sobreviven. El motivo real es el volcado, que es del esquema completo de la
conexión — mezcla las tablas de todos los módulos en un archivo sin dueño, que
es exactamente lo que R25 y R26 existen para evitar.

## R36 — Cada versión se cierra con un tag semver `vX.Y.Z` alcanzable desde `trunk`.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: puntual

**Qué significa.** Cada versión se marca con una etiqueta en el repositorio
(`v3.0.0`). Sin esa marca no hay forma de saber qué está desplegado y qué no,
que es justamente lo que la regla anterior necesita saber.

El check es `git describe --tags --match 'v*'`: resuelve o no resuelve. Sin
umbral inventado de commits máximos.

Precondición del CI, y es la clase de detalle que convierte un check en una
mentira: `actions/checkout` clona sin tags por defecto, así que `git describe`
falla en el runner **aunque el tag exista y sea ancestro de HEAD**. Sin
`fetch-depth: 0` en los dos jobs, esta regla no verifica el repositorio:
verifica la configuración del workflow, y lo hace diciendo otra cosa. R56.

Cicatriz: había seis tags y `git describe --tags --match 'v*'` no resolvía.
Cinco apuntaban a la historia pre-squash, abandonada. El sexto, `ia-v1.0.0`,
**sí era ancestro de `trunk`** —a 91 commits— pero su prefijo lo dejaba fuera
del filtro, así que el efecto práctico era el mismo que no tener ninguno.

```
$ git describe --tags --match 'v*'
fatal: No tags can describe '6dbb25e'.
```

R35 se apoya en «el último tag desplegado» y esa referencia estuvo rota cinco
meses — y R35 dice que el boilerplate consolida *al cerrar versión*. Si nunca
cierra, nunca consolida.

Resuelto en `v3.0.0`, el primer tag que cumple esta regla. Los cinco huérfanos
se renombraron a `archive/*` en lugar de borrarse: no estaban contenidos en
ninguna rama, así que eran lo único que mantenía viva esa historia y borrarlos
la habría perdido en el siguiente `gc`. El prefijo los saca del filtro `v*` sin
destruir nada, y `git tag` deja claro cuáles son versiones y cuáles archivo.

Esta regla también fija el momento en que **la numeración de este documento se
congela**.

## R37 — `down()` es obligatorio y está probado.

> Enforcement: PHPUnit · `php artisan test --filter=MigrationsAreReversible` · Severidad: error
> Escape: puntual

**Qué significa.** Toda migración sabe deshacerse, y hay un test que lo
comprueba de verdad. Una vuelta atrás que nunca se probó falla justo el día
que hace falta, con prisa y de madrugada.

```php
public function test_all_migrations_are_reversible(): void
{
    $this->artisan('migrate');
    $this->artisan('migrate:rollback', ['--step' => 100]);
    $this->artisan('migrate');
}
```

Diez líneas que cubren todas las migraciones presentes y futuras.

Diecinueve de las 20 actuales tienen `down()`; `create_media_table` no lo
tiene, y `Migrator` lo omite con un `method_exists` sin decir nada. Por eso el
check son dos afirmaciones y no una: un `grep` de `function down` en cada
migración, y el test de ida y vuelta. Si solo estuviera el test, la migración
sin `down()` fallaría por rebote —al recrear la tabla— y el nombre del fallo
apuntaría al sitio equivocado.

Ninguna se ha probado nunca. Eso es una
promesa sin respaldo: cuando haga falta —normalmente en una situación mala, con
prisa— se descubre que no funciona justo entonces. Un `down()` roto es peor que
ausente, porque la ausencia al menos es honesta.

Con R34, ahora se itera sobre una migración varias veces antes de mergearla, y
`migrate:rollback` es exactamente la herramienta. Vale la pena que funcione.

## R38 — Las migraciones cambian esquema; las transformaciones de datos van a un comando de Artisan.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: puntual

**Qué significa.** Las migraciones cambian la forma de las tablas; no mueven
ni transforman datos. Ese código solo se ejecutaría una vez, en producción,
sin haberse probado nunca. Para eso se hace un comando aparte.

Un `UPDATE` en una migración no es idempotente, bloquea el despliegue si son
200.000 filas, y en `migrate:fresh` no hace nada — o sea que **nunca se prueba
en desarrollo y solo se ejecuta una vez, en producción, sin vuelta atrás**.

La heurística que distingue el caso legítimo:

| Patrón | Qué es |
|---|---|
| la migración **crea la columna** y luego la rellena | backfill, permitido |
| solo hay `update` / `DB::statement`, sin tocar esquema | transformación, prohibida |

El caso ambiguo —rellenar una columna creada en otra migración del mismo PR— es
raro y se anota.

---

# §6 · Autorización y configuración

## R39 — La Policy es el único punto donde se decide una autorización.

> Enforcement: phpstan-disallowed-calls · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: puntual

**Qué significa.** Quién puede hacer qué se decide en un solo sitio: la
Policy. La pantalla la consulta para esconder botones y el caso de uso la
consulta para bloquear de verdad, pero la respuesta sale del mismo lugar.

Tres capas comprueban, **una sola decide**:

| Capa | Para qué | Cómo |
|---|---|---|
| Ruta | primera línea barata | `->can('update', 'user')` |
| Livewire | decidir qué **mostrar** | `@can`, `$this->authorize()` |
| Action | **garantizar** que no pasa | `Gate::forUser($actor)->authorize(...)` |

Eso no es duplicación: la UI oculta el botón, el Action bloquea la operación
aunque el botón se haya saltado. El error sería que cada capa tuviera su propia
lógica — que es lo que había.

**`Gate::before` era la excepción, y está retirada.**
`AppServiceProvider` registraba `Gate::before(fn ($user, $ability) =>
$user->hasRole(ADMIN) ? true : null)`. Un `before` que devuelve `true` corta
antes que toda Policy y **no se puede sobrescribir desde ella**, así que
mientras existió, «la Policy es el único punto donde se decide» fue falso para
el rol admin — justamente el actor capaz del daño irreversible que R20 quiere
frenar.

Se temía que quitarlo obligara a escribir el permiso de admin en cada Policy.
No era así: el atajo no daba acceso a nada, porque el seeder ya asigna al rol
admin todos los permisos declarados. Solo escondía el día que una Policy dejara
de ejecutarse. Lo que sostiene ahora al admin es esa asignación, y
`AdminTienePermisosTest` la mantiene cierta: un módulo que cree permisos y no se
los dé pone el test en rojo, que es el mismo fallo que antes solo veía un
usuario no-admin.

La guarda de invariante de R20 sigue en el Observer y no en la Policy. Esa
decisión no depende ya de `before`: un Observer cubre también las escrituras que
no pasan por una pantalla —un seeder, un job, una consola—, donde no hay Gate
que consultar.

Para índices, el ejemplo de la tabla es `->can('viewAny', Invoice::class)`: el
middleware resuelve el parámetro de ruta por nombre y en un listado no hay
ninguno, así que la forma con instancia falla siempre para quien no sea admin.

Cicatriz, ya cerrada: `app/Policies/` estaba **vacío**, y dos reglas de
autorización vivían dentro de un closure de ruta:

```php
abort_if($user->id === auth()->id(), 403);   // no puedes editarte a ti mismo
abort_if($user->is_protected, 404);          // 404 deliberado, se documenta
```

No eran testeables en aislamiento, no eran reutilizables desde el componente, y
la primera estaba **también** en `Table::render()` como
`where('id', '!=', auth()->id())`.

Hoy viven en `Access\Policies\UserPolicy::update()`, y la ruta encadena
`['permission:editar usuarios', 'can:update,user']`: el permiso es la puerta
gruesa, la Policy la decisión sobre ese usuario concreto. Los tres casos
—editar a otro, editarse, y el protegido— están en
`EditarUsuarioPolicyTest`, y corren para el admin porque ya no hay `before` que
los salte.

Los permisos de Spatie no desaparecen: pasan a ser el **dato** que la Policy
consulta, no el mecanismo de decisión. Toda entidad autorizable tiene Policy,
incluso cuando solo consulte un permiso — se descartó la variante «Policy solo
para lo complejo» porque crea dos mecanismos y una pregunta previa que un
agente responde distinto cada vez.

Cuando el 404 sea deliberado, se expresa con `Response::denyAsNotFound()`, para
que la intención quede escrita.

## R40 — Un permiso se llama `{modulo}.{recurso}.{accion}`, en inglés.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: permanente

**Qué significa.** Los permisos se llaman `modulo.recurso.accion` en inglés:
`billing.invoices.create`. Sin el prefijo del módulo, dos módulos que usen la
misma palabra comparten permiso sin darse cuenta.

`access.users.view` · `billing.invoices.create`. Las etiquetas visibles siguen
en `lang/`, como ya estaban.

Motivo técnico, no estético: **los permisos de Spatie son globales y únicos.**

```
Inventory  →  'ver items'
Billing    →  'ver items'      ← la misma fila de la tabla permissions
```

No hay error. El segundo módulo reutiliza el permiso del primero, y quien pueda
ver artículos de inventario puede ver líneas de factura. Es un fallo de
autorización silencioso, y aparece justo cuando dos módulos usan una palabra
común — `items`, `documentos`, `registros`, `movimientos`.

El prefijo lo elimina por construcción.

## R41 — Permisos, notificaciones, breadcrumbs y seeders viven en el módulo; el menú es central.

> Enforcement: script propio · `php artisan arch:check` · Severidad: error
> Escape: permanente

**Qué significa.** Cada módulo declara sus propios permisos, notificaciones y
datos iniciales dentro de su carpeta. Solo el menú lateral sigue siendo un
archivo común, porque el orden de los enlaces es una decisión de toda la
aplicación.

La asimetría es deliberada. **Los permisos pertenecen al módulo**:
`billing.invoices.create` no significa nada sin `Billing`, y si el módulo se va,
el permiso se va. **El menú no pertenece a ninguno**: el orden de los ítems del
sidebar y qué va agrupado con qué es una decisión de navegación global. Si cada
módulo declarara su ítem, el orden sería emergente y habría que inventar un
campo `order` numérico, que es peor que un archivo central legible.

Cicatriz: `create-crud` tenía 13 pasos y **cuatro editaban archivos fuera del
módulo**. Los dos peligrosos fallaban en silencio: sin entrada en
`config/menu.php` el módulo existe pero es invisible; sin permisos en
`config/roles.php` nadie puede entrar, ni el admin. El agente genera 20 archivos
correctos, se salta el paso 12, y reporta «listo».

Con esta regla queda **un** paso remoto, protegido por un check. Un paso remoto
verificado es manejable; cuatro sin verificar es lo que había.

`config('roles.permissions')` se sigue leyendo igual: cambia dónde se declara,
no dónde se lee.

## R57 — Toda propiedad pública que identifica un registro lleva `#[Locked]`.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: puntual

**Qué significa.** Una propiedad pública de un componente Livewire que dice
*sobre qué registro* se está operando —`$record`, `$id`, `$invoiceId`— lleva
`#[Locked]`. Sin el atributo, quien elige el registro es el navegador.

Livewire no guarda el estado en el servidor: lo serializa en un snapshot que
viaja al cliente y **vuelve rehidratado en cada petición**. El snapshot va
firmado, así que nadie puede inventarse uno; pero las propiedades públicas son
justamente la parte que el componente *espera* que cambie, y Livewire las
escribe con lo que llegue. `#[Locked]` es lo que declara que esa concreta no.

La cicatriz, de agosto de 2026. `Users\Form` tenía `public ?User $record` sin
`#[Locked]`. Un usuario con solo `access.users.create` abría el alta —donde
`$record` es `null` legítimamente—, ponía en el payload el id de un usuario
existente y guardaba: el formulario de creación escribía sobre un registro
ajeno, y de paso le sincronizaba los permisos que el atacante eligiera. Antes
del arreglo había **un solo `#[Locked]` en todo el proyecto**.

Esta regla y R58 son mitades del mismo fallo, y ninguna basta sola. `#[Locked]`
sin `authorize()` fija el objeto pero no comprueba quién eres; `authorize()`
sobre una propiedad no fijada decide sobre el objeto que el atacante acaba de
cambiar, que es la forma más cara de dar un permiso.

```php
#[Locked]
public ?User $record = null;        // ✓ lo fija quien montó el componente

public ?int $invoiceId = null;      // ✗ lo elige el navegador en cada petición
```

`#[Locked]` no estorba a `mount()` ni a `fill()`: la restricción es sobre la
hidratación desde el cliente, no sobre lo que el servidor asigne.

**Qué mira el check.** Los nombres que identifican sin ambigüedad —`$record`,
`$model`, `$id` y cualquier `$algoId` o `$algo_id`— en todo archivo bajo
`Livewire/`. Una propiedad que identifica y se llama de otro modo se le escapa;
reconocerla pide leer el tipo con un AST, y ese trabajo vive en R12.

## R58 — Un método público de escritura de un componente Livewire llama a `authorize()`.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: warning
> Escape: puntual

**Qué significa.** `save()`, `store()`, `update()`, `delete()` y `destroy()` de
un componente Livewire empiezan autorizando. No porque la ruta no lo haga, sino
porque **la ruta no interviene**: una acción de Livewire viaja a
`/livewire/update`, no a la URL de la pantalla. El `permission:` y el `can:`
que protegen `/users` no corren ahí. Tampoco `mount()`, que se ejecuta una vez
en el render inicial y nunca más.

Es el mismo error que R39 describe visto desde la otra punta: allí el problema
era decidir la autorización en varios sitios, aquí es no decidirla en ninguno
porque cada capa supone que la anterior ya lo hizo.

De los 26 componentes del proyecto en agosto de 2026, **4 autorizaban**. Este
check, al escribirse, encontró uno más: `Platform\Settings\SystemForm` llamaba
a `authorize()` en `mount()` y no en `save()`.

**Por qué es `warning` y no `error`.** Porque las dos exclusiones que lo hacen
utilizable son heurísticas, y una heurística que rompe el build enseña a
silenciarla:

- Un **Form object** (`extends Form`) no autoriza: no es alcanzable por HTTP
  por sí mismo, y el componente que lo monta ya decidió. `UserForm::store()`
  es correcto tal cual.
- Un método que solo toca **al usuario de la sesión** —usa `currentUser()` o
  `auth()`, y no `$this->record`— no tiene objeto ajeno que autorizar. Los
  cuatro formularios de `Settings/` son eso.

El día que las dos exclusiones se comprueben con un AST en vez de con `grep`,
la regla sube a `error`. Mientras tanto avisa, que es lo que el CI no hacía.

---

# §7 · Colas, jobs y cache

## R42 — Lo que sale de la aplicación va a cola; lo que solo toca la base local es síncrono.

> Enforcement: phpstan-disallowed-calls · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: puntual

**Qué significa.** Todo lo que sale fuera de la aplicación —correos, archivos,
llamadas a otros servicios— se hace en segundo plano. Lo que solo toca la base
de datos se hace al momento.

| A cola | Síncrono |
|---|---|
| mail, broadcast, HTTP externo, S3, PDF, ZIP | escrituras en la BD, cache, cálculos |

Se descartó el criterio por tiempo («lo que tarde más de X»): el tiempo no es
evaluable al escribir el código, y es exactamente lo que produjo el
`ShouldBroadcastNow` dentro de un bucle, que parecía rápido con un destinatario.

«Salir de la aplicación» es una propiedad de los sitios de llamada, no de los
tipos, así que no la ve una regla de dependencias: se declaran las facades de
salida —`Http`, `Mail`, `Storage::disk` remoto— como llamadas prohibidas fuera
de `Jobs/` y `Listeners/`, que es lo que hace `phpstan-disallowed-calls` con su
lista de excepciones por carpeta.

## R43 — Un módulo no encola jobs de otro.

> Enforcement: PHPat · `vendor/bin/phpstan analyse` · Severidad: error
> Escape: puntual

**Qué significa.** Un módulo no le manda trabajo en segundo plano a otro.
Avisa, y el otro decide si crea el suyo.

Encolar el job de otro módulo es una orden (R11). Se emite el evento; el otro
módulo escucha y encola **su** job. La cola es infraestructura compartida; los
jobs no.

Consecuencia útil: un job es siempre del módulo que lo ejecuta, así que puede
usar sus modelos libremente. No hay frontera nueva que aprender.

## R44 — Toda clave de cache lleva el prefijo de su módulo.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: puntual

**Qué significa.** Las claves con las que se guardan datos en memoria llevan
delante el nombre del módulo. Sin eso, dos módulos pueden pisarse sin
enterarse.

`platform:setting:app_name` · `billing:invoice-totals:{uuid}`.

Sin prefijo hay dos problemas: colisión (`billing:report` vs
`inventory:report`) y extracción (al sacar un módulo, nadie sabe qué claves se
lleva). `Setting::get()` usaba `"setting:{$key}"`, una clave global sin dueño.

No se usan cache tags: no funcionan con los drivers `database` ni `file`, y
`CACHE_STORE` es `database`. El prefijo funciona en todos y se verifica con un
`grep`.

---

# §8 · Tests

## R45 — Todo Action, todo contrato y todo componente Livewire tiene su archivo de test.

> Enforcement: script propio · `php artisan arch:check` · Severidad: warning
> Escape: puntual

**Qué significa.** Cada caso de uso, cada contrato y cada pantalla tienen que
tener su archivo de test. No se comprueba que el test sea bueno —eso no lo
puede saber una máquina— pero sí que exista.

Tres `glob` comparados. No comprueban que el test sea bueno —eso no lo puede
saber una máquina— pero sí que **existe**, que es donde estaba el agujero.

Cicatriz: `UserCrudTest` tenía 7 tests y **no probaba ningún CRUD**. Tres
comprobaban que una página devuelve 200, dos probaban la factory (código de
test, no de la aplicación), y dos probaban reglas reales. `UserForm::store()`
—las cinco escrituras sin transacción— no tenía ni un test. De 12 archivos de
test, uno solo usaba `Livewire::test`.

No era descuido: es lo que sale cuando la lógica vive dentro de un componente
Livewire, porque probarla exige montar el ciclo de vida completo. **El test
difícil se sustituye por el test fácil.** R21 lo arregla de raíz: un Action se
instancia, se le pasa un DTO y se afirma el resultado.

Se descartó el coverage por porcentaje: los siete tests de arriba lo suben sin
probar nada, requiere Xdebug o PCOV, y ralentiza la suite.

Los tests viven en `Modules/{X}/Tests/`. Los que cruzan módulos —probar que un
evento de `Billing` llega a `Inventory`— van a `tests/Integration/`, porque no
pertenecen a ninguno de los dos.

Es `warning` mientras el check sea un `glob`, y la razón es la cicatriz de
arriba: `UserCrudTest` existía y no probaba ningún CRUD, así que habría pasado
un check de existencia sin cambiar nada. Un `error` que se satisface creando un
archivo vacío enseña a satisfacer checks. Sube a `error` cuando el check
compruebe la referencia —que el test de `SaveUser` mencione `SaveUser`—, que es
poco más de trabajo y sí distingue un test de un archivo.

---

# §9 · Comentarios, documentación y forma

## R46 — Una línea de comentario no pasa de 90 caracteres.

> Enforcement: script propio · `php artisan arch:check` · Severidad: error
> Escape: puntual

**Qué significa.** Una línea de comentario no pasa de 90 caracteres, para que
se lea sin desplazarse a lo ancho.

Aplica a `//`, `#`, `/* */` y a la **prosa** dentro de un docblock, en `.php` y
en `.blade.php`. Markdown queda exento: partir a 90 rompe tablas y URLs, y se
lee renderizado.

Exentas las **anotaciones de tipo** —`@param`, `@return`, `@var`, `@throws`—
porque un genérico largo no se parte de forma que PHPDoc entienda.

Nota de implementación, no cosmética: el verificador **no es un grep**. Un grep
ingenuo reporta 8 violaciones en `app/`; cinco son atributos
`#[Description(...)]` de `app/Mcp`. Las reales son tres. Un check que nace con
62 % de falsos positivos se desactiva en dos semanas.

Y `#[` es solo la primera de cinco trampas léxicas: `//` dentro de una URL en
un string, `#` dentro de una cadena, la frontera entre prosa y anotación dentro
del mismo docblock, y `{{-- --}}` en Blade, que es otro delimitador. Distinguir
cinco contextos es lo que hace un lexer, así que el check usa `token_get_all()`
y mira `T_COMMENT` y `T_DOC_COMMENT` — que regalan el contexto y eliminan las
cinco de una vez. Por eso vive en `arch:check` y no en el script de bash.

La lista de anotaciones exentas tampoco se enumera. El motivo —un genérico
largo no se parte de forma que PHPDoc entienda— aplica igual a `@extends`,
`@template`, `@method` y `@phpstan-*`, y `@extends` de una factory con el
namespace completo de un módulo pasa de 90 caracteres sin remedio. La regla es
la que ya estaba escrita en el motivo: **exenta toda línea cuyo primer token
tras el asterisco sea un `@tag`**; el límite aplica a la prosa.

## R47 — Un comentario explica por qué, no qué.

> Enforcement: — · — · Severidad: guideline
> Escape: no aplica

**Qué significa.** Un comentario explica **por qué** se hizo algo, no qué hace
el código. El qué ya lo dice el código; el porqué se pierde para siempre si
nadie lo escribe.

El qué lo dice el código. Los mejores comentarios de este repositorio ya lo
hacen, y traen el ticket:

```php
// Entre que se pinta la fila y se confirma el diálogo pasa tiempo real,
// y en ese hueco otro usuario pudo borrarla. Con `find($id)?->delete()`
// el toast de éxito salía igual con la fila intacta delante: el peor de
// los dos fallos posibles, porque el usuario deja de mirar.
```

## R48 — No hay banners ni separadores ASCII.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: puntual

**Qué significa.** Nada de líneas decorativas ni marcos de guiones para
separar secciones. Ocupan espacio y se desalinean en cuanto alguien edita.

Ni `── Sección ──`, ni `/* |----- Módulo ----- */`, ni `====`. Aplica a `app/`,
`Modules/`, `routes/`, `database/`, `tests/` y a los archivos de `config/`
propios (`menu`, `roles`, `notifications`).

Exentos: los `config/` publicados por paquetes, que vienen con banner de
fábrica y no tiene sentido tocar.

Los `/* |---- */` de `routes/` **no** son convención del framework: el stub de
`routes/web.php` de Laravel 12+ es minimalista y no los trae.

## R49 — Una explicación de más de tres líneas va al README del módulo; en el código queda un puntero de una línea.

> Enforcement: — · — · Severidad: guideline
> Escape: no aplica

**Qué significa.** Si una explicación necesita más de tres líneas, va al
README del módulo y en el código queda una línea que apunta ahí.

```php
// Ver «Documentos privados» en el README del módulo.
```

## R50 — Un docblock que enumera un contrato usa formato escalera.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: warning
> Escape: puntual

**Qué significa.** Cuando un comentario enumera cosas —qué debe definir quien
use esta clase— se escribe escalonado: el enunciado a la izquierda, los puntos
sangrados y los ejemplos más sangrados aún.

Enunciado terminado en dos puntos, ítems a **3 espacios** tras el asterisco,
ejemplos a **7**:

```php
/**
 * El componente sobrescribe:
 *   searchable(): array   — columnas donde busca $search
 *       ['username', 'name', 'email']
 *   filterable(): array   — filtro => operador SQL
 *       ['email' => 'like', 'status' => '=']
 */
```

La prosa explicativa **no** usa escalera: usa bloque justificado sin sangría
interna, como el ejemplo de R47. Son dos formatos con dos usos, y confundirlos
hace la prosa menos legible, no más.

El formato ya existía en seis archivos del repositorio sin nombre ni regla.
Esta regla solo lo nombra.

## R51 — Un PHPDoc documenta lo que la firma no puede decir; si repite tipos nativos, se borra.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: warning
> Escape: puntual

**Qué significa.** Un comentario de documentación solo dice lo que la firma
del método no puede decir: qué errores lanza, qué efectos tiene, en qué
unidades está un número. Repetir los tipos que ya están escritos es ruido.

**Obligatorio** cuando aplique alguno: genéricos y array shapes · `@throws` ·
efectos secundarios («escribe en `media`», «encola un job») · invariantes y
precondiciones («requiere `profile` cargado») · unidades y zona horaria
(«centavos», «UTC») · contrato de negocio.

**Prohibido**: `@param string $name` sin más · `@return void` · `@inheritDoc`
vacío.

**Dónde se exige**: `Contracts/` completo, constructores de `Events/`,
`Actions::handle()`, y scopes y accessors no triviales.
**Dónde no**: `render()`, `mount()`, `updated*()`, getters triviales, métodos
privados, Policies de una línea.

`Actions::handle()` entra porque es donde viven los `@throws`, los efectos
secundarios y las unidades — el sitio con más que decir del proyecto.

Es el mismo principio que R4 aplica a los README: **lo que puede derivarse del
código se deriva; lo escrito a mano se limita a lo que el código no dice.**

## R52 — Un archivo no pasa de 350 líneas.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: aceptada

**Qué significa.** Ningún archivo de código pasa de 350 líneas. En un proyecto
anterior la regla existía y había un archivo de 1806, porque nada lo
comprobaba.

Aplica a `.php` y `.blade.php` bajo `app/`, `Modules/`, `routes/`, `database/`
y `tests/`. Quedan fuera los `config/` y las vistas publicados por paquetes, el
markdown y los CSS — el mismo alcance que declaran R46 y R48, que hasta ahora
era el único que estaba escrito.

Cicatriz heredada: en un proyecto anterior la regla decía 350 y había un
archivo de **1806 líneas**, porque nada lo revisaba.

Al adoptarla la deuda es una sola: `Docs\Index.php`, 323 líneas, a 27 del
techo. Fuera del alcance quedan `resources/css/theme/_docs.css` (456) y
`config/debugbar.php` (368), publicado por su paquete.

Ese margen de 27 líneas es el dato que importa, y es reciente: cuando esta
regla se escribió el máximo en PHP era 208. Su valor es preventivo, y el
archivo de 1806 líneas tampoco nació así.

## R53 — Los nombres son completos y pronunciables; los booleanos llevan prefijo verbal.

> Enforcement: script propio · `php artisan arch:check` · Severidad: warning
> Escape: puntual

**Qué significa.** Los nombres se escriben completos: `period`, no `p`. Los
que responden sí o no empiezan por verbo: `isActive`, `hasPermission`.

`period`, no `p`. `isActive`, `hasPermission`, `canEdit`.

Lista blanca de nombres cortos, cerrada: `$i`, `$k`, `$v` como índices de
bucle, y `$q` como parámetro de closure de query —que es lo que usa la
documentación oficial de Laravel, y prohibirlo sería pelear con el idioma del
framework.

Deuda medida al adoptar la regla: 8 usos (`$r`, `$m`).

La regla tiene dos mitades y solo una se comprueba. **Una variable de una letra
fuera de la lista blanca** es un `grep` con cuatro exclusiones, y esa es la que
avisa. **Que un nombre sea completo y pronunciable, y que un booleano lleve
prefijo verbal**, es criterio: distinguir un método que devuelve `bool` y
debería llamarse `isActive` de uno que está bien como está exige saber qué
significa, y ninguna máquina lo sabe. Esa mitad no se verifica, y por eso la
severidad es `warning` y no `error`.

---

# §10 · Verificación y deuda

## R54 — El análisis estático corre en `level 8` o superior.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: permanente

**Qué significa.** El analizador de código corre en un nivel de exigencia
alto, capaz de avisar cuando se usa algo que podría no existir. Es la familia
de errores más común y la que solo aparece en producción.

Level 8 es el escalón que detecta llamadas y accesos sobre tipos **nullable**,
que en un proyecto con `HasOne`, `?->` y SoftDeletes es la familia de bugs más
frecuente.

Cicatriz:

```php
// app/Livewire/App/Personal/User/Form.php:150
if ($this->photo) {
    $user->profile->addMedia($this->photo->getRealPath())    // ← sin ?->
```

`profile()` es un `HasOne` y devuelve `null` si no hay fila. Hay un camino real
hacia eso: las tres escrituras sin transacción de R22. La siguiente foto que
alguien suba revienta con `Call to a member function addMedia() on null`. El
resto del archivo usa `?->` en seis líneas — el código *sabe* que puede ser
null y en esta se olvidó.

Se descartó level 9/10: con los atributos mágicos de Eloquent generan ruido que
se acalla escribiendo PHPDoc sin valor.

Existe como regla y no solo como configuración porque un nivel es fácil de
bajar cuando estorba. Sin regla citable, `level: 8` → `level: 6` pone el build
en verde y no es una violación de nada.

Por eso quien la verifica **no es PHPStan**: pedirle a PHPStan que falle porque
su propio `level` es 6 es circular — a nivel 6 termina en verde, y nada compara
lo declarado contra lo exigido. El check es una línea de `grep` sobre
`phpstan.neon`, y vive en el script de bash porque el archivo que vigila es
justamente el de la herramienta.

Hoy dice `level: 6`, con 19 errores vivos y 74 entradas de baseline. Subir a 8
no es cambiar una línea: es un trabajo con su propio calendario, y entra por la
válvula de deuda de R55 como todo lo demás.

## R55 — Todo baseline lleva fecha de vencimiento, y el build falla cuando pasa.

> Enforcement: script propio · `scripts/arch-lint.sh` · Severidad: error
> Escape: no aplica

**Qué significa.** Cuando se decide convivir con errores conocidos, esa lista
lleva fecha de caducidad y el build falla cuando pasa. Sin fecha, la deuda se
olvida: el CI de este proyecto estuvo en rojo 23 veces seguidas sin que nadie
lo notara.

Aplica al baseline de PHPStan y a cualquier otro mecanismo que congele deuda.

Esta es la regla que responde al origen de todo este documento. El CI llevaba
**23 corridas consecutivas en rojo** y nunca detuvo nada. El problema no fue
tener deuda —la deuda era consciente— sino que **no tenía dueño ni fecha**, así
que duró cinco meses y creció en silencio hasta que una entrada se venció sola:
el baseline esperaba 2 ocurrencias de `Role::$display_name` y había 3.

Un baseline sin fecha no es deuda gestionada: es deuda olvidada con formato de
archivo.

## R56 — Cada verificación falla por una sola razón, y esa razón es la que declara su nombre.

> Enforcement: — · — · Severidad: guideline
> Escape: no aplica

**Qué significa.** Cada comprobación falla por un solo motivo, y ese motivo es
el que dice su nombre. Un test que se llama «la página de login carga» no
puede fallar porque falte compilar el CSS: enseña a desconfiar de todos los
demás.

Criterio con el que se escriben los verificadores de este documento.

Cicatriz: `test_login_page_renders` fallaba en CI porque **nadie había
compilado el CSS** — `public/build` está en `.gitignore` y el workflow no
instalaba Node. El test decía una cosa y significaba otra.

Ese es el mecanismo por el que dejas de creerle a un check, luego a la pantalla
entera, y acabas con 23 builds ignorados: el rojo de PHPStan (19 errores
legítimos) se contaminó por vecindad con un rojo falso.

Con un agente el daño es peor. Lee `expected 200, got 500`, concluye que la
ruta de login está rota, y se pone a «arreglar» código sano. **Un check
mentiroso no solo se ignora: manda al agente a romper cosas que funcionaban.**

Aplicación concreta: `withoutVite()` en `TestCase` (los tests prueban
comportamiento) y un step propio `Build assets` en CI (que los assets compilen
es otra afirmación, y merece su propio nombre).

---

# Fuera de alcance

Dicho en voz alta, para que no se confunda «no normado» con «olvidado».

| Área | Por qué queda fuera |
|---|---|
| **Seguridad** más allá de autorización | merece `docs/SECURITY_RULES.md` propio: se agrupa por tipo de dato, no por estructura de código |
| **Frontend, Blade, Tailwind** | otro dominio; R52 y R6 cubren lo que cruza con arquitectura |
| **API REST** | hay un endpoint. Normar lo inexistente es adivinar |
| **Performance y N+1** | R12 cubre el caso cruzado, que es el caro. El resto lo ven Larastan y Debugbar |
| **Versionado y deploy** | fuera **salvo** R36, que R35 necesita |
| **Observabilidad** | Sentry está instalado sin DSN. Es una conversación aparte |
| **i18n** | `lang/` funciona; R40 resolvió lo que cruzaba |
| **Commits y ramas** | ya viven en la skill `git-commits` |
| **Mutation testing** | responde «¿son buenos mis tests?» cuando la pregunta aún es «¿existen?» |

**Regla puente**: un módulo que maneje datos sensibles lo declara en su README
y sigue `docs/SECURITY_RULES.md` cuando exista.

## Higiene pendiente, fuera de estas reglas

Cinco cosas que este análisis encontró y que no son arquitectura:

- `config/scramble.php` está publicado y `dedoc/scramble` **no está instalado**.
  Un config de un paquete inexistente es contexto falso para un agente.
- **`CLAUDE.md` sigue documentando `{Domain}/{Module}`**, y debe hacerlo:
  es la estructura vigente y un agente necesita saber cómo escribir código en
  el proyecto que existe. Ya apunta aquí y avisa de que estas reglas describen
  el destino. Esa tabla se borra el día que la migración a `app/Modules/` esté
  hecha, no antes.
- Sentry está instalado y publicado **sin `SENTRY_DSN`**. O se configura o se
  desinstala: un paquete de observabilidad a medio instalar es peor que ninguno.
- `rector.php` declara `withPhpSets(php84: true)` con un `require` de `^8.5`.
- **TallStackUI estuvo roto entero** desde `6f1ccd5` (actualización de
  dependencias): las vistas Blade compiladas conservaban los hashes de los
  assets anteriores, así que los cinco scripts daban 404, `tallstackui_card`
  quedaba indefinido y **toda card renderizaba con `display:none`**. Se
  arregló con `optimize:clear` y se añadió `view:clear` al `post-update-cmd`
  de Composer para que no vuelva. Es un caso de R56: el síntoma («no se ve la
  página») no nombraba la causa por ninguna parte.
