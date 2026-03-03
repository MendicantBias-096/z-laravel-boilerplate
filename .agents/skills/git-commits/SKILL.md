---
name: git-commits
description: Enforces Spanish Conventional Commits and branch naming for this project.
license: MIT
compatibility: claude_code, codex, cursor, opencode
---

## Cuándo activar

Activar siempre que se vaya a crear un commit o una rama nueva.
Frases de activación: "commit", "hacer commit", "rama", "nueva rama", "branch", "versionar", "tag".

---

## Reglas de commits

### Formato obligatorio

```
<tipo>: <descripción en español en imperativo>

[cuerpo opcional — explicar el por qué, no el qué]
```

### Tipos permitidos

| Tipo | Cuándo usarlo |
|---|---|
| `feat` | Nueva funcionalidad |
| `fix` | Corrección de bug |
| `chore` | Tareas de mantenimiento, dependencias |
| `refactor` | Refactorización sin cambio de comportamiento |
| `docs` | Solo documentación |
| `style` | Formato, espacios (sin cambio de lógica) |
| `test` | Añadir o corregir tests |
| `perf` | Mejoras de rendimiento |

### Reglas estrictas

- **Idioma:** español — título y cuerpo
- **Sin co-autores** — nunca añadir `Co-Authored-By`
- **Título en imperativo** — "agregar", "corregir", "eliminar" (no "agregado", "se corrigió")
- **Título sin punto final**
- **Título ≤ 72 caracteres**
- **Cuerpo separado del título con línea en blanco**

### Ejemplos correctos

```
feat: agregar módulo de usuarios con CRUD completo
```

```
fix: corregir validación de email en formulario de registro
```

```
chore: actualizar dependencias de composer
```

```
refactor: extraer lógica de permisos a PolicyService

Centraliza la verificación de permisos que estaba duplicada
en tres controladores distintos.
```

### Ejemplos incorrectos

```
# ❌ en inglés
feat: add user module

# ❌ con co-autor
feat: agregar módulo
Co-Authored-By: Claude <noreply@anthropic.com>

# ❌ pasado, no imperativo
feat: agregado módulo de usuarios

# ❌ con punto final
feat: agregar módulo de usuarios.
```

---

## Reglas de ramas

### Formato

```
<tipo>/<descripcion-en-kebab-case-español>
```

### Tipos de rama

| Tipo | Cuándo usarlo |
|---|---|
| `feat/` | Nueva funcionalidad |
| `fix/` | Corrección de bug |
| `chore/` | Mantenimiento |
| `refactor/` | Refactorización |
| `docs/` | Documentación |

### Ejemplos correctos

```
feat/modulo-usuarios
fix/validacion-registro
chore/actualizar-dependencias
refactor/servicio-permisos
```

### Ejemplos incorrectos

```
# ❌ en inglés
feat/user-module

# ❌ sin tipo
usuarios

# ❌ camelCase
feat/moduloUsuarios
```

---

## Flujo al hacer commit

1. Revisar `git status` y `git diff` para entender todos los cambios
2. Agrupar cambios relacionados — preferir commits atómicos
3. Redactar título siguiendo el formato: `<tipo>: <descripción imperativo español>`
4. Añadir cuerpo solo si el "por qué" no es obvio
5. Nunca incluir `Co-Authored-By`
