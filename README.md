# Laravel 12 Boilerplate

> **Stack:** Laravel 12 · PHP 8.5 · PostgreSQL 16 · Bun · Alpine.js · Tailwind CSS v4 · Vite HMR · DDEV

Boilerplate listo para clonar. Al clonarlo, un script interactivo te pregunta el nombre de tu proyecto y configura todo automáticamente — DDEV, `.env`, dependencias, migraciones y apertura del navegador.

---

## Quick Start

```bash
# 1. Clonar y entrar al directorio
git clone https://github.com/tu-org/laravel12-boilerplate mi-proyecto
cd mi-proyecto

# 2. Ejecutar el setup interactivo
bash setup.sh
```

El script te pedirá el nombre del proyecto y hará todo lo demás:

```
  Laravel 12 Boilerplate — Project Setup
  PHP 8.5 · PostgreSQL · Bun · Alpine.js · Tailwind v4

  Project name (becomes the DDEV subdomain)
  Leave blank to use the default: mi-proyecto

  Project name [mi-proyecto]: my-app

  ┌──────────────────────────────────────────────────────┐
  │  Project:   my-app                                   │
  │  App URL:   https://my-app.ddev.site                 │
  │  Vite HMR:  https://my-app.ddev.site:5173            │
  └──────────────────────────────────────────────────────┘

  Proceed with this configuration? [Y/n]:
```

Una vez completado, abre una segunda terminal para Vite:

```bash
ddev bun run dev
```

---

## Tabla de contenido

1. [Quick Start](#quick-start)
2. [Árbol de archivos](#árbol-de-archivos)
3. [Requisitos](#requisitos)
4. [Instalación del entorno](#instalación-del-entorno)
   - [Mac](#mac)
   - [Windows (WSL2)](#windows-wsl2)
5. [Setup manual (sin script)](#setup-manual-sin-script)
6. [Configurar la base de datos](#configurar-la-base-de-datos)
7. [Vite + HMR en DDEV](#vite--hmr-en-ddev)
8. [Flujo de trabajo diario](#flujo-de-trabajo-diario)
9. [Comandos disponibles](#comandos-disponibles)
10. [HMR Troubleshooting](#hmr-troubleshooting)
11. [Compatibilidad Herd / Laragon / Valet](#compatibilidad-herd--laragon--valet)

---

## Árbol de archivos

```
laravel12-boilerplate/
├── .ddev/
│   ├── config.yaml                  # Configuración DDEV (versionada)
│   ├── commands/web/bun             # Habilita: ddev bun <cmd>
│   └── web-build/Dockerfile         # Instala Bun en el contenedor
├── resources/
│   ├── css/app.css                  # Tailwind CSS v4
│   └── js/app.js                    # Alpine.js entry point
├── .env.example                     # Template .env neutral (Herd/Laragon)
├── .env.ddev.example                # Template .env para DDEV
├── .gitignore
├── Makefile                         # Atajos: make dev, make migrate…
├── README.md
├── setup.sh                         # Script de primer setup interactivo
├── package.json                     # Scripts Bun + Alpine.js + Tailwind
├── vite.config.js                   # Config Vite inteligente (detecta DDEV)
└── [archivos Laravel estándar]      # app/, config/, routes/, etc.
```

---

## Requisitos

### Mac
| Herramienta | Notas |
|---|---|
| Docker Desktop 4.x+ | O [OrbStack](https://orbstack.dev) (más ligero) |
| DDEV 1.25+ | Instalado con Homebrew |

### Windows
| Herramienta | Notas |
|---|---|
| WSL2 + Ubuntu 22.04+ | Habilitar en PowerShell (Admin) |
| Docker Desktop | Activar backend WSL2 + integración Ubuntu |
| DDEV 1.25+ | Instalado dentro de WSL2 |

---

## Instalación del entorno

### Mac

```bash
# 1. Homebrew (si no lo tienes)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. Docker Desktop  —  O alternativamente OrbStack (más ligero):
brew install --cask docker
# brew install --cask orbstack

# 3. DDEV
brew install ddev/ddev/ddev

# Verificar
ddev version
```

### Windows (WSL2)

**En PowerShell (Administrador):**

```powershell
wsl --install
# Reiniciar el equipo
wsl --set-default-version 2
```

**En Docker Desktop:**
- Settings → General → activar "Use the WSL 2 based engine"
- Settings → Resources → WSL Integration → activar Ubuntu

**En la terminal Ubuntu (WSL2):**

```bash
sudo apt update && sudo apt upgrade -y
curl -fsSL https://ddev.com/install.sh | bash
ddev version
```

---

## Setup manual (sin script)

Si prefieres no usar `setup.sh`, puedes hacerlo paso a paso:

### 1. Elegir el nombre del proyecto

Decide el nombre de tu proyecto (será tu subdominio: `<nombre>.ddev.site`).
Solo puede contener letras minúsculas, números y guiones.

### 2. Actualizar `.ddev/config.yaml`

Cambia la línea `name:`:

```yaml
# .ddev/config.yaml
name: mi-proyecto       # ← cambia esto
```

### 3. Crear `.env`

```bash
cp .env.ddev.example .env
```

Edita `.env` y reemplaza **todas** las apariciones de `<project-name>`:

```env
APP_URL=https://mi-proyecto.ddev.site
VITE_DEV_SERVER_URL=https://mi-proyecto.ddev.site:5173
```

### 4. Iniciar y configurar

```bash
ddev start
ddev composer install
ddev artisan key:generate
ddev bun install
ddev artisan migrate
ddev launch
```

---

## Configurar la base de datos

### PostgreSQL (por defecto)

`.ddev/config.yaml` ya configura PostgreSQL 16. Las credenciales dentro de DDEV son siempre:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=db
DB_USERNAME=db
DB_PASSWORD=db
```

### Cambiar a MySQL

**1. Editar `.ddev/config.yaml`:**

```yaml
database:
  type: mysql
  version: "8"
```

**2. Reiniciar DDEV:**

```bash
ddev restart
```

> ⚠️ Los datos existentes se pierden al cambiar el tipo de base de datos.

**3. Actualizar `.env`:**

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=db
DB_USERNAME=db
DB_PASSWORD=db
```

**4. Migrar:**

```bash
ddev artisan migrate
```

---

## Vite + HMR en DDEV

### Cómo funciona

`vite.config.js` detecta automáticamente el entorno DDEV:

```js
const isDdev = !!process.env.DDEV_HOSTNAME
```

- **Dentro de DDEV**: activa `host: '0.0.0.0'`, file polling y HMR via `wss://`.
- **Fuera de DDEV** (Herd, Laragon): el bloque `server` usa los defaults de Vite, sin interferencia.

### Iniciar el servidor de desarrollo

```bash
# Terminal 1: entorno corriendo
ddev start

# Terminal 2: Vite con HMR
ddev bun run dev
```

Verifica que Vite está corriendo abriendo en el navegador:
```
https://<tu-proyecto>.ddev.site:5173/@vite/client
```

---

## Flujo de trabajo diario

```bash
ddev start            # iniciar entorno
ddev bun run dev      # Vite HMR (terminal separada)
ddev artisan migrate  # migraciones pendientes
ddev stop             # detener al terminar
```

---

## Comandos disponibles

### DDEV

| Comando | Descripción |
|---|---|
| `ddev start` | Inicia los contenedores |
| `ddev stop` | Detiene los contenedores |
| `ddev restart` | Reinicia los contenedores |
| `ddev launch` | Abre el proyecto en el navegador |
| `ddev ssh` | Shell dentro del contenedor web |
| `ddev describe` | Muestra URLs y estado del proyecto |
| `ddev bun <cmd>` | Ejecuta cualquier comando Bun |
| `ddev artisan <cmd>` | Ejecuta comandos Artisan |
| `ddev composer <cmd>` | Ejecuta Composer |

### Makefile

```bash
make setup     # Setup interactivo (llama a setup.sh)
make dev       # Iniciar Vite
make build     # Build de producción
make migrate   # Ejecutar migraciones pendientes
make fresh     # migrate:fresh --seed
make logs      # Seguir logs del contenedor
make shell     # Shell en el contenedor
make help      # Ver todos los targets
```

---

## HMR Troubleshooting

### El navegador no carga los assets de Vite

1. **Verifica que Vite está corriendo:** `ddev bun run dev`
2. **Abre el endpoint** `https://<proyecto>.ddev.site:5173/@vite/client` — debe devolver JavaScript.
3. **Verifica el puerto en `config.yaml`:**
   ```yaml
   web_extra_exposed_ports:
     - name: vite
       container_port: 5173
       http_port: 5172
       https_port: 5173
   ```
   Si lo modificaste: `ddev restart`

### HMR no actualiza el navegador

Verifica en `.env`:

```env
APP_URL=https://<proyecto>.ddev.site          # ← debe ser https
VITE_DEV_SERVER_URL=https://<proyecto>.ddev.site:5173
```

Luego:

```bash
ddev artisan optimize:clear
```

### El certificado HTTPS da error

```bash
ddev poweroff
mkcert -install   # instala el certificado CA local
ddev start
```

### Bun no encontrado tras reiniciar

```bash
ddev restart      # rebuild del contenedor (Bun se instala en el Dockerfile)
ddev exec bun --version
```

---

## Compatibilidad Herd / Laragon / Valet

`vite.config.js` **no modifica nada fuera de DDEV**:

```js
const isDdev = !!process.env.DDEV_HOSTNAME
// DDEV_HOSTNAME solo existe dentro del contenedor DDEV
```

Si `DDEV_HOSTNAME` no está definido, el bloque `server` cae al fallback de Vite:

```js
// Fuera de DDEV: solo ignora los archivos de caché de Laravel
server: {
    watch: { ignored: ['**/storage/framework/views/**'] }
}
```

No se necesita ningún ajuste adicional en Herd, Laragon o Valet.

---

## Cambiar el nombre de proyecto después del setup

Si ya corriste `setup.sh` y quieres renombrar el proyecto:

```bash
# 1. Detener DDEV
ddev stop

# 2. Editar .ddev/config.yaml
#    Cambiar: name: nuevo-nombre

# 3. Editar .env
#    Cambiar: APP_URL y VITE_DEV_SERVER_URL con el nuevo nombre

# 4. Reiniciar
ddev start
ddev artisan optimize:clear
```

---

## Créditos

[DDEV](https://ddev.com) · [Laravel](https://laravel.com) · [Bun](https://bun.sh) · [Alpine.js](https://alpinejs.dev) · [Tailwind CSS](https://tailwindcss.com)
