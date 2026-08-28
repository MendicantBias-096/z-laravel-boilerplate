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

# ── R36 · un tag semver alcanzable desde trunk ────────────────────────────────
check_R36() {
    local described
    if described=$(git describe --tags --match 'v*' 2>/dev/null); then
        pass R36 "versión alcanzable: $described"
    else
        report R36 error "ningún tag \`v*\` es ancestro de HEAD. R35 se apoya en esta referencia; ciérrala con \`git tag -a vX.Y.Z\`"
    fi
}

# ── R38 · las migraciones cambian esquema, no datos ───────────────────────────
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

# ── R48 · sin banners ni separadores ASCII ────────────────────────────────────
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

# ── R52 · un archivo no pasa de 350 líneas ────────────────────────────────────
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

# ── R53 · nombres de una letra fuera de la lista blanca ───────────────────────
# Cerrada: $i/$k/$v como índices, $q para closures de query, $a/$b para
# comparadores — las dos últimas son el idioma de Laravel y de PHP, y pelearse
# con ellas cuesta más de lo que aclara.
check_R53() {
    local hits
    hits=$(targets .php "${CODE_DIRS[@]}" \
        | xargs -r grep -nE '\$[a-hj-pr-uwxyz]\b' 2>/dev/null \
        | grep -vE '\$(a|b)\b')

    if [[ -z "$hits" ]]; then
        pass R53 "sin variables de una letra fuera de la lista blanca"
        return
    fi

    report R53 warning "$(wc -l <<<"$hits") nombres de una letra:"
    head -6 <<<"$hits" | sed 's/^/      /' | cut -c1-100
}

# ── R55 · el baseline lleva fecha de vencimiento y no ha pasado ───────────────
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

# ── EXC · formato y caducidad de las válvulas de escape ───────────────────────
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

# ── reglas que esperan a la migración a app/Modules/ ──────────────────────────
# Se declaran omitidas en vez de pasar en silencio: un check que da verde
# porque no encontró nada que mirar enseña a no creerle (R56).
PENDING='R2 R3 R5 R6 R25 R26 R33 R40 R44'

main() {
    echo "arch-lint · docs/ARCHITECTURE_RULES.md"
    echo

    local rule
    for rule in R36 R38 R48 R52 R53 R55 EXC; do
        wants "$rule" && "check_$rule"
    done

    if [[ -z "$ONLY_RULE" && ! -d app/Modules ]]; then
        echo
        skip "$(echo $PENDING | tr ' ' ',')" "omitidas: requieren app/Modules/, que aún no existe"
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
