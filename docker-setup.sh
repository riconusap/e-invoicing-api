#!/bin/bash

# E-Invoice API Docker Setup Script
# This script helps set up the Laravel application with Docker

set -e

print_green() {
  printf "\033[32m%s\033[0m\n" "$1"
}

print_red() {
  printf "\033[31m%s\033[0m\n" "$1"
}

print_yellow() {
  printf "\033[33m%s\033[0m\n" "$1"
}

print_green "🚀 Starting E-Invoice API Docker Setup..."

# Check if docker and docker-compose are installed
if ! command -v docker &> /dev/null; then
    print_red "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    print_red "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Stop any running containers
print_yellow "🛑 Stopping any existing containers..."
docker-compose down

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    print_yellow "📄 Creating .env file from .env.example..."
    cp .env.example .env
    print_green "✅ .env file created"
else
    print_yellow "📄 .env file already exists"
fi

# Build and start containers
print_yellow "🏗️  Building Docker containers..."
docker-compose build --no-cache

print_yellow "🚀 Starting Docker containers..."
docker-compose up -d

# Wait for MySQL to be ready
print_yellow "⏳ Waiting for MySQL to be ready..."
sleep 30

# Install Composer dependencies
print_yellow "📦 Installing Composer dependencies..."
docker-compose exec app composer install

# Generate application key
print_yellow "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Run database migrations
print_yellow "🗄️  Running database migrations..."
docker-compose exec app php artisan migrate

# Clear application caches
print_yellow "🧹 Clearing application caches..."
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Set proper permissions
print_yellow "🔒 Setting proper file permissions..."
docker-compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker-compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

print_green "✅ Setup completed successfully!"
print_green ""
print_green "🌐 Your application is now running at:"
print_green "   - API: http://localhost:8080"
print_green "   - phpMyAdmin: http://localhost:8081"
print_green ""
print_green "📊 Database Connection Details:"
print_green "   - Host: localhost"
print_green "   - Port: 3032"
print_green "   - Database: einvoicedb"
print_green "   - Username: root"
print_green "   - Password: rootpass"
print_green ""
print_green "🛠️  Useful Docker commands:"
print_green "   - View logs: docker-compose logs -f"
print_green "   - Stop containers: docker-compose down"
print_green "   - Restart containers: docker-compose restart"
print_green "   - Run artisan commands: docker-compose exec app php artisan [command]"
print_green "   - Access app container: docker-compose exec app sh"
print_green ""
print_yellow "🎉 Happy coding!"
