#!/usr/bin/env bash
# =============================================================================
# Laravel 12 Boilerplate — First-time project setup
# =============================================================================
# Usage:
#   bash setup.sh
#
# What it does:
#   1. Asks for your DDEV project name (→ subdomain: <name>.ddev.site)
#   2. Updates .ddev/config.yaml with that name
#   3. Creates .env from .env.ddev.example
#   4. Starts DDEV and installs all dependencies
#   5. Runs migrations and opens the browser
# =============================================================================
set -euo pipefail

# ── Colors ────────────────────────────────────────────────────────────────────
BOLD='\033[1m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
YELLOW='\033[0;33m'
RED='\033[0;31m'
DIM='\033[2m'
RESET='\033[0m'

# ── Helpers ───────────────────────────────────────────────────────────────────
step()  { echo -e "  ${CYAN}→${RESET} $1"; }
ok()    { echo -e "  ${GREEN}✓${RESET} $1"; }
warn()  { echo -e "  ${YELLOW}!${RESET} $1"; }
error() { echo -e "  ${RED}✗${RESET} $1"; exit 1; }

# Cross-platform sed -i (macOS BSD vs Linux GNU)
sed_inplace() {
    if [[ "$OSTYPE" == "darwin"* ]]; then
        sed -i '' "$@"
    else
        sed -i "$@"
    fi
}

# ── Header ────────────────────────────────────────────────────────────────────
echo ""
echo -e "  ${BOLD}Laravel 12 Boilerplate — Project Setup${RESET}"
echo -e "  ${DIM}PHP 8.5 · PostgreSQL · Bun · Alpine.js · Tailwind v4${RESET}"
echo ""

# ── Check: must be run from the project root ──────────────────────────────────
if [[ ! -f ".ddev/config.yaml" ]]; then
    error "Run this script from the project root (where .ddev/ lives)."
fi

# ── Check: DDEV installed ─────────────────────────────────────────────────────
if ! command -v ddev &>/dev/null; then
    echo -e "  ${RED}DDEV not found. Install it first:${RESET}"
    echo ""
    echo -e "    ${BOLD}Mac:${RESET}          brew install ddev/ddev/ddev"
    echo -e "    ${BOLD}Windows (WSL2):${RESET} curl -fsSL https://ddev.com/install.sh | bash"
    echo ""
    exit 1
fi

ok "DDEV $(ddev version | grep 'DDEV version' | awk '{print $NF}') found"

# ── Check: already set up ─────────────────────────────────────────────────────
if [[ -f ".env" ]]; then
    warn ".env already exists — project may already be set up."
    echo -e "  ${DIM}If you want to reconfigure, delete .env first and re-run this script.${RESET}"
    echo ""
    read -r -p "  Continue anyway? [y/N] " CONTINUE
    [[ "$CONTINUE" =~ ^[Yy]$ ]] || { echo "Aborted."; exit 0; }
    echo ""
fi

# ── Ask: project name ─────────────────────────────────────────────────────────
# Default: slugified version of the current directory name
DEFAULT_NAME=$(basename "$(pwd)" | tr '[:upper:]' '[:lower:]' | tr '_' '-' | sed 's/[^a-z0-9-]/-/g' | sed 's/^-//;s/-$//')

echo -e "  ${BOLD}Project name${RESET} (becomes the DDEV subdomain)"
echo -e "  ${DIM}Rules: lowercase letters, numbers and hyphens only. Must start with a letter.${RESET}"
echo -e "  ${DIM}Leave blank to use the default: ${BOLD}${DEFAULT_NAME}${RESET}${DIM}${RESET}"
echo ""
read -r -p "  Project name [${DEFAULT_NAME}]: " PROJECT_NAME
PROJECT_NAME="${PROJECT_NAME:-$DEFAULT_NAME}"

# Validate
if [[ ! "$PROJECT_NAME" =~ ^[a-z][a-z0-9-]*$ ]]; then
    error "Invalid name '${PROJECT_NAME}'. Use only lowercase letters, numbers and hyphens, starting with a letter."
fi

echo ""
echo -e "  ┌──────────────────────────────────────────────────────┐"
echo -e "  │  ${BOLD}Project:${RESET}   ${PROJECT_NAME}"
echo -e "  │  ${BOLD}App URL:${RESET}   https://${PROJECT_NAME}.ddev.site"
echo -e "  │  ${BOLD}Vite HMR:${RESET}  https://${PROJECT_NAME}.ddev.site:5173"
echo -e "  └──────────────────────────────────────────────────────┘"
echo ""
read -r -p "  Proceed with this configuration? [Y/n] " CONFIRM
CONFIRM="${CONFIRM:-Y}"
[[ "$CONFIRM" =~ ^[Yy]$ ]] || { echo "Aborted."; exit 0; }
echo ""

# ── 1. Update .ddev/config.yaml ───────────────────────────────────────────────
step "Updating .ddev/config.yaml (name: ${PROJECT_NAME})"
sed_inplace "s/^name:.*/name: ${PROJECT_NAME}/" .ddev/config.yaml
ok "config.yaml updated"

# ── 2. Create .env ────────────────────────────────────────────────────────────
step "Creating .env from .env.ddev.example"
cp .env.ddev.example .env
sed_inplace "s/<project-name>/${PROJECT_NAME}/g" .env
ok ".env created"

# ── 3. Start DDEV ─────────────────────────────────────────────────────────────
step "Starting DDEV (first run pulls Docker images — may take a few minutes)"
ddev start
ok "DDEV started → https://${PROJECT_NAME}.ddev.site"

# ── 4. PHP dependencies ───────────────────────────────────────────────────────
step "Installing PHP dependencies (composer install)"
ddev composer install --no-interaction
ok "PHP dependencies installed"

# ── 5. Generate app key ───────────────────────────────────────────────────────
step "Generating application key"
ddev artisan key:generate
ok "APP_KEY generated"

# ── 6. JS dependencies ───────────────────────────────────────────────────────
step "Installing JS dependencies (bun install)"
ddev bun install
ok "JS dependencies installed (Alpine.js · Tailwind v4 · Vite)"

# ── 7. Migrations ─────────────────────────────────────────────────────────────
step "Running database migrations"
ddev artisan migrate --no-interaction
ok "Migrations complete (PostgreSQL)"

# ── Done ──────────────────────────────────────────────────────────────────────
echo ""
echo -e "  ${GREEN}${BOLD}Setup complete!${RESET}"
echo ""
echo -e "  ${BOLD}App:${RESET}      https://${PROJECT_NAME}.ddev.site"
echo -e "  ${BOLD}Mailpit:${RESET}  https://${PROJECT_NAME}.ddev.site:8026"
echo ""
echo -e "  ${BOLD}Start Vite HMR${RESET} (open a second terminal and run):"
echo -e "    ${CYAN}ddev bun run dev${RESET}"
echo ""
echo -e "  ${BOLD}Daily workflow:${RESET}"
echo -e "    ${DIM}ddev start           # start environment${RESET}"
echo -e "    ${DIM}ddev bun run dev     # Vite HMR${RESET}"
echo -e "    ${DIM}ddev artisan migrate # run pending migrations${RESET}"
echo -e "    ${DIM}ddev stop            # stop environment${RESET}"
echo ""

ddev launch
