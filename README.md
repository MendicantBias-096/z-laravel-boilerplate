# Laravel 12 Boilerplate

> **TALL Stack:** Laravel 12 · Livewire 4 · TallStackUI v2 · Alpine.js 3 · Tailwind CSS 4
>
> **Entorno:** PHP 8.5 · PostgreSQL 16 · Bun · Vite HMR · DDEV

Boilerplate completo para aplicaciones Laravel empresariales. Incluye autenticación con 2FA, roles y permisos, auditoría, gestión de medios, notificaciones en tiempo real, localización multi-idioma y una arquitectura modular por dominios.

---

## Características principales

### Autenticación completa (Fortify)
- Login y registro con validación
- Verificación de email (compatible con Resend y Mailpit)
- Recuperación de contraseña
- Autenticación de dos factores (TOTP + códigos de recuperación)
- Usuarios protegidos (`is_protected`) inmunes a eliminación y edición
- Usuarios desactivables (`is_active`) con bloqueo de sesión y login

### Roles y permisos (Spatie)
- CRUD completo de roles con `display_name`
- Permisos granulares por módulo: `ver`, `crear`, `editar`, `eliminar`, `restaurar`
- Asignación de permisos individuales por usuario
- Plantillas de permisos basadas en rol
- Protección a nivel de ruta, menú y componente

### Auditoría (owen-it/laravel-auditing)
- Registro automático de cambios en modelos `User`, `Profile` y `Role`
- Historial completo de creación, edición y eliminación

### Gestión de medios (Spatie Media Library)
- Fotos de perfil de usuario
- Logo y favicon del sistema configurables desde la interfaz
- Almacenamiento en disco público con URLs generadas

### Notificaciones
- Sistema basado en eventos con permisos (`config/notifications.php`)
- Notificaciones de base de datos con soft delete
- Campana en tiempo real con broadcasting (Reverb)
- Notificaciones de CRUD de usuarios y roles

### Localización
- Español e inglés completos (enum `Language`)
- Selector de idioma en navbar y configuración
- Middleware `SetLocale` con prioridad: sesión → perfil → config
- Cookie persistente de preferencia de idioma

### Interfaz de usuario
- TallStackUI v2 como biblioteca de componentes (inputs, modals, toasts, tabs, badges, etc.)
- Modo oscuro/claro con persistencia por cookie
- Sidebar colapsable con menú configurable (`config/menu.php`)
- Subheader con icono y título por página
- Footer fijo con nombre del sistema y desarrollador

### Páginas públicas
- Landing page con hero, features y stack tecnológico
- Página "Sobre nosotros"
- Navbar público con selector de idioma
- Diseño con ondas animadas y soporte dark/light

### Configuración del sistema
- Página de ajustes con tabs: Perfil, Sistema, Roles y permisos
- Nombre, logo y favicon editables desde la UI
- Modelo `Setting` con caché automática

---

## Stack tecnológico

| Paquete | Versión | Propósito |
|---|---|---|
| PHP | 8.5 | Runtime |
| Laravel Framework | 12 | Framework backend |
| Livewire | 4 | Componentes reactivos |
| TallStackUI | 2 | Biblioteca de componentes Blade |
| Tailwind CSS | 4 | Framework CSS utility-first |
| Alpine.js | 3 | Interactividad frontend |
| Spatie Permission | 7 | Roles y permisos |
| Spatie Media Library | 11 | Gestión de archivos y medios |
| Laravel Auditing | 14 | Auditoría de cambios en modelos |
| Laravel Fortify | 1 | Autenticación headless con 2FA |
| Laravel Sanctum | — | Tokens API |
| Laravel Reverb | — | WebSockets en tiempo real |

---

## Arquitectura

### Organización por dominios

El área autenticada se organiza en **dominios** y cada dominio contiene **módulos**:

| Dominio | Prefijo URL | Archivo de rutas | Módulos |
|---|---|---|---|
| General | `/` | `routes/general.php` | Dashboard, Settings, Notifications |
| Personal | `/personal` | `routes/personal.php` | Usuarios, Roles |

### Patrón de tres capas

Cada página sigue esta convención:

```
Ruta       →  fn () => view('{wrapper}')          nunca apunta a clase Livewire
Wrapper    →  {module}/index.blade.php             <x-layouts.app> + @livewire(...)
Componente →  {module}/_index.blade.php            HTML real, sin layout
Livewire   →  {Module}/Index.php                   return view('...._index')
```

### Estructura de archivos

```
app/
├── Actions/Fortify/              # Acciones de auth (crear usuario, reset password)
├── Auth/Responses/               # Respuestas personalizadas de Fortify
├── Enums/Language.php            # Enum de idiomas soportados
├── Events/NewNotification.php    # Evento broadcast de notificaciones
├── Http/Middleware/
│   ├── EnsureUserIsActive.php    # Expulsa usuarios desactivados/eliminados
│   └── SetLocale.php             # Establece idioma de la app
├── Livewire/
│   ├── App/{Domain}/{Module}/    # Componentes del área autenticada
│   ├── Auth/                     # Componentes de autenticación
│   ├── Forms/UserForm.php        # Form Object de usuarios
│   ├── Layouts/Navbar.php        # Barra de navegación
│   └── Public/                   # Componentes de páginas públicas
├── Models/
│   ├── User.php                  # Usuario con soft delete y protección
│   ├── Profile.php               # Perfil (nombre, foto, locale)
│   ├── Role.php                  # Rol (extiende Spatie con auditoría)
│   ├── Setting.php               # Configuración key-value con caché
│   └── DatabaseNotification.php  # Notificación con soft delete
├── Notifications/                # Clases de notificación por evento
└── Services/
    └── NotificationsService.php  # Dispatcher de notificaciones por permiso

config/
├── menu.php                      # Estructura del sidebar
├── notifications.php             # Mapeo evento → permiso → canales
├── roles.php                     # Módulos y permisos por grupo
└── permission.php                # Config de Spatie Permission

lang/
├── es/                           # Traducciones en español
└── en/                           # Traducciones en inglés

resources/views/
├── app/{domain}/{module}/        # Vistas del área autenticada
├── auth/                         # Vistas de autenticación
├── components/
│   ├── layouts/                  # Layouts (app, public, sidebar, navbar)
│   └── app/settings/             # Componentes de tabs de configuración
└── public/                       # Vistas de páginas públicas

routes/
├── web.php                       # Rutas públicas y auth
├── general.php                   # Dominio General (dashboard, settings)
├── personal.php                  # Dominio Personal (usuarios, roles)
├── channels.php                  # Canales de broadcast
└── breadcrumbs.php               # Breadcrumbs
```

---

## Quick Start

```bash
# 1. Clonar y entrar al directorio
git clone https://github.com/MendicantBias-096/z-laravel-boilerplate mi-proyecto
cd mi-proyecto

# 2. Ejecutar el setup interactivo
bash setup.sh
```

El script te pedirá el nombre del proyecto y configurará todo: DDEV, `.env`, dependencias, migraciones, seeders y apertura del navegador.

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

### Usuarios por defecto

| Rol | Email | Contraseña | Notas |
|---|---|---|---|
| Admin | `admin@example.com` | `zygma-online-boilerplate-2026-1.0.0` | Protegido, no eliminable |
| User | `user@example.com` | `password` | Usuario estándar |

> **Alcance de DDEV:** DDEV es exclusivamente una herramienta de **desarrollo local**. Gestiona contenedores Docker en tu máquina para replicar el entorno de producción. Para desplegar en un VPS, consulta la sección [Producción en VPS](#producción-en-vps).

---

## Tabla de contenido

1. [Características principales](#características-principales)
2. [Stack tecnológico](#stack-tecnológico)
3. [Arquitectura](#arquitectura)
4. [Quick Start](#quick-start)
5. [Requisitos](#requisitos)
6. [Instalación del entorno](#instalación-del-entorno)
7. [Setup manual (sin script)](#setup-manual-sin-script)
8. [Configurar la base de datos](#configurar-la-base-de-datos)
9. [Vite + HMR en DDEV](#vite--hmr-en-ddev)
10. [Flujo de trabajo diario](#flujo-de-trabajo-diario)
11. [Comandos disponibles](#comandos-disponibles)
12. [HMR Troubleshooting](#hmr-troubleshooting)
13. [Compatibilidad Herd / Laragon / Valet](#compatibilidad-herd--laragon--valet)
14. [Producción en VPS](#producción-en-vps)

---

## Requisitos

DDEV necesita un **motor Docker** corriendo.

| Plataforma | Motor Docker | Herramientas |
|---|---|---|
| **Mac** | [Docker Desktop](https://www.docker.com/products/docker-desktop/) (gratis, uso personal) | Homebrew, DDEV 1.25+ |
| **Windows** | Docker Engine en WSL2 | WSL2 + Ubuntu 22.04+, DDEV 1.25+ |
| **Linux** | Docker Engine | DDEV 1.25+ |

> Existen alternativas como OrbStack, Colima o Rancher Desktop, pero Docker Desktop (Mac) y Docker Engine (Windows/Linux) son las opciones recomendadas por defecto.

---

## Instalación del entorno

### Mac

```bash
# 1. Homebrew (si no lo tienes)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. Docker Desktop
brew install --cask docker

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

**2. Docker Engine — dentro de Ubuntu (WSL2):**

```bash
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
# Cerrar y reabrir la terminal para aplicar el grupo
docker run hello-world   # verificar
```

**3. DDEV:**

```bash
sudo apt update && sudo apt upgrade -y
curl -fsSL https://ddev.com/install.sh | bash
ddev version
```

> Todos los comandos del proyecto (`bash setup.sh`, `ddev start`, etc.) se ejecutan desde la terminal de Ubuntu en WSL2.

### Linux

```bash
# 1. Docker Engine
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
# Cerrar y reabrir la terminal

# 2. DDEV
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
ddev artisan migrate --seed
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

> Los datos existentes se pierden al cambiar el tipo de base de datos.

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
ddev artisan migrate --seed
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

DDEV es la vía principal de este boilerplate. Sin embargo, puedes usar otras herramientas con pasos adicionales.

### Compatibilidad de `vite.config.js`

El boilerplate **ya tiene soporte incorporado** para entornos sin DDEV:

```js
const isDdev = !!process.env.DDEV_HOSTNAME
// Fuera de DDEV, Vite usa sus propios defaults sin interferencia.
```

### Qué necesitas configurar manualmente

| | Herd Free | Herd Pro | Laragon | XAMPP |
|---|---|---|---|---|
| **PHP 8.5** | ✅ | ✅ | ✅ | ❌ |
| **PostgreSQL** | Manual¹ | ✅ incluido | ✅ Quick Add² | ❌ |
| **Bun** | `brew install bun` | `brew install bun` | instalador manual | instalador manual |
| **Vite HMR** | ✅ sin cambios | ✅ sin cambios | ✅ sin cambios | ⚠️ problemático |
| **Plataformas** | Mac / Win | Mac / Win | Solo Windows | Mac / Win / Linux |

¹ Herd Free: instalar PostgreSQL por separado (DBngin o instalador oficial).
² Laragon: Menu → Quick Add → PostgreSQL.

### Setup con Herd o Laragon

```bash
# 1. Clonar
git clone https://github.com/MendicantBias-096/z-laravel-boilerplate mi-proyecto
cd mi-proyecto

# 2. Configurar .env
cp .env.example .env
# Editar: APP_URL, DB_CONNECTION, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 3. Instalar dependencias
composer install
php artisan key:generate
bun install

# 4. Migraciones y seeders
php artisan migrate --seed

# 5. Vite (terminal separada)
bun run dev
```

---

## Producción en VPS

> DDEV **no se usa en producción**. En el VPS sirves la app directamente con Nginx + PHP-FPM.

### Aliases globales + Makefile por proyecto

**1. Aliases en el servidor** (`~/.bashrc`):

```bash
alias php85='php8.5'
alias composer85='php8.5 /usr/local/bin/composer'
```

**2. Makefile por proyecto** con la versión de PHP explícita:

```makefile
PHP      = php8.5
COMPOSER = php8.5 /usr/local/bin/composer

install:
	$(COMPOSER) install --no-dev --optimize-autoloader

build:
	bun install --frozen-lockfile
	bun run build

migrate:
	$(PHP) artisan migrate --force

cache:
	$(PHP) artisan optimize

deploy: install build migrate cache
```

**Uso:**

```bash
cd /var/www/mi-proyecto
make deploy
```

### Nginx + PHP-FPM

```nginx
server {
    listen 443 ssl;
    server_name mi-proyecto.com;
    root /var/www/mi-proyecto/public;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Instalar PHP en Ubuntu/Debian

```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.5-fpm php8.5-cli php8.5-pgsql php8.5-mbstring php8.5-xml php8.5-curl php8.5-zip
```

---

## Créditos

[Laravel](https://laravel.com) · [Livewire](https://livewire.laravel.com) · [TallStackUI](https://tallstackui.com) · [Alpine.js](https://alpinejs.dev) · [Tailwind CSS](https://tailwindcss.com) · [Spatie](https://spatie.be) · [DDEV](https://ddev.com) · [Bun](https://bun.sh)
