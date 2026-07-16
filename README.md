# Setup Instructions - Custom PHP CMS

## Prerequisites

- PHP 7.4 or higher
- MySQL or MariaDB
- Apache or Nginx web server
- Composer

## Installation Steps

### 1. Install Composer Dependencies

```bash
cd c:/laragon/www/cms
composer install
```

This will install all PHP dependencies and set up autoloading.

### 2. Configure Database

Edit the database configuration in `config/config.php`:

```php
'database' => [
    'host' => 'localhost',        // Your database host
    'dbname' => 'cms_db',         // Your database name
    'username' => 'root',         // Your database username
    'password' => '',             // Your database password
    'charset' => 'utf8mb4'
],
```

### 3. Import Database Schema

Import the SQL schema into your MySQL database:

```bash
# Using MySQL command line
mysql -u root -p cms_db < config/database.sql

# Or using Laragon/phpMyAdmin
# - Open phpMyAdmin
# - Create database 'cms_db'
# - Import config/database.sql
```

### 4. Configure Site URL

Update the site URL in `config/config.php`:

```php
'site' => [
    'name' => 'My CMS',
    'url' => 'http://localhost/cms',        // Update this
    'admin_url' => 'http://localhost/cms/admin',
    'timezone' => 'America/New_York'
],
```

### 5. Set Permissions

Ensure the following directories are writable:

```bash
chmod 755 cache/
chmod 755 public/uploads/
```

### 6. Configure Web Server

#### Apache (already configured via .htaccess)

The `.htaccess` file is already included. Just ensure `mod_rewrite` is enabled:

```bash
# Enable mod_rewrite on Apache
a2enmod rewrite
service apache2 restart
```

#### Nginx Configuration (if using Nginx)

Add this to your Nginx site configuration:

```nginx
server {
    listen 80;
    server_name localhost;
    root /path/to/cms;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location /admin {
        try_files $uri $uri/ /admin/index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## Default Admin Credentials

**Email:** admin@cms.local  
**Password:** admin123

> ⚠️ **IMPORTANT**: Change these credentials after first login!

## Accessing the CMS

- **Frontend:** http://localhost/cms
- **Admin Panel:** http://localhost/cms/admin
- **Shop:** http://localhost/cms/shop
- **Cart:** http://localhost/cms/cart
- **Sitemap:** http://localhost/cms/sitemap.xml
- **Robots.txt:** http://localhost/cms/robots.txt

## Plugin Development

### Creating a Custom Plugin

1. Create a new directory in `plugins/`:

```bash
plugins/
└── my-plugin/
    ├── my-plugin.php       # Main plugin file
    ├── Controllers/        # Plugin controllers
    ├── Models/            # Plugin models
    └── views/             # Plugin views
```

2. Create the main plugin file `plugins/my-plugin/my-plugin.php`:

```php
<?php
use Core\PluginManager;

$pm = PluginManager::getInstance();
$router = $GLOBALS['app']->getRouter();

// Register routes
$router->get('/my-route', 'Plugins\MyPlugin\Controllers\MyController@index');

// Add hooks
$pm->addAction('hook_name', function($data) {
    // Your code here
    return $data;
});

// Add filters
$pm->addFilter('filter_name', function($value) {
    // Modify and return value
    return $value;
});
```

3. Enable your plugin in `config/config.php`:

```php
'plugins' => [
    'ecommerce',
    'seo',
    'my-plugin'  // Add your plugin here
],
```

### Available Hooks

- `header_meta` - Add content to HTML head
- `product_price` - Filter product prices
- Custom hooks can be added using `$pm->doAction('your_hook_name', $data)`

## Troubleshooting

### Database Connection Error

- Check database credentials in `config/config.php`
- Ensure MySQL service is running
- Verify database exists

### 404 on all pages

- Check that `.htaccess` exists in root directory
- Ensure `mod_rewrite` is enabled (Apache)
- Verify base URL in `config/config.php`

### Autoload errors

- Run `composer install` to generate autoload files
- Check namespace declarations match directory structure

## Next Steps

1. Log in to the admin panel
2. Add products in **Admin > Products**
3. Configure SEO settings in **Admin > SEO Settings**
4. Create custom pages and content
5. Customize the theme by editing view files

## Support

For questions or issues, review the code documentation or create custom plugins to extend functionality.
