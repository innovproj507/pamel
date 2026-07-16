# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Custom PHP CMS built for an educational/training institution (PAMEL). Monolithic, plugin-based architecture with MVC structure, e-commerce, multi-language (EN/ES), PayPal payments, and Word document generation.

- **PHP 7.4+**, MySQL/MariaDB, Apache or Nginx
- **Local dev URL:** http://cms.test:8080 (configured in `config/config.php`)
- **Admin panel:** http://cms.test:8080/admin/manager — default credentials: `admin@cms.local` / `admin123`
  - Entry point: `admin/index.php`; all admin routes are prefixed with `/manager/` in `routes/admin.php`

## Common Commands

```bash
# Install dependencies (first time or after composer.json changes)
composer install

# Regenerate autoloader after adding new classes/namespaces
composer dump-autoload --optimize

# Production install
composer install --no-dev --optimize-autoloader

# Import database schema (actual DB name is cms_db2)
mysql -u root -p cms_db2 < database/schema/database.sql

# Utility scripts (run from project root via CLI, not web)
php scripts/list_users.php
php scripts/check_user.php
php scripts/reset_pass.php admin@cms.local newpassword
php scripts/seed_test_users.php
```

There is no test suite, build pipeline, or linter configured.

## Architecture

### Entry Points

The document root is `public/`. Apache rewrites all unmatched requests to `public/index.php` (see `public/.htaccess`).

- `public/index.php` — primary entry point; detects LMS subdomain (`elearning.*`), loads the appropriate route set, and runs the app
- `admin/index.php` — secondary entry point used when hitting the admin directly; loads `routes/web.php` + `routes/admin.php` only (no API, no LMS subdomain routing)

Both bootstrap the same `Core\Application`. Do not add admin-only routes to `web.php` or vice versa.

### Request Lifecycle

`public/index.php` → `Core\Application` (loads config, plugins, language) → `Core\Router` dispatches to controller or closure → `Core\View` renders template with layout.

Routes are closures or `ControllerClass@method` strings defined in `routes/web.php` (frontend), `routes/admin.php` (admin), and `routes/api.php` (REST API under `/api/v1/`). When the host matches `elearning.*`, only `routes/lms.php` is loaded instead. Route parameters use `:param` syntax, converted to regex capture groups internally. Named routes are supported via an optional third argument: `$router->get('/shop/:slug', 'Controller@method', 'shop.show')` — generate URLs with `$router->url('shop.show', ['slug' => 'foo'])`.

`public/index.php` also handles CORS for `/api/` requests, allowlisting the `elearning.*` origins (production + `.test` dev) so the LMS subdomain can call the main site's API with credentials.

### Namespace Map (PSR-4, via composer.json)

| Namespace | Directory |
|-----------|-----------|
| `Core\` | `Core/` |
| `Controllers\` | `Controllers/` |
| `Admin\` | `admin/` |
| `Plugins\` | `plugins/` |
| `Plugins\Ecommerce\` | `plugins/ecommerce/` |
| `Plugins\Paypal\` | `plugins/paypal/` |
| `Plugins\Seo\` | `plugins/seo/` |
| `Plugins\Maintenance\` | `plugins/maintenance/` |
| `Plugins\Elearning\` | `plugins/elearning/` |

Windows dev filesystems are case-insensitive; Linux production is not. If a new plugin/controller namespace's casing doesn't exactly match its directory name (e.g. `Plugins\Foo\` vs `plugins/foo/`), it will resolve locally but throw class-not-found only on the Linux server. Always add an explicit lowercase PSR-4 mapping in `composer.json` for new plugins, matching the pattern above, and run `composer dump-autoload --optimize` after.

### Key Singletons

- `Core\Database::getInstance()` — PDO wrapper for MySQL
- `Core\PluginManager::getInstance()` — hook/filter system (WordPress-style `addAction`/`addFilter`/`doAction`/`applyFilter`)
- `Core\Config::get('dot.path')` — configuration access
- `Core\Auth::getInstance()` — session-based authentication

### Views

`Core\View::render($template, $data, $layout)` — `$template` and `$layout` are paths relative to the CMS root, without `.php` extension. Frontend templates live in `public/views/`, admin templates in `admin/views/`.

`render()` calls `extract($data)`, so all keys in `$data` become local variables inside the template (e.g. pass `['user' => $user]` → template gets `$user`).

### Plugin System

Plugins are loaded from `plugins/<name>/<name>.php` when listed in `config/config.php` under `'plugins'`. Each plugin file registers its own routes (via `$GLOBALS['app']->getRouter()`) and hooks at load time.

Available built-in hooks: `header_meta`, `product_price`, `account_menu_links`. Custom hooks use `$pm->doAction('hook_name', $data)`.

The maintenance plugin checks for `cache/maintenance.flag` (JSON file) at boot time and short-circuits non-admin requests with a 503 page when it exists.

The **elearning plugin** (`plugins/elearning/`) is the largest plugin — it adds a full LMS with courses, lessons, quizzes, certificates, and enrollments. Its routes are registered in both `routes/web.php` (public course pages) and `routes/admin.php` (admin LMS management under `/manager/lms/`). An optional `routes/lms.php` handles LMS subdomain routing when the `IS_LMS` constant is defined (set when the request host matches `elearning.*`).

### Database

Direct PDO queries through `Core\Database`. No ORM.

Key methods:
- `$db->fetchAll($sql, $params)` — returns array of rows
- `$db->fetch($sql, $params)` — alias for `fetchOne()`, returns single row or `null`
- `$db->execute($sql, $params)` — run INSERT/UPDATE/DELETE, returns `PDOStatement|false`
- `$db->insert($table, $data)` — returns last insert ID (int) or `false`
- `$db->update($table, $data, $where, $whereParams)` — returns bool
- `$db->delete($table, $where, $params)` — returns bool

Models in `Core/Models/` and `plugins/ecommerce/Models/` are thin wrappers around raw SQL inheriting from `Core\Model` (which provides `all()`, `find($id)`, `where($col, $val)`, `create()`, `update()`, `delete()`).

### Authentication

`Core\Auth` singleton. Key methods:

- `Auth::getInstance()->check()` — is a user logged in?
- `Auth::getInstance()->user()` — current user array from `users` table
- `Auth::getInstance()->isAdmin()` — checks `role === 'admin'`
- `Auth::getInstance()->requireAuth()` — redirects to `/admin/login` if not authenticated
- `Auth::getInstance()->requireAdmin()` — redirects if not admin role
- `Auth::hashPassword($password)` — static, uses `PASSWORD_DEFAULT`

Session keys set on login: `$_SESSION['user_id']`, `$_SESSION['user_role']`.

### Base Controller

All controllers extend `Core\Controller`, which injects `$this->db` (Database) and `$this->view` (View) and provides:

- `$this->json($data, $status)` — sends JSON response and exits
- `$this->redirect($url)` — sends Location header and exits
- `$this->back()` — redirects to `HTTP_REFERER` or `/`

### Security

`Core\Security` provides static utility methods:

- `Security::generateCsrfToken()` / `Security::validateCsrfToken($token)` — session-based CSRF
- `Security::getCsrfField()` — renders `<input type="hidden">` for forms
- `Security::checkRateLimit($action, $maxAttempts, $timeWindow)` — session-based rate limiting
- `Security::sanitizeString($string)` / `Security::escape($string)` — output escaping

### Language / i18n

`Core\Language` loads from `config/lang/en.php` or `config/lang/es.php`. Active locale is determined by `?lang=` URL param → session → config default. Global helper `__('key.path')` wraps `Language::get()` with optional `:placeholder` substitution.

### Email & Word Documents

`Core\Email::send($to, $subject, $body, $isHtml, $attachment)` — uses PHPMailer with SMTP when enabled; falls back to PHP `mail()`. Merges `email_*` keys from the `settings` DB table over values in `config/config.php`, so SMTP credentials configured via the admin panel take precedence. Supports file attachments.

`Core\Email::sendSurveyNotification($data)` — generates a `.docx` via `Core\WordGenerator` (wraps `phpoffice/phpword`), attaches it to an admin email, then deletes the temp file. The same pattern is used for admission request Word exports.

### Configuration

All runtime configuration is in `config/config.php`. Access via `Config::get('section.key', $default)`. PayPal credentials are in `config/paypal.php`. Translations in `config/lang/en.php` and `config/lang/es.php`.

`config/config.php` reads from `$_ENV` (with hardcoded fallbacks), populated by a custom `Core\Env::load()` (not `vlucas/phpdotenv`) that parses `.env` at the project root on every `Application` boot. Copy `.env.example` to `.env` for local overrides (DB credentials, site URL, SMTP, `APP_SECRET`, `APP_SYNC_KEY`). `.env` is gitignored.

`Application::__construct()` also shares the session cookie across subdomains (`.{site.url host}`) so login persists between the main site and `elearning.*` — but only when `site.url` resolves to a real domain; it's skipped for `localhost`/`.test`/`.local`/`.dev` to avoid cookie issues in local dev.

### Writable Directories

`cache/` and `public/uploads/` must be writable by the web server (chmod 755). `public/uploads/admissions/` stores uploaded ID and health certificate files from the admission form.

## Adding Features

- **New frontend route:** Add to `routes/web.php`
- **New admin route:** Add to `routes/admin.php`
- **New API route:** Add to `routes/api.php` (loaded only by `public/index.php`, not `admin/index.php`)
- **New plugin:** Create `plugins/<name>/<name>.php`, register it in `config/config.php` under `'plugins'`, and add a namespace entry to `composer.json` if needed
- **New controller namespace:** Add PSR-4 entry to `composer.json` autoload, then run `composer dump-autoload`
- **Plugin accessing router:** Use `$GLOBALS['app']->getRouter()` inside the plugin file (called at load time, before dispatch)
- **Email settings:** Stored in the `settings` table with `email_*` keys; configure via admin panel at `/manager/email-settings` — DB values override `config/config.php`
