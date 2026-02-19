# Laravel 12 Boilerplate

> **Stack:** Laravel 12 · PHP 8.5 · PostgreSQL 16 · Bun · Alpine.js · Tailwind CSS v4 · Vite HMR · DDEV

Boilerplate listo para clonar. Al clonarlo, un script interactivo te pregunta el nombre de tu proyecto y configura todo automáticamente — DDEV, `.env`, dependencias, migraciones y apertura del navegador.

> **Alcance de DDEV:** DDEV es exclusivamente una herramienta de **desarrollo local**. Gestiona contenedores Docker en tu máquina para replicar el entorno de producción sin instalar PHP, PostgreSQL ni Nginx directamente en el sistema operativo. Para desplegar en un VPS, consulta la sección [Producción en VPS](#producción-en-vps).

---

## Quick Start

```bash
# 1. Clonar y entrar al directorio
git clone https://github.com/MendicantBias-096/z-laravel-boilerplate mi-proyecto
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
12. [Producción en VPS](#producción-en-vps)

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

DDEV necesita un **motor Docker** corriendo. No requiere Docker Desktop específicamente — es solo una de varias formas de obtener ese motor.

### Mac

| Herramienta | Notas |
|---|---|
| Motor Docker | Elige una opción abajo |
| DDEV 1.25+ | Instalado con Homebrew |

**Opciones de motor Docker en Mac** (elige una):

| Opción | Coste | Notas |
|---|---|---|
| [Docker Desktop](https://www.docker.com/products/docker-desktop/) | Gratis (uso personal) | La más conocida |
| [OrbStack](https://orbstack.dev) | Gratis (uso personal) | Más ligero y rápido, recomendado |
| [Colima](https://github.com/abiosoft/colima) | Gratis / open source | Línea de comandos, sin GUI |
| [Rancher Desktop](https://rancherdesktop.io) | Gratis / open source | Con GUI |

> En Mac, Docker Engine no corre de forma nativa (necesita una VM Linux). Cualquiera de las opciones anteriores provee esa VM. No existe una opción "solo engine" como en Linux.

### Windows

| Herramienta | Notas |
|---|---|
| WSL2 + Ubuntu 22.04+ | Requerido para DDEV |
| Motor Docker | Elige una opción abajo |
| DDEV 1.25+ | Instalado **dentro de WSL2** |

**Opciones de motor Docker en Windows** (elige una):

| Opción | Coste | Notas |
|---|---|---|
| Docker Engine en WSL2 | Gratis / open source | Sin GUI, instala directo en Ubuntu. **Recomendado** |
| [Docker Desktop](https://www.docker.com/products/docker-desktop/) | Gratis (uso personal) | Con GUI, configura WSL2 automáticamente |
| [Rancher Desktop](https://rancherdesktop.io) | Gratis / open source | Alternativa con GUI |

> **Docker Desktop no es obligatorio en Windows.** Puedes instalar Docker Engine directamente en Ubuntu (WSL2) y DDEV lo detecta sin problemas.

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

**1. Habilitar WSL2 — PowerShell (Administrador):**

```powershell
wsl --install
# Reiniciar el equipo
wsl --set-default-version 2
```

**2. Motor Docker — elige una opción:**

**Opción A: Docker Engine directo en WSL2 (sin Docker Desktop)**

```bash
# Dentro de Ubuntu (WSL2)
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
# Cerrar y reabrir la terminal para aplicar el grupo
docker run hello-world   # verificar
```

**Opción B: Docker Desktop**
- Instalar [Docker Desktop para Windows](https://www.docker.com/products/docker-desktop/)
- Settings → General → activar **"Use the WSL 2 based engine"**
- Settings → Resources → WSL Integration → activar Ubuntu

**3. DDEV — dentro de Ubuntu (WSL2):**

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

## Entornos locales alternativos (Herd, Laragon, XAMPP)

DDEV es la vía principal de este boilerplate porque gestiona PHP, PostgreSQL, Nginx y el proxy de Vite en contenedores aislados, sin tocar el sistema operativo. Sin embargo, puedes usar otras herramientas con pasos adicionales.

### Compatibilidad de `vite.config.js`

El boilerplate **ya tiene soporte incorporado** para entornos sin DDEV:

```js
const isDdev = !!process.env.DDEV_HOSTNAME
// DDEV_HOSTNAME solo existe dentro del contenedor DDEV.
// Fuera de él, Vite usa sus propios defaults sin interferencia.
```

Esto significa que con Herd, Laragon o Valet, Vite HMR **funciona sin ningún cambio** en `vite.config.js`. Simplemente ejecutas `bun run dev` en tu terminal.

### Qué necesitas configurar manualmente

| | Herd Free | Herd Pro | Laragon | XAMPP |
|---|---|---|---|---|
| **PHP 8.5** | ✅ | ✅ | ✅ | ❌ |
| **PostgreSQL** | Manual¹ | ✅ incluido | ✅ Quick Add² | ❌ muy complejo |
| **Bun** | `brew install bun` | `brew install bun` | instalador manual | instalador manual |
| **Vite HMR** | ✅ sin cambios | ✅ sin cambios | ✅ sin cambios | ⚠️ problemático |
| **Plataformas** | Mac / Win | Mac / Win | Solo Windows | Mac / Win / Linux |
| **Coste** | Gratis | $99/año | Gratis | Gratis |

¹ Herd Free: instalar PostgreSQL por separado (DBngin o instalador oficial) y apuntar `DB_HOST=127.0.0.1`.
² Laragon: Menu → Quick Add → PostgreSQL.

> **XAMPP no está recomendado** para este stack: no soporta PHP 8.5, PostgreSQL requiere configuración muy manual y Vite HMR genera problemas frecuentes con WebSockets.

### Setup con Herd o Laragon

En lugar de `bash setup.sh` (que configura DDEV), haz los pasos manualmente:

```bash
# 1. Clonar
git clone https://github.com/MendicantBias-096/z-laravel-boilerplate mi-proyecto
cd mi-proyecto

# 2. Instalar Bun en tu máquina (si no lo tienes)
# Mac:
brew install bun
# Windows: https://bun.sh

# 3. Configurar .env desde la plantilla neutral
cp .env.example .env
# Editar .env:
#   APP_URL=http://mi-proyecto.test
#   DB_CONNECTION=pgsql (o mysql si usas MySQL)
#   DB_HOST=127.0.0.1
#   DB_DATABASE=mi_proyecto
#   DB_USERNAME=...
#   DB_PASSWORD=...

# 4. Instalar dependencias
composer install
php artisan key:generate
bun install

# 5. Migraciones
php artisan migrate

# 6. Vite en modo desarrollo (terminal separada)
bun run dev
```

El `Makefile` también funciona si tienes `make` disponible, pero apuntando a tu PHP local:

```bash
# Ajusta la variable PHP al inicio del Makefile si usas Herd/Laragon
PHP = php          # o php8.5 si tienes múltiples versiones
COMPOSER = composer
```

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

## Producción en VPS

> DDEV **no se usa en producción**. En el VPS sirves la app directamente con Nginx + PHP-FPM instalados en el sistema.

### El problema: múltiples versiones de PHP en el mismo VPS

Cuando tienes varios proyectos con distintas versiones de PHP, el punto de dolor es el **CLI**: el comando `php` apunta a una sola versión global, pero Composer y Artisan necesitan la versión correcta de cada proyecto.

```bash
# Nginx sirve con la versión correcta ✅ (configurado por socket FPM)
fastcgi_pass unix:/run/php/php8.3-fpm.sock;

# Pero en terminal, composer usa el PHP global ❌
composer update   # puede estar usando PHP 8.5 en un proyecto que requiere 8.3
```

### Solución: aliases globales + Makefile por proyecto

**1. Añadir aliases en el servidor** (`~/.bashrc` o `~/.zshrc`):

```bash
# ~/.bashrc — aliases de PHP por versión
alias php83='php8.3'
alias php84='php8.4'
alias php85='php8.5'

alias composer83='php8.3 /usr/local/bin/composer'
alias composer84='php8.4 /usr/local/bin/composer'
alias composer85='php8.5 /usr/local/bin/composer'
```

```bash
source ~/.bashrc
```

**2. Makefile en cada proyecto** con la versión de PHP explícita:

Cada proyecto define su propio `PHP` y `COMPOSER` en el `Makefile`, de forma que cualquier operación usa la versión correcta sin importar cuál sea el `php` global del servidor.

```makefile
# Makefile — variables de entorno del proyecto
PHP      = php8.5
COMPOSER = php8.5 /usr/local/bin/composer

# ── Dependencias ──────────────────────────────────────────────
install: ## Instalar dependencias PHP (producción, sin dev)
	$(COMPOSER) install --no-dev --optimize-autoloader

update: ## Actualizar dependencias
	$(COMPOSER) update

# ── Assets ────────────────────────────────────────────────────
build: ## Compilar assets con Bun
	bun install --frozen-lockfile
	bun run build

# ── Laravel ───────────────────────────────────────────────────
migrate: ## Ejecutar migraciones pendientes
	$(PHP) artisan migrate --force

cache: ## Cachear config, rutas y vistas para producción
	$(PHP) artisan optimize

cache-clear: ## Limpiar todos los cachés
	$(PHP) artisan optimize:clear

queue-restart: ## Reiniciar workers de cola
	$(PHP) artisan queue:restart

# ── Despliegue completo ───────────────────────────────────────
deploy: install build migrate cache ## Despliegue completo (install + build + migrate + cache)
```

**Uso en el servidor:**

```bash
cd /var/www/mi-proyecto
make deploy        # despliegue completo
make migrate       # solo migraciones
make cache-clear   # limpiar caché tras cambios de config
```

### Nginx + PHP-FPM por versión

Cada virtual host apunta al socket FPM de su versión:

```nginx
# /etc/nginx/sites-available/mi-proyecto
server {
    listen 443 ssl;
    server_name mi-proyecto.com;
    root /var/www/mi-proyecto/public;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;  # ← versión del proyecto
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Instalar múltiples versiones de PHP (Ubuntu/Debian)

```bash
# Añadir PPA de Ondřej Surý (todas las versiones de PHP)
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Instalar las versiones que necesites
sudo apt install php8.3-fpm php8.3-cli php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip
sudo apt install php8.5-fpm php8.5-cli php8.5-pgsql php8.5-mbstring php8.5-xml php8.5-curl php8.5-zip

# Verificar que ambas FPM están activas
sudo systemctl status php8.3-fpm
sudo systemctl status php8.5-fpm

# Confirmar binarios disponibles
php8.3 --version
php8.5 --version
```

---

## Créditos

[DDEV](https://ddev.com) · [Laravel](https://laravel.com) · [Bun](https://bun.sh) · [Alpine.js](https://alpinejs.dev) · [Tailwind CSS](https://tailwindcss.com)
