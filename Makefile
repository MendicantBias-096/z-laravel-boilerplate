.PHONY: help setup start stop restart install update dev build migrate fresh logs shell

# ── Colors ──────────────────────────────────────────────────────────────────
BOLD  := \033[1m
RESET := \033[0m
GREEN := \033[32m
CYAN  := \033[36m

help: ## Show this help message
	@echo ""
	@echo "  $(BOLD)Laravel 12 Boilerplate — DDEV$(RESET)"
	@echo ""
	@printf "  $(CYAN)%-15s$(RESET) %s\n" "Target" "Description"
	@printf "  %-15s %s\n" "──────────────" "────────────────────────────────────"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(RESET) %s\n", $$1, $$2}'
	@echo ""

# ── First-time setup ────────────────────────────────────────────────────────

setup: ## Interactive first-time setup (asks for project name, configures everything)
	@bash setup.sh

# ── DDEV lifecycle ──────────────────────────────────────────────────────────

start: ## Start DDEV
	ddev start

stop: ## Stop DDEV
	ddev stop

restart: ## Restart DDEV
	ddev restart

# ── Dependencies ────────────────────────────────────────────────────────────

install: ## Install PHP and JS dependencies
	ddev composer install
	ddev bun install

update: ## Pull latest changes and refresh the DDEV instance (deps, migrations, cache)
	git pull --ff-only
	ddev composer install
	ddev bun install
	ddev bun run build
	ddev artisan migrate --force
	ddev artisan optimize:clear
	ddev artisan optimize

# ── Frontend ─────────────────────────────────────────────────────────────────

dev: ## Start Vite dev server with HMR
	ddev bun run dev

build: ## Build assets for production
	ddev bun run build

# ── Database ────────────────────────────────────────────────────────────────

migrate: ## Run pending migrations
	ddev artisan migrate

fresh: ## Drop all tables and re-run all migrations + seeders
	ddev artisan migrate:fresh --seed

# ── Utilities ───────────────────────────────────────────────────────────────

logs: ## Follow DDEV web container logs
	ddev logs -f

shell: ## Open a shell inside the DDEV web container
	ddev ssh
