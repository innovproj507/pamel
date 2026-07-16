# 🚀 Production Deployment Guide

## Pre-Deployment Checklist

- [ ] All code changes committed and tested locally
- [ ] Database migrations prepared (if any)
- [ ] Configuration files reviewed
- [ ] Backup current production files and database

## Deployment Steps

### 1. Upload Files

Upload all project files to `/var/www/html/pamel_edu_pa/` via FTP/SFTP:

```bash
# If using rsync (recommended)
rsync -avz --exclude 'vendor' --exclude '.git' --exclude 'cache/*' \
  /local/path/cms/ user@server:/var/www/html/pamel_edu_pa/
```

**Important:** Exclude these directories/files:
- `vendor/` (will be regenerated)
- `.git/`
- `cache/*`
- `logs/*`
- `composer.lock` (optional, regenerate on server)

### 2. Install Dependencies

SSH into the production server and run:

```bash
cd /var/www/html/pamel_edu_pa
composer install --no-dev --optimize-autoloader
```

**Flags explained:**
- `--no-dev`: Don't install development dependencies
- `--optimize-autoloader`: Creates optimized class maps for better performance

### 3. Set File Permissions

```bash
# Set ownership (adjust user/group as needed)
sudo chown -R www-data:www-data /var/www/html/pamel_edu_pa

# Set directory permissions
find /var/www/html/pamel_edu_pa -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/html/pamel_edu_pa -type f -exec chmod 644 {} \;

# Make specific directories writable
chmod -R 775 /var/www/html/pamel_edu_pa/cache
chmod -R 775 /var/www/html/pamel_edu_pa/logs
chmod -R 775 /var/www/html/pamel_edu_pa/public/uploads
```

### 4. Configure Environment

Update `config/config.php` for production:

```php
// Database Configuration
'database' => [
    'host' => 'localhost',
    'dbname' => 'production_db_name',
    'username' => 'production_db_user',
    'password' => 'SECURE_PASSWORD_HERE',
    'charset' => 'utf8mb4'
],

// Site Configuration
'site' => [
    'name' => 'Your Site Name',
    'url' => 'https://your-domain.com',
    'admin_url' => 'https://your-domain.com/admin',
    'timezone' => 'America/Panama'
],
```

**Security Note:** Never commit production credentials to version control!

### 5. Verify Nginx/Apache Configuration

#### For Nginx:

Check that `nginx.conf` is properly configured:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

#### For Apache:

Ensure `.htaccess` is being read:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 6. Clear Cache

```bash
cd /var/www/html/pamel_edu_pa
rm -rf cache/*
```

### 7. Test Critical Routes

Visit these URLs to verify deployment:

1. **Homepage:** `https://your-domain.com/`
2. **Login:** `https://your-domain.com/login`
3. **Admin:** `https://your-domain.com/admin`
4. **My Account:** `https://your-domain.com/my-account`

### 8. Check Error Logs

```bash
# Check application logs
tail -f /var/www/html/pamel_edu_pa/logs/nginx_error.log

# Check PHP error logs
tail -f /var/log/php/error.log

# Check Nginx error logs
tail -f /var/log/nginx/error.log
```

## Common Issues & Solutions

### Issue: "Invalid route callback"

**Cause:** Missing controller files or autoloader not optimized

**Solution:**
```bash
cd /var/www/html/pamel_edu_pa
composer dump-autoload --optimize
```

### Issue: "Class not found"

**Cause:** PSR-4 autoloading not configured properly

**Solution:**
1. Verify `composer.json` autoload section
2. Run `composer dump-autoload`
3. Check file permissions

### Issue: 500 Internal Server Error

**Cause:** PHP errors or missing dependencies

**Solution:**
1. Check error logs: `tail -f /var/log/nginx/error.log`
2. Enable error display temporarily in `Core/Application.php`:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
3. Check PHP version: `php -v` (requires >= 7.4)

### Issue: Database connection failed

**Cause:** Wrong credentials or database not accessible

**Solution:**
1. Verify credentials in `config/config.php`
2. Test database connection:
   ```bash
   mysql -u username -p database_name
   ```
3. Check if database exists and user has permissions

## Post-Deployment Verification

- [ ] Homepage loads correctly
- [ ] User login/registration works
- [ ] Admin panel accessible
- [ ] Product pages display properly
- [ ] Payment processing functional
- [ ] No errors in logs
- [ ] SSL certificate valid (HTTPS)
- [ ] Contact forms working
- [ ] File uploads working

## Rollback Procedure

If deployment fails:

```bash
# Restore from backup
cd /var/www/html
mv pamel_edu_pa pamel_edu_pa_failed
mv pamel_edu_pa_backup pamel_edu_pa

# Restore database (if changed)
mysql -u username -p database_name < backup.sql
```

## Performance Optimization (Optional)

### Enable OPcache

Edit `/etc/php/8.x/fpm/php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### Enable Gzip Compression

In `nginx.conf`:

```nginx
gzip on;
gzip_types text/plain text/css application/json application/javascript text/xml;
```

## Security Hardening

1. **Disable directory listing** (already in `.htaccess`)
2. **Hide sensitive files:**
   ```nginx
   location ~ /\. {
       deny all;
   }
   ```
3. **Set secure headers** (already in `.htaccess`)
4. **Use HTTPS only**
5. **Keep dependencies updated:**
   ```bash
   composer update --no-dev
   ```

## Monitoring

Set up monitoring for:
- Server uptime
- Error logs
- Disk space
- Database performance
- SSL certificate expiration

---

**Last Updated:** 2026-02-06
**Maintainer:** Development Team
