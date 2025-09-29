# E-Invoice API - Docker Setup

This Laravel-based E-Invoice API can be easily run using Docker. This setup includes PHP 8.2, MySQL 8.0, Nginx, Redis, and phpMyAdmin.

## 🚀 Quick Start

### Prerequisites
- Docker
- Docker Compose

### Setup

1. **Clone and navigate to the project:**
   ```bash
   git clone <repository-url>
   cd e-invoicing-api
   ```

2. **Run the setup script:**
   ```bash
   ./docker-setup.sh
   ```

   Or manually:
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Build and start containers
   docker-compose up -d
   
   # Install dependencies
   docker-compose exec app composer install
   
   # Generate app key
   docker-compose exec app php artisan key:generate
   
   # Run migrations
   docker-compose exec app php artisan migrate
   ```

3. **Access your application:**
   - **API**: http://localhost:8080
   - **phpMyAdmin**: http://localhost:8081

## 🐳 Docker Services

| Service | Container Name | Ports | Description |
|---------|----------------|-------|-------------|
| app | e-invoicing-app | - | PHP 8.2-FPM application |
| nginx | e-invoicing-nginx | 8080:80 | Web server |
| mysql | e-invoicing-mysql | 3032:3306 | MySQL 8.0 database |
| phpmyadmin | e-invoicing-phpmyadmin | 8081:80 | Database management |
| redis | e-invoicing-redis | 6379:6379 | Redis cache |
| composer | e-invoicing-composer | - | Composer dependency management |
| artisan | e-invoicing-artisan | - | Laravel Artisan commands |

## 🛠️ Common Commands

### Using Makefile (Recommended)
```bash
make help          # Show all available commands
make setup         # Initial setup
make up            # Start containers
make down          # Stop containers
make logs          # Show logs
make shell         # Access app container
make artisan CMD="migrate"     # Run artisan commands
make composer CMD="install"   # Run composer commands
make test          # Run tests
```

### Using Docker Compose
```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Access app container
docker-compose exec app sh

# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller UserController

# Run composer commands
docker-compose exec app composer install
docker-compose exec app composer update

# Run tests
docker-compose exec app php artisan test
```

## 📁 Docker Configuration

### PHP Configuration
- **Version**: PHP 8.2-FPM
- **Extensions**: pdo_mysql, zip, exif, pcntl, gd, intl, mbstring, xml, bcmath, opcache
- **Memory Limit**: 256M
- **Upload Max Size**: 32M
- **Custom Config**: `docker/php/php.ini`

### MySQL Configuration
- **Version**: MySQL 8.0
- **Database**: einvoicedb
- **Username**: root
- **Password**: rootpass
- **Port**: 3032 (external)
- **Custom Config**: `docker/mysql/my.cnf`

### Nginx Configuration
- **Port**: 8080 (external)
- **Document Root**: `/var/www/html/public`
- **Config**: `docker/nginx.conf`

## 🔧 Development

### Environment Variables
Copy `.env.example` to `.env` and adjust as needed:
```bash
APP_URL=http://localhost:8080
DB_HOST=mysql
DB_DATABASE=einvoicedb
DB_USERNAME=root
DB_PASSWORD=rootpass
```

### File Permissions
If you encounter permission issues:
```bash
make permissions
# or
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Database Access
- **Internal**: `mysql:3306` (from within containers)
- **External**: `localhost:3032` (from host machine)
- **phpMyAdmin**: http://localhost:8081

### Xdebug (Development)
Xdebug is configured for development. IDE configuration:
- **Host**: localhost
- **Port**: 9003
- **Path Mapping**: `/var/www/html` → `<project-root>`

## 🧪 Testing

```bash
# Run all tests
make test

# Run specific test
docker-compose exec app php artisan test --filter=UserTest

# Run with coverage
docker-compose exec app php artisan test --coverage
```

## 🚀 Production

For production deployment, create a `docker-compose.prod.yml`:
```bash
# Build for production
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Or use environment-specific configs
docker-compose --env-file .env.production up -d
```

## 📝 Troubleshooting

### Common Issues

1. **Port conflicts**: Change ports in `docker-compose.yml`
2. **Permission denied**: Run `make permissions`
3. **Database connection**: Ensure MySQL is fully started (wait 30s)
4. **Composer errors**: Clear cache with `docker-compose exec app composer clear-cache`

### Reset Everything
```bash
docker-compose down -v
docker system prune -f
./docker-setup.sh
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.configuration.php)