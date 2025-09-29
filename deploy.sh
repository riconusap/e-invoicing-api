#!/bin/bash

# Digital Ocean E-Invoice API Deployment Script
# This script automates the deployment of Laravel API on Digital Ocean

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

print_blue() {
  printf "\033[34m%s\033[0m\n" "$1"
}

# Function to check if running as root
check_root() {
    if [[ $EUID -eq 0 ]]; then
        print_red "❌ This script should not be run as root for security reasons"
        print_yellow "Create a non-root user and add to docker group instead"
        exit 1
    fi
}

# Function to install Docker if not present
install_docker() {
    if ! command -v docker &> /dev/null; then
        print_yellow "🐳 Installing Docker..."
        curl -fsSL https://get.docker.com -o get-docker.sh
        sh get-docker.sh
        sudo usermod -aG docker $USER
        print_green "✅ Docker installed successfully"
        print_yellow "⚠️  Please log out and log back in for group changes to take effect"
        rm get-docker.sh
    else
        print_green "✅ Docker is already installed"
    fi
}

# Function to install Docker Compose if not present
install_docker_compose() {
    if ! command -v docker-compose &> /dev/null; then
        print_yellow "🔧 Installing Docker Compose..."
        sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
        sudo chmod +x /usr/local/bin/docker-compose
        print_green "✅ Docker Compose installed successfully"
    else
        print_green "✅ Docker Compose is already installed"
    fi
}

# Function to configure firewall
configure_firewall() {
    print_yellow "🔥 Configuring UFW firewall..."

    # Install UFW if not present
    if ! command -v ufw &> /dev/null; then
        sudo apt-get update
        sudo apt-get install -y ufw
    fi

    # Configure firewall rules
    sudo ufw --force reset
    sudo ufw default deny incoming
    sudo ufw default allow outgoing
    sudo ufw allow ssh
    sudo ufw allow 80/tcp
    sudo ufw allow 443/tcp
    sudo ufw --force enable

    print_green "✅ Firewall configured successfully"
}

# Function to create swap if system has low memory
create_swap() {
    local mem_total=$(grep MemTotal /proc/meminfo | awk '{print $2}')
    local mem_gb=$((mem_total / 1024 / 1024))

    if [ "$mem_gb" -lt 2 ]; then
        print_yellow "💾 Creating swap file for low memory system..."
        sudo fallocate -l 2G /swapfile
        sudo chmod 600 /swapfile
        sudo mkswap /swapfile
        sudo swapon /swapfile
        echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
        print_green "✅ Swap file created successfully"
    fi
}

# Function to optimize system for production
optimize_system() {
    print_yellow "⚡ Optimizing system for production..."

    # Update system packages
    sudo apt-get update
    sudo apt-get upgrade -y

    # Install essential packages
    sudo apt-get install -y curl wget git htop unzip

    # Set timezone
    sudo timedatectl set-timezone Asia/Jakarta

    print_green "✅ System optimized successfully"
}

# Main deployment function
deploy_application() {
    print_yellow "🚀 Starting deployment process..."

    # Ensure we're in the project directory
    if [ ! -f "docker-compose.prod.yml" ]; then
        print_red "❌ docker-compose.prod.yml not found. Are you in the correct directory?"
        exit 1
    fi

    # Create production environment file if it doesn't exist
    if [ ! -f ".env" ]; then
        if [ -f ".env.production" ]; then
            print_yellow "📄 Creating .env from .env.production template..."
            cp .env.production .env

            # Get server IP
            SERVER_IP=$(curl -s ifconfig.me)
            print_blue "🌐 Detected server IP: $SERVER_IP"

            # Prompt for configuration
            print_yellow "⚙️  Please configure the following in .env file:"
            print_yellow "   - APP_URL=http://$SERVER_IP"
            print_yellow "   - APP_KEY (generate with: docker-compose exec app php artisan key:generate)"
            print_yellow "   - DB_PASSWORD (set a secure password)"
            print_yellow "   - REDIS_PASSWORD (set a secure password)"

            read -p "Press Enter after updating .env file..."
        else
            print_red "❌ No .env or .env.production file found"
            exit 1
        fi
    fi

    # Stop any existing containers
    print_yellow "🛑 Stopping existing containers..."
    docker-compose -f docker-compose.prod.yml down --remove-orphans || true

    # Pull latest images and build
    print_yellow "🏗️  Building application..."
    docker-compose -f docker-compose.prod.yml build --no-cache

    # Start services
    print_yellow "🚀 Starting production services..."
    docker-compose -f docker-compose.prod.yml up -d

    # Wait for services to be ready
    print_yellow "⏳ Waiting for services to be ready..."
    sleep 30

    # Install dependencies and setup application
    print_yellow "📦 Installing dependencies..."
    docker-compose -f docker-compose.prod.yml exec -T app composer install --no-dev --optimize-autoloader

    # Generate application key if not set
    if ! docker-compose -f docker-compose.prod.yml exec -T app php artisan tinker --execute="echo config('app.key');" | grep -q "base64:"; then
        print_yellow "🔑 Generating application key..."
        docker-compose -f docker-compose.prod.yml exec -T app php artisan key:generate --force
    fi

    # Run migrations
    print_yellow "🗄️  Running database migrations..."
    docker-compose -f docker-compose.prod.yml exec -T app php artisan migrate --force

    # Clear and cache configurations
    print_yellow "🧹 Optimizing application..."
    docker-compose -f docker-compose.prod.yml exec -T app php artisan config:cache
    docker-compose -f docker-compose.prod.yml exec -T app php artisan route:cache
    docker-compose -f docker-compose.prod.yml exec -T app php artisan view:cache

    # Set proper permissions
    print_yellow "🔒 Setting file permissions..."
    docker-compose -f docker-compose.prod.yml exec -T app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
    docker-compose -f docker-compose.prod.yml exec -T app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

    print_green "✅ Deployment completed successfully!"
}

# Function to show deployment info
show_deployment_info() {
    SERVER_IP=$(curl -s ifconfig.me)
    print_green ""
    print_green "🎉 Deployment Summary:"
    print_green "========================"
    print_green "🌐 API Endpoint: http://$SERVER_IP"
    print_green "🔍 Health Check: http://$SERVER_IP/health"
    print_green "📊 Server IP: $SERVER_IP"
    print_green ""
    print_green "🛠️  Useful Commands:"
    print_green "   View logs: docker-compose -f docker-compose.prod.yml logs -f"
    print_green "   Restart: docker-compose -f docker-compose.prod.yml restart"
    print_green "   Stop: docker-compose -f docker-compose.prod.yml down"
    print_green "   Shell access: docker-compose -f docker-compose.prod.yml exec app sh"
    print_green ""
    print_yellow "🔧 Troubleshooting:"
    print_yellow "   - Check firewall: sudo ufw status"
    print_yellow "   - Check containers: docker ps"
    print_yellow "   - Check logs: docker-compose -f docker-compose.prod.yml logs"
    print_yellow "   - Test API: curl http://$SERVER_IP/health"
}

# Main execution
main() {
    print_green "🚀 Digital Ocean E-Invoice API Deployment"
    print_green "=========================================="

    # Pre-flight checks
    check_root

    # System setup
    optimize_system
    create_swap
    install_docker
    install_docker_compose
    configure_firewall

    # Application deployment
    deploy_application

    # Show deployment information
    show_deployment_info

    print_green ""
    print_green "🎊 Deployment completed successfully!"
    print_yellow "💡 Remember to update DNS records to point to your server IP"
}

# Run main function
main "$@"
