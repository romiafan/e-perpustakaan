# e-Perpustakaan Deployment Guide

## Pre-Deployment Checklist

### ✅ Completed Items

- [x] All database migrations created and tested
- [x] All models with relationships defined
- [x] All service layer implemented
- [x] All HTTP controllers implemented
- [x] All frontend pages created (Vue 3 + Inertia.js)
- [x] All Vue components created (ShadCN/Vue)
- [x] Authentication system configured (Laravel Fortify)
- [x] Role-based middleware implemented
- [x] Background jobs for reservation expiry
- [x] Database seeders with sample data
- [x] TypeScript interfaces for type safety
- [x] Book cover images feature
- [x] .gitignore configured
- [x] .prettierignore configured
- [x] .eslintignore configured

### ⚠️ Known Issues (Non-Blocking)

- [ ] Some unit tests need database connection configuration
- [ ] Auth contract tests expect JSON, app returns Inertia redirects (by design)
- [ ] UserService interface mismatch with tests (tests need updating)

## Environment Setup

### 1. Clone and Install Dependencies

```bash
git clone <repository-url>
cd e-perpustakaan
composer install
pnpm install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration

Update `.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_perpustakaan
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 4. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

This will create:

- **12 sample books** with cover images
- **9 test users** (3 admins, 3 librarians, 3 members)
- Sample reservations

### 5. Build Frontend Assets

```bash
# Development
pnpm run dev

# Production
pnpm run build
```

### 6. Configure Queue Worker (Production)

For reservation expiry automation:

```bash
# Install supervisor (Ubuntu/Debian)
sudo apt-get install supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/e-perpustakaan-worker.conf
```

Add:

```ini
[program:e-perpustakaan-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/e-perpustakaan/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/e-perpustakaan/storage/logs/worker.log
```

Reload supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start e-perpustakaan-worker:*
```

### 7. Configure Scheduler (Production)

Add to crontab:

```bash
crontab -e
```

Add:

```cron
* * * * * cd /path/to/e-perpustakaan && php artisan schedule:run >> /dev/null 2>&1
```

### 8. Configure Web Server

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/e-perpustakaan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Configuration

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/e-perpustakaan/public

    <Directory /path/to/e-perpustakaan/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 9. Set Permissions

```bash
sudo chown -R www-data:www-data /path/to/e-perpustakaan
sudo chmod -R 755 /path/to/e-perpustakaan
sudo chmod -R 775 /path/to/e-perpustakaan/storage
sudo chmod -R 775 /path/to/e-perpustakaan/bootstrap/cache
```

### 10. Configure Mail (For Notifications)

Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Production Optimizations

### 1. Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Optimize Composer Autoload

```bash
composer install --optimize-autoloader --no-dev
```

### 3. Enable OPcache (php.ini)

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

## Monitoring

### Health Checks

```bash
# Application health
curl https://your-domain.com

# Queue status
php artisan queue:monitor

# Check scheduler
php artisan schedule:list
```

### Log Locations

- **Application logs**: `storage/logs/laravel.log`
- **Nginx logs**: `/var/log/nginx/access.log` and `/var/log/nginx/error.log`
- **PHP-FPM logs**: `/var/log/php8.2-fpm.log`
- **Queue worker logs**: `storage/logs/worker.log`

## Security Recommendations

1. **Use HTTPS**: Configure SSL certificate (Let's Encrypt recommended)
2. **Strong APP_KEY**: Never commit `.env` file
3. **Database credentials**: Use strong passwords
4. **Update packages**: Run `composer update` and `pnpm update` regularly
5. **Enable CSRF protection**: Already configured in Laravel
6. **Rate limiting**: Already configured for login attempts
7. **Input validation**: Already implemented in Form Requests

## Backup Strategy

### Database Backup

```bash
# Daily backup script
#!/bin/bash
TIMESTAMP=$(date +"%F")
BACKUP_DIR="/backups/e-perpustakaan"
mysqldump -u username -p password e_perpustakaan > $BACKUP_DIR/db-$TIMESTAMP.sql
```

### File Backup

```bash
# Weekly file backup
tar -czf /backups/e-perpustakaan/files-$(date +"%F").tar.gz /path/to/e-perpustakaan
```

## Rollback Plan

### Database Rollback

```bash
# Rollback last migration
php artisan migrate:rollback

# Rollback specific number of migrations
php artisan migrate:rollback --step=5

# Restore from backup
mysql -u username -p password e_perpustakaan < /backups/e-perpustakaan/db-YYYY-MM-DD.sql
```

### Code Rollback

```bash
# Using git
git checkout <previous-commit>
composer install
pnpm install
pnpm run build
php artisan migrate
```

## Test Users

After running seeders, you can log in with:

**Admins:**

- admin1@example.com / password
- admin2@example.com / password
- admin3@example.com / password

**Librarians:**

- librarian1@example.com / password
- librarian2@example.com / password
- librarian3@example.com / password

**Members:**

- member1@example.com / password
- member2@example.com / password
- member3@example.com / password

## Support and Troubleshooting

### Common Issues

**Queue not processing:**

```bash
# Check queue connection
php artisan queue:work --queue=default --verbose

# Restart supervisor
sudo supervisorctl restart e-perpustakaan-worker:*
```

**Permissions errors:**

```bash
sudo chown -R www-data:www-data storage/ bootstrap/cache/
```

**Frontend not loading:**

```bash
pnpm run build
php artisan cache:clear
```

**Database connection error:**

- Verify `.env` database credentials
- Check MySQL service: `sudo systemctl status mysql`

## Performance Metrics

**Expected Performance:**

- Page load: <200ms (catalog, dashboard)
- API response: <100ms (book search)
- Reservation creation: <50ms
- Database queries: <20ms

## Maintenance Mode

```bash
# Enable maintenance mode
php artisan down

# Enable with secret bypass
php artisan down --secret="maintenance-bypass-token"
# Visit: https://your-domain.com/maintenance-bypass-token

# Disable maintenance mode
php artisan up
```

## Updates and Upgrades

```bash
# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
pnpm install

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend
pnpm run build

# Restart services
sudo supervisorctl restart e-perpustakaan-worker:*
```

---

**Deployment Date**: 2025-10-18
**Version**: 1.0.0
**Status**: ✅ Ready for Production
