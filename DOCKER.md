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

## 🌊 Digital Ocean Production Deployment

### Prerequisites for Digital Ocean
- Ubuntu 20.04+ droplet
- At least 1GB RAM (2GB recommended)
- Sudo access to the server

### Quick Production Deployment

1. **Upload your code to the server:**
   ```bash
   # On your local machine
   rsync -avz --exclude node_modules --exclude vendor . user@your-server-ip:/home/user/e-invoicing-api/
   
   # Or use git
   ssh user@your-server-ip
   git clone https://github.com/your-username/e-invoicing-api.git
   cd e-invoicing-api
   ```

2. **Run the deployment script:**
   ```bash
   # This will install Docker, configure firewall, and deploy the app
   ./deploy.sh
   ```

3. **Configure your domain (optional):**
   ```bash
   # Update your DNS A record to point to your server IP
   # Then update .env file:
   APP_URL=http://yourdomain.com
   ```

### Manual Production Setup

If you prefer manual setup:

```bash
# 1. Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
sudo usermod -aG docker $USER

# 2. Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# 3. Configure firewall
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw --force enable

# 4. Deploy application
cp .env.production .env
# Edit .env with your settings
docker-compose -f docker-compose.prod.yml up -d
```

### Production Configuration

Create `.env` file from `.env.production` template and update:
```bash
APP_URL=http://YOUR_SERVER_IP_OR_DOMAIN
APP_KEY=base64:YOUR_GENERATED_KEY
DB_PASSWORD=YOUR_SECURE_DB_PASSWORD
REDIS_PASSWORD=YOUR_SECURE_REDIS_PASSWORD
```

### Digital Ocean Specific Troubleshooting

#### 🚫 "Can't access API from external"

**Possible causes and solutions:**

1. **Port binding issue:**
   ```bash
   # Check if nginx is binding to all interfaces
   docker-compose -f docker-compose.prod.yml exec nginx netstat -tlnp
   # Should show 0.0.0.0:80, not 127.0.0.1:80
   ```

2. **Firewall blocking connections:**
   ```bash
   # Check UFW status
   sudo ufw status
   
   # Allow HTTP traffic
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   
   # Check iptables
   sudo iptables -L
   ```

3. **Digital Ocean Firewall:**
   - Check Digital Ocean control panel → Networking → Firewalls
   - Ensure inbound rules allow HTTP (80) and HTTPS (443)

4. **Container health issues:**
   ```bash
   # Check container status
   docker-compose -f docker-compose.prod.yml ps
   
   # Check container logs
   docker-compose -f docker-compose.prod.yml logs nginx
   docker-compose -f docker-compose.prod.yml logs app
   
   # Test internal connectivity
   docker-compose -f docker-compose.prod.yml exec nginx curl -f http://app:9000
   ```

5. **Nginx configuration:**
   ```bash
   # Test nginx config
   docker-compose -f docker-compose.prod.yml exec nginx nginx -t
   
   # Check nginx is listening
   docker-compose -f docker-compose.prod.yml exec nginx ss -tlnp
   ```

#### 🔍 Debugging Steps

1. **Test from inside the server:**
   ```bash
   # Test locally on the server
   curl http://localhost/health
   curl http://localhost/api/
   
   # If this works but external doesn't, it's a firewall issue
   ```

2. **Test network connectivity:**
   ```bash
   # From your local machine
   telnet YOUR_SERVER_IP 80
   nmap -p 80 YOUR_SERVER_IP
   ```

3. **Check application logs:**
   ```bash
   # Laravel application logs
   docker-compose -f docker-compose.prod.yml exec app tail -f storage/logs/laravel.log
   
   # Nginx access logs
   docker-compose -f docker-compose.prod.yml logs nginx | grep -E "(GET|POST|PUT|DELETE)"
   ```

4. **Verify environment:**
   ```bash
   # Check environment variables
   docker-compose -f docker-compose.prod.yml exec app env | grep APP_
   
   # Test database connection
   docker-compose -f docker-compose.prod.yml exec app php artisan tinker --execute="DB::connection()->getPdo();"
   ```

#### 🔧 Common Fixes

1. **Force rebuild containers:**
   ```bash
   docker-compose -f docker-compose.prod.yml down -v
   docker-compose -f docker-compose.prod.yml build --no-cache
   docker-compose -f docker-compose.prod.yml up -d
   ```

2. **Reset networking:**
   ```bash
   docker network prune -f
   docker-compose -f docker-compose.prod.yml down
   docker-compose -f docker-compose.prod.yml up -d
   ```

3. **Check and fix permissions:**
   ```bash
   docker-compose -f docker-compose.prod.yml exec app chown -R www-data:www-data storage bootstrap/cache
   docker-compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
   ```

#### 📊 Monitoring Commands

```bash
# Container resource usage
docker stats

# System resource usage
htop
df -h
free -h

# Service status
docker-compose -f docker-compose.prod.yml ps
systemctl status docker

# Network connections
ss -tlnp | grep :80
netstat -tlnp | grep :80
```

### 🎯 Quick Fix Commands

```bash
# Restart everything
docker-compose -f docker-compose.prod.yml restart

# Rebuild and restart
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d --build

# Check if API is responding
curl -I http://$(curl -s ifconfig.me)/health

# View real-time logs
docker-compose -f docker-compose.prod.yml logs -f --tail=50
```

### 💡 Production Tips

1. **Use a domain instead of IP:**
   - Point your domain A record to your server IP
   - Update `APP_URL` in `.env`
   - Consider setting up SSL/TLS with Let's Encrypt

2. **Set up monitoring:**
   - Use tools like Uptime Robot for external monitoring
   - Set up log aggregation
   - Monitor disk space and memory usage

3. **Regular maintenance:**
   ```bash
   # Clean up old containers and images
   docker system prune -f
   
   # Update system packages
   sudo apt update && sudo apt upgrade -y
   
   # Backup database
   docker-compose -f docker-compose.prod.yml exec mysql mysqldump -u root -p einvoicedb > backup.sql
   ```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.configuration.php)
- [Digital Ocean Docker Guide](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-on-ubuntu-20-04)

1. **Port conflicts**: Change ports in `docker-compose.yml`
2. **Permission denied**: Run `make permissions`
3. **Database connection**: Ensure MySQL is fully started (wait 30s)
4. **Composer errors**: Clear cache with `docker-compose exec app composer clear-cache`

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.configuration.php)
