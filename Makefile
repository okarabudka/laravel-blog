# Makefile — local Docker workflow. Run `make help` for the list of targets.

SHELL := /bin/bash
.DEFAULT_GOAL := help

export UID := $(shell id -u)
export GID := $(shell id -g)

COMPOSE := docker compose
# -T keeps exec usable without a TTY (CI, scripts).
EXEC := $(COMPOSE) exec -T php

.PHONY: help certs env up down fresh sh test test-mariadb lint fix

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-13s\033[0m %s\n", $$1, $$2}'

certs: ## Create a self-signed TLS certificate (skipped if present)
	@sh docker/conf/ssl/generate.sh

env: ## Create .env from .env.example (skipped if present)
	@test -f .env || cp .env.example .env

up: certs env ## Build and start containers, install dependencies, migrate and seed the demo blog
	$(COMPOSE) up -d --build --wait
	$(EXEC) composer install --no-interaction
	@grep -q '^APP_KEY=.\+' .env || $(EXEC) php artisan key:generate
	$(EXEC) php artisan migrate --force
	$(EXEC) php artisan db:seed --force
	$(EXEC) php artisan storage:link
	@source .env && echo "Blog: https://localhost:$${HTTPS_PORT:-8443}  Admin: /admin  Mailpit: http://localhost:$${MAILPIT_PORT:-8025}"

down: ## Stop and remove containers (the database volume is kept)
	$(COMPOSE) down

fresh: ## Recreate the database with fresh demo data
	$(EXEC) php artisan migrate:fresh --seed --force

sh: ## Open a shell in the PHP container
	$(COMPOSE) exec php sh

test: ## Run the test suite
	$(EXEC) composer test

test-mariadb: ## Run the test suite against MariaDB instead of in-memory SQLite
	@source .env && $(COMPOSE) exec -T -e DB_CONNECTION=mariadb -e DB_DATABASE=$${DB_DATABASE}_test php php artisan test

lint: ## Check code style (Pint) and run static analysis (PHPStan, level max)
	$(EXEC) composer lint

fix: ## Fix code style (Pint)
	$(EXEC) composer fix
