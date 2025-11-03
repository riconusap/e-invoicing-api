#!/bin/bash

set -e

print_green() {
  printf "\033[32m%s\033[0m\n" "$1"
}

print_yellow() {
  printf "\033[33m%s\033[0m\n" "$1"
}

print_red() {
  printf "\033[31m%s\033[0m\n" "$1"
}

# Main deployment function
deploy_application() {
    print_yellow "🚀 Starting deployment process..."

    # Ensure we're in the project directory
    if [ ! -f "docker-compose.prod.yml" ]; then
        print_red "❌ docker-compose.prod.yml not found. Are you in the correct directory?"
        exit 1
    fi

    # Create .env file if it doesn't exist
    if [ ! -f ".env" ] && [ -f ".env.production" ]; then
        print_yellow "📄 Creating .env from .env.production..."
        cp .env.production .env
    fi

    # Stop existing containers
    print_yellow "🛑 Stopping existing containers..."
    docker-compose -f docker-compose.prod.yml down --remove-orphans || true

    # Build and start services
    print_yellow "🏗️  Building and starting services..."
    docker-compose -f docker-compose.prod.yml up -d --build

    # Wait for services to be ready
    print_yellow "⏳ Waiting for services to be ready..."
    sleep 30

    # Run migrations and optimize application
    print_yellow "🧹 Running migrations and optimizing application..."
    docker-compose -f docker-compose.prod.yml exec -T app composer install --no-dev --optimize-autoloader
    docker-compose -f docker-compose.prod.yml exec -T app php artisan key:generate --force || true
    docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
    docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
    docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

    # Run additional migration script
    print_yellow "🗄️ Running additional migration script..."
    ./run_migrations.sh

    print_green "✅ Deployment completed successfully!"
}

# Main execution
main() {
    print_green "🚀 Simplified Deployment Script"
    deploy_application
    print_green "🎉 Deployment finished!"
}

main "$@"
