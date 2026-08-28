#!/usr/bin/env bash
#
# arch-lint.sh — checks textuales de docs/ARCHITECTURE_RULES.md
#
# Vive en bash y no en artisan porque no hay PHP en el host: cualquier comando
# de Laravel arranca en ~1 s y no cabe en el pre-commit junto a Pint. Aquí solo
# van los checks que se resuelven leyendo texto. Si un check necesita entender
# el código, va a `php artisan arch:check`.
#
#   ./scripts/arch-lint.sh                  todas las reglas
#   ./scripts/arch-lint.sh --rule=R52       una sola
#   ./scripts/arch-lint.sh --files=a.php,b  solo esos archivos
#
# Sale 1 si falla un `error`. Un `warning` avisa y no rompe.

set -uo pipefail

cd "$(dirname "${BASH_SOURCE[0]}")/.." || exit 2

if [[ -t 1 ]]; then
    RED=$'\e[31m'; YEL=$'\e[33m'; GRN=$'\e[32m'; DIM=$'\e[2m'; OFF=$'\e[0m'
else
    RED=''; YEL=''; GRN=''; DIM=''; OFF=''
fi

ONLY_RULE=''
ONLY_FILES=''

for arg in "$@"; do
    case "$arg" in
        --rule=*)  ONLY_RULE="${arg#*=}" ;;
        --files=*) ONLY_FILES="${arg#*=}" ;;
        -h|--help) sed -n '2,20p' "$0" | sed 's/^# \{0,1\}//'; exit 0 ;;
        *) echo "opción desconocida: $arg" >&2; exit 2 ;;
    esac
done

FAILED=0
WARNED=0

# Ámbito de código propio. `resources/views` entra aparte porque solo aplica a
# algunas reglas, y los config/ publicados por paquetes quedan fuera siempre.
CODE_DIRS=(app routes database tests)
OWN_CONFIGS=(config/menu.php config/roles.php config/notifications.php)

# Lista los archivos a revisar: los de --files si se pasó, o todo el ámbito.
# Un archivo borrado o fuera del ámbito se descarta en silencio.
targets() {
    local ext="$1"; shift
    local dirs=("$@")

    if [[ -n "$ONLY_FILES" ]]; then
        tr ',' '\n' <<<"$ONLY_FILES" | while read -r f; do
            [[ -f "$f" && "$f" == *"$ext" ]] && echo "$f"
        done
        return
    fi

    local d
    for d in "${dirs[@]}"; do
        [[ -e "$d" ]] || continue
        if [[ -d "$d" ]]; then
            find "$d" -type f -name "*$ext" 2>/dev/null
        else
            [[ "$d" == *"$ext" ]] && echo "$d"
        fi
    done
}

report() {
    local rule="$1" severity="$2" detail="$3"

    if [[ "$severity" == error ]]; then
        FAILED=1
        printf '%s✗ %s%s  %s\n' "$RED" "$rule" "$OFF" "$detail"
    else
        WARNED=1
        printf '%s! %s%s  %s\n' "$YEL" "$rule" "$OFF" "$detail"
    fi
}

pass() { printf '%s✓ %s%s  %s\n' "$GRN" "$1" "$OFF" "$2"; }
skip() { printf '%s· %s   %s%s\n' "$DIM" "$1" "$2" "$OFF"; }

wants() { [[ -z "$ONLY_RULE" || "$ONLY_RULE" == "$1" ]]; }

# R36 · un tag semver alcanzable desde trunk
check_R36() {
    local described
    if described=$(git describe --tags --match 'v*' 2>/dev/null); then
        pass R36 "versión alcanzable: $described"
    else
        report R36 error "ningún tag \`v*\` es ancestro de HEAD. R35 se apoya en esta referencia; ciérrala con \`git tag -a vX.Y.Z\`"
    fi
}

# R38 · las migraciones cambian esquema, no datos
check_R38() {
    local found=0 f
    while read -r f; do
        [[ -n "$f" ]] || continue
        grep -qE '(->update\(|DB::statement)' "$f" || continue
        grep -q 'Schema::' "$f" && continue
        report R38 error "$f transforma datos sin tocar esquema; va a un comando de Artisan"
        found=1
    done < <(targets .php database/migrations)

    (( found )) || pass R38 "ninguna migración transforma datos"
}

# R2 · un módulo es una carpeta bajo app/Modules/
check_R2() {
    local stray
    stray=$(find app/Modules -maxdepth 1 -type f 2>/dev/null)

    if [[ -n "$stray" ]]; then
        report R2 error "hay archivos sueltos en app/Modules/, que solo contiene módulos:"
        sed 's/^/      /' <<<"$stray"
        return
    fi
    pass R2 "$(find app/Modules -maxdepth 1 -mindepth 1 -type d | wc -l) módulos, sin archivos sueltos"
}

# R3 · el mínimo de un módulo es su ServiceProvider y su README
check_R3() {
    local found=0 m name
    while read -r m; do
        [[ -n "$m" ]] || continue
        name=$(basename "$m")
        [[ -f "$m/${name}ServiceProvider.php" ]] || { report R3 error "$m no tiene ${name}ServiceProvider.php"; found=1; }
        [[ -f "$m/README.md" ]] || { report R3 error "$m no tiene README.md"; found=1; }
    done < <(find app/Modules -maxdepth 1 -mindepth 1 -type d)

    (( found )) || pass R3 "todos los módulos declaran ServiceProvider y README"
}

# R5 · la plataforma son exactamente Access y Platform
check_R5() {
    local found=0 m
    for m in Access Platform; do
        [[ -d "app/Modules/$m" ]] || { report R5 error "falta el módulo de plataforma $m"; found=1; }
    done
    # Un módulo de negocio no puede llamarse como uno de plataforma; con dos
    # nombres reservados el check es la comprobación de existencia de arriba.
    (( found )) || pass R5 "la plataforma son Access y Platform"
}

# R6 · dentro de un módulo, las carpetas son por tipo
# Lista cerrada: si hace falta un tipo nuevo, se añade aquí y al árbol de R6,
# que es donde se discute. Inventar una carpeta en un módulo y que nadie se
# entere es como «dominio» acabó significando dos cosas.
check_R6() {
    local allowed=" Actions Config Console Contracts Data Database Enums Events Exceptions Http Jobs Listeners Livewire Models Notifications Observers Policies Resources Routes Rules Services Tests Traits "
    local found=0 d name
    while read -r d; do
        [[ -n "$d" ]] || continue
        name=$(basename "$d")
        [[ "$allowed" == *" $name "* ]] && continue
        report R6 error "$d no es un tipo de la lista de R6"
        found=1
    done < <(find app/Modules -mindepth 2 -maxdepth 2 -type d)

    (( found )) || pass R6 "las carpetas de los módulos son tipos conocidos"
}

# R25 · toda tabla de un módulo lleva el prefijo de su módulo
# La lista de exentas vive en config/arch.php, que R30 lee también: dos copias
# es lo que hace que una se quede corta.
check_R25() {
    local exempt prefixes found=0 table file renamed
    exempt=$(sed -n "s/^        '\([a-z_]*\)',$/\1/p" config/arch.php | tr '\n' ' ')
    prefixes=$(sed -n "s/.*'module_prefixes' => \[\(.*\)\].*/\1/p" config/arch.php | tr -d "' " | tr ',' '|')

    # Lo que cuenta es el nombre final, no el de la migración que la creó: una
    # tabla renombrada después cumple la regla aunque naciera sin prefijo.
    renamed=" $(find app/Modules database/migrations -name '*.php' 2>/dev/null \
        | xargs -r grep -hoE "Schema::rename\('[a-z_]+'" 2>/dev/null \
        | sed "s/.*'\(.*\)'/\1/" | tr '\n' ' ')"

    while read -r file; do
        [[ -n "$file" ]] || continue
        while read -r table; do
            [[ -n "$table" ]] || continue
            [[ " $exempt " == *" $table "* ]] && continue
            [[ "$renamed" == *" $table "* ]] && continue
            [[ "$table" =~ ^(${prefixes})_ ]] && continue
            report R25 error "$(basename "$file") crea «$table» sin prefijo de módulo"
            found=1
        done < <(grep -oE "Schema::create\('[a-z_]+'" "$file" | sed "s/.*'\(.*\)'/\1/")
    done < <(find app/Modules database/migrations -name '*.php' -not -path '*/Tests/*' 2>/dev/null)

    (( found )) || pass R25 "toda tabla propia lleva el prefijo de su módulo"
}

# R40 · un permiso se llama {modulo}.{recurso}.{accion}, en inglés
# Los permisos de Spatie son globales y únicos: sin el prefijo del módulo, dos
# módulos que usen la misma palabra comparten permiso sin darse cuenta.
check_R40() {
    local prefixes hits
    prefixes=$(sed -n "s/.*'module_prefixes' => \[\(.*\)\].*/\1/p" config/arch.php | tr -d "' " | tr ',' '|')

    hits=$(targets .php .blade.php "${CODE_DIRS[@]}" config \
        | xargs -r grep -nE "(@can\(|->authorize\(|can\(|permission:|Permission::[a-z]+\(\[?'name')[^)]*['\"](permission:)?[a-záéíóúñ]+ [a-záéíóúñ ]+['\"]" 2>/dev/null \
        | grep -vE "['\"](${prefixes})\.")

    if [[ -z "$hits" ]]; then
        pass R40 "los permisos llevan módulo, recurso y acción en inglés"
        return
    fi

    report R40 error "$(wc -l <<<"$hits") permisos con nombre en español:"
    head -5 <<<"$hits" | sed 's/^/      /' | cut -c1-110
}

# R26 · las migraciones de un módulo viven dentro del módulo
# Lo que queda en database/migrations/ es infraestructura sin dueño: cache,
# jobs y tokens. Cualquier otra cosa ahí pertenece a algún módulo.
check_R26() {
    local infra=" 0001_01_01_000001_create_cache_table.php 0001_01_01_000002_create_jobs_table.php 2026_02_19_210632_create_personal_access_tokens_table.php "
    local found=0 f name
    while read -r f; do
        [[ -n "$f" ]] || continue
        name=$(basename "$f")
        [[ "$infra" == *" $name "* ]] && continue
        report R26 error "$f está fuera de su módulo"
        found=1
    done < <(find database/migrations -name '*.php' 2>/dev/null)

    (( found )) || pass R26 "solo quedan migraciones de infraestructura en database/"
}

# R44 · toda clave de cache lleva el prefijo de su módulo
check_R44() {
    local hits
    hits=$(targets .php "${CODE_DIRS[@]}" \
        | xargs -r grep -nE "(Cache::(get|put|remember|rememberForever|forget|has)|cache\(\)->(get|put|remember|forget))\(\s*['\"][a-z_-]+['\"]" 2>/dev/null \
        | grep -vE "['\"](access|platform):")

    if [[ -z "$hits" ]]; then
        pass R44 "ninguna clave de cache sin prefijo de módulo"
        return
    fi

    report R44 error "$(wc -l <<<"$hits") claves de cache sin prefijo:"
    head -5 <<<"$hits" | sed 's/^/      /' | cut -c1-100
}

# R37 · toda migración declara down()
# La otra mitad de R37 es el test de ida y vuelta. Son dos afirmaciones y por
# eso son dos checks: si solo estuviera el test, una migración sin `down()`
# fallaría por rebote —al recrear su tabla— y el nombre del fallo apuntaría al
# sitio equivocado. `Migrator` omite un `down()` ausente sin decir nada.
check_R37() {
    local found=0 f
    while read -r f; do
        [[ -n "$f" ]] || continue
        grep -q 'function down' "$f" && continue
        report R37 error "$f no declara down(); Migrator lo omite en silencio"
        found=1
    done < <(targets .php database/migrations)

    (( found )) || pass R37 "todas las migraciones declaran down()"
}

# R48 · sin banners ni separadores ASCII
check_R48() {
    local hits
    hits=$(targets .php "${CODE_DIRS[@]}" "${OWN_CONFIGS[@]}" \
        | xargs -r grep -nE '(─{2,}|═{2,}|\|-{6,}|={6,}|-{6,})' 2>/dev/null)

    if [[ -z "$hits" ]]; then
        pass R48 "sin banners ASCII"
        return
    fi

    local count
    count=$(wc -l <<<"$hits")
    report R48 error "$count separadores decorativos:"
    head -8 <<<"$hits" | sed 's/^/      /' | cut -c1-100
    (( count > 8 )) && echo "      … y $((count - 8)) más"
}

# R52 · un archivo no pasa de 350 líneas
check_R52() {
    local found=0 lines f
    while read -r f; do
        [[ -n "$f" ]] || continue
        lines=$(wc -l <"$f")
        (( lines > 350 )) || continue
        grep -q 'arch-accepted: R52' "$f" && continue
        report R52 error "$f tiene $lines líneas"
        found=1
    done < <({ targets .php "${CODE_DIRS[@]}"; targets .blade.php resources/views; })

    (( found )) || pass R52 "ningún archivo pasa de 350 líneas"
}

# R55 · el baseline lleva fecha de vencimiento y no ha pasado
check_R55() {
    local file=phpstan-baseline.neon

    [[ -f "$file" ]] || { pass R55 "no hay baseline"; return; }

    local date
    date=$(grep -oE '^# arch-baseline: vence [0-9]{4}-[0-9]{2}-[0-9]{2}' "$file" \
        | grep -oE '[0-9]{4}-[0-9]{2}-[0-9]{2}')

    if [[ -z "$date" ]]; then
        report R55 error "$file no declara \`# arch-baseline: vence YYYY-MM-DD\`. Sin fecha la deuda se olvida"
        return
    fi

    if [[ "$date" < "$(date +%F)" ]]; then
        report R55 error "el baseline venció el $date: corrige las entradas o renueva la fecha con una razón"
    else
        pass R55 "baseline vigente hasta $date"
    fi
}

# EXC · formato y caducidad de las válvulas de escape
# `arch-exception` lleva fecha porque promete arreglarse; `arch-accepted` no la
# lleva porque es una decisión revisada. Confundirlas es lo que convierte una
# excepción en una fecha que alguien corre cada año.
check_EXC() {
    local bad=0 line file num body rule owner date today
    today=$(date +%F)

    while IFS=: read -r file num body; do
        [[ -n "$file" ]] || continue

        if [[ "$body" =~ arch-exception:[[:space:]]*(R[0-9]+)[[:space:]]*·[^·]+·[[:space:]]*(@[A-Za-z0-9_-]+)[[:space:]]*·[[:space:]]*([0-9]{4}-[0-9]{2}-[0-9]{2}) ]]; then
            date="${BASH_REMATCH[3]}"
            if [[ "$date" < "$today" ]]; then
                report EXC error "$file:$num · la excepción de ${BASH_REMATCH[1]} venció el $date"
                bad=1
            fi
        else
            report EXC error "$file:$num · formato inválido. Se espera: arch-exception: R13 · razón · @owner · YYYY-MM-DD"
            bad=1
        fi
    done < <(targets .php "${CODE_DIRS[@]}" | xargs -r grep -n 'arch-exception:' 2>/dev/null)

    while IFS=: read -r file num body; do
        [[ -n "$file" ]] || continue

        if ! [[ "$body" =~ arch-accepted:[[:space:]]*R[0-9]+[[:space:]]*·[^·]+·[[:space:]]*@[A-Za-z0-9_-]+[[:space:]]*$ ]]; then
            report EXC error "$file:$num · formato inválido. Se espera: arch-accepted: R52 · razón · @owner (sin fecha)"
            bad=1
        fi
    done < <(targets .php "${CODE_DIRS[@]}" | xargs -r grep -n 'arch-accepted:' 2>/dev/null)

    (( bad )) || pass EXC "las válvulas de escape tienen formato válido y ninguna venció"
}

# Reglas que este script todavía no comprueba, y por qué. Se declaran en vez de
# callar: un check que da verde porque no encontró nada que mirar enseña a no
# creerle (R56).
PENDING='R33 (una FK que cruce módulos aún no se comprueba)'

main() {
    echo "arch-lint · docs/ARCHITECTURE_RULES.md"
    echo

    local rule
    for rule in R2 R3 R5 R6 R25 R26 R40 R36 R37 R38 R44 R48 R52 R55 EXC; do
        wants "$rule" && "check_$rule"
    done

    if [[ -z "$ONLY_RULE" ]]; then
        echo
        skip "sin verificar:" "$PENDING"
    fi

    echo
    if (( FAILED )); then
        echo "${RED}arch-lint: falla${OFF}"
        return 1
    fi
    (( WARNED )) && echo "${YEL}arch-lint: pasa con avisos${OFF}" || echo "${GRN}arch-lint: pasa${OFF}"
    return 0
}

main
