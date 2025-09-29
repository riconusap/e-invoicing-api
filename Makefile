# E-Invoice API Docker Management
.PHONY: help build up down restart logs shell artisan composer test migrate seed fresh

# Default target
help: ## Show this help message
	@echo "E-Invoice API Docker Commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

setup: ## Initial setup with Docker
	@./docker-setup.sh

build: ## Build Docker containers
	docker-compose build --no-cache

up: ## Start containers in background
	docker-compose up -d

down: ## Stop and remove containers
	docker-compose down

restart: ## Restart all containers
	docker-compose restart

logs: ## Show container logs
	docker-compose logs -f

shell: ## Access the app container shell
	docker-compose exec app sh

artisan: ## Run artisan command (usage: make artisan CMD="migrate")
	docker-compose exec app php artisan $(CMD)

composer: ## Run composer command (usage: make composer CMD="install")
	docker-compose exec app composer $(CMD)

test: ## Run PHPUnit tests
	docker-compose exec app php artisan test

migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migration with seeding
	docker-compose exec app php artisan migrate:fresh --seed

seed: ## Run database seeders
	docker-compose exec app php artisan db:seed

cache-clear: ## Clear all caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

permissions: ## Fix file permissions
	docker-compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
	docker-compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

status: ## Show container status
	docker-compose ps