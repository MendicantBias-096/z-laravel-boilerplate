# Empezar aquí

Este es el punto de entrada. Si nunca has tocado el proyecto, empieza por
arriba y sigue en orden; si ya lo tienes levantado, salta a
[El mapa](#el-mapa-que-hay-dentro).

## Qué es este proyecto

Es un **boilerplate**: una aplicación web a medio hacer que sirve de punto de
partida para construir productos nuevos. Ya trae resueltas las cosas que todo
sistema necesita y nadie quiere volver a escribir — entrar con usuario y
contraseña, permisos, subida de archivos, notificaciones, envío de correo — para
que al empezar un producto solo escribas lo que ese producto tiene de propio.

No es un producto en sí mismo. Es el suelo sobre el que se construyen.

## Qué necesitas instalado

Tres cosas, y ninguna es PHP:

- **Docker** — el motor que corre todo en contenedores aislados.
- **DDEV** — el que gestiona esos contenedores por ti. Es la única herramienta
  con la que hablas.
- **Git** — para clonar el repositorio.

PHP, PostgreSQL, Node y las demás piezas viven **dentro** de los contenedores.
No hace falta instalarlas en tu máquina, y de hecho es mejor que no.

Por eso todos los comandos empiezan por `ddev`: se ejecutan dentro del
contenedor, no en tu ordenador.

## Levantarlo por primera vez

```bash
git clone <url-del-repositorio> mi-proyecto
cd mi-proyecto
make setup
```

`make setup` es interactivo: pregunta el nombre del proyecto y hace el resto —
crea la configuración, arranca los contenedores, instala dependencias, crea la
base de datos y la llena con datos de prueba.

La primera vez tarda varios minutos porque Docker descarga las imágenes. Las
siguientes son segundos.

Cuando termine te dirá la dirección: `https://mi-proyecto.ddev.site`.

### Si algo sale mal

| Síntoma | Qué mirar |
|---|---|
| No abre la web | ¿Docker está corriendo? `ddev start` |
| Entra pero se ve sin estilos | falta compilar: `ddev bun run build` |
| «These credentials do not match» | falta sembrar: `ddev artisan db:seed` |
| Cambias un archivo y no se refleja | `ddev exec php artisan optimize:clear` |

## Entrar

En **local** la pantalla de login trae dos botones bajo la etiqueta `DEV`:

- **Admin** — entra con todos los permisos
- **Usuario** — entra sin permisos de administración

Son un atajo de desarrollo: el servidor los rechaza fuera de local, así que no
existen en producción aunque alguien los invoque a mano.

Si prefieres escribir las credenciales, los dos usuarios de prueba y sus
contraseñas están en `database/seeders/UsersTableSeeder.php`.

## El mapa: qué hay dentro

```
app/          el código de la aplicación
config/       ajustes: menú lateral, permisos, notificaciones
database/     migraciones (la forma de las tablas) y datos de prueba
docs/         esta documentación
lang/         los textos en español y en inglés
resources/    las pantallas (Blade) y los estilos
routes/       qué dirección web lleva a qué pantalla
tests/        las pruebas automáticas
```

Las tres carpetas que vas a tocar casi siempre son `app/`, `resources/` y
`routes/`.

## Trece palabras que vas a leer todo el rato

Están ordenadas de más general a más específica. No hace falta memorizarlas:
vuelve aquí cuando te tropieces con una.

**Módulo** — un área completa del negocio, con todo lo suyo dentro: facturación,
inventario, usuarios. Es la unidad grande de organización.

**Pantalla** — una vista que el usuario ve, con su dirección web. Un módulo
puede tener varias, o ninguna.

**Migración** — un archivo que crea o cambia una tabla de la base de datos. Se
ejecutan en orden y quedan registradas, así que todos los ordenadores acaban con
la misma estructura.

**Seeder** — un archivo que rellena la base con datos de prueba: los usuarios
admin y usuario salen de ahí.

**Modelo** — la clase que representa una tabla. `User` es la tabla `users`.
Sirve para leer y guardar, no para decidir reglas del negocio.

**Action** — una cosa que la aplicación hace: guardar un usuario, emitir una
factura. Una clase, un método, un archivo. Es donde vive la lógica.

**DTO** — una caja de datos simple que se pasa de un sitio a otro. No sabe
guardarse ni consultarse: solo lleva valores.

**Contrato** — una promesa escrita de lo que un módulo sabe hacer, sin decir
cómo. Otros módulos hablan con esa promesa, no con el código de dentro.

**Evento** — un aviso de que algo pasó («se emitió una factura»). Quien lo
lanza no sabe ni le importa quién lo escucha.

**Cola** — la lista de trabajos pendientes que se hacen en segundo plano, para
que el usuario no espere. Los correos y los avisos entre módulos van ahí.

**Policy** — el sitio donde se decide si alguien puede hacer algo. La pantalla
la consulta para esconder botones; el Action, para bloquear de verdad.

**Livewire** — la herramienta que hace que las pantallas reaccionen sin escribir
JavaScript. Escribes PHP y la página se actualiza sola.

**Migración pendiente vs aplicada** — `ddev artisan migrate:status` te dice
cuáles se han ejecutado ya en tu base y cuáles no.

## Cómo se trabaja aquí

El ciclo de un cambio, de principio a fin:

```bash
git switch -c feat/mi-cambio     # 1. una rama para tu cambio
                                 # 2. escribes el código
ddev exec vendor/bin/pint        # 3. formatea el estilo
ddev exec php artisan test       # 4. corre las pruebas
git commit                       # 5. commit (en español, ver la skill git-commits)
git push                         # 6. y abres un Pull Request
```

Tres cosas que conviene saber desde el primer día:

- **Los tests no son opcionales.** Cada caso de uso y cada pantalla llevan el
  suyo. La regla completa es R45.
- **El estilo no se discute.** Pint lo decide y punto; no hay debates de comas
  en las revisiones.
- **Hay reglas de arquitectura y algunas rompen el build.** No hace falta
  aprendértelas: cuando violes una, la herramienta te lo dirá con el número de
  la regla y podrás buscarla.

### Comandos del día a día

```bash
make start                    # arrancar el entorno
make dev                      # Vite con recarga automática al editar estilos
make migrate                  # aplicar migraciones nuevas
make fresh                    # borrar todo y empezar la base de cero
make shell                    # entrar al contenedor
make stop                     # apagar
```

`make` a secas lista todos los comandos disponibles.

## Los otros documentos

- **Reglas de arquitectura** — las 56 reglas que ordenan el proyecto. Cada una
  tiene un número citable (R25), una explicación en lenguaje llano y el motivo
  por el que existe. No se lee de corrido: se consulta.
- **Patrones** — piezas de código reutilizables que ya han demostrado servir en
  proyectos reales: tablas, documentos adjuntos, validaciones mexicanas.

Los dos están en el menú de la izquierda.

## El estado real hoy

Esto es importante y conviene decirlo antes de que te confunda.

**Las reglas de arquitectura describen hacia dónde va el proyecto, no dónde está
hoy.** Se acordaron en agosto de 2026 y su implementación está pendiente. Si
lees R2 («un módulo es una carpeta bajo `app/Modules/`») y luego miras el
código, no vas a encontrar esa carpeta: hoy la estructura es la anterior, con
todo bajo `app/Livewire/App/`.

Los verificadores, en cambio, ya empezaron. Corren en cada push:

```bash
./scripts/arch-lint.sh          # R36, R38, R48, R52, R55 y las válvulas
ddev exec php artisan arch:check   # R46, R53
ddev exec vendor/bin/phpstan analyse   # R9, R13, R14, R17, R18, R21, R24, R29, R39, R42
```

Las reglas que hablan de módulos todavía no se comprueban, y los dos comandos
lo dicen en su última línea en vez de dar un verde que no significa nada.

Mientras tanto:

- **Para entender por qué el proyecto está organizado como está** y hacia dónde
  va, lee las reglas.
- **Para saber cómo se escribe código hoy**, mira los módulos que ya existen
  (`Personal › Usuarios` es el más completo) y copia su forma.

Cuando la migración se haga, esta sección desaparece.
