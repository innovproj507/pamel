# 🔧 Checklist de Verificación en Producción

## 1️⃣ Verificar que los archivos se subieron correctamente

```bash
# SSH al servidor
ssh usuario@tu-servidor.com

cd /var/www/html/pamel_edu_pa

# Verificar index.php tiene las rutas de admin
grep -A 3 "Load admin routes" index.php

# Verificar composer.json tiene los mappings
grep -A 5 "Ecommerce" composer.json

# Verificar fecha de modificación
ls -la index.php composer.json
```

## 2️⃣ Regenerar autoloader

```bash
cd /var/www/html/pamel_edu_pa
composer dump-autoload --optimize
```

Deberías ver:
```
Generating optimized autoload files
Generated optimized autoload files containing 219 classes
```

## 3️⃣ Verificar usuario admin existe

```bash
# Conectar a MySQL
mysql -u root -p

# Usar la base de datos
USE nombre_de_tu_base_de_datos;

# Verificar usuario admin
SELECT id, email, name, role, status FROM users WHERE role = 'admin';
```

Si NO aparece ningún usuario admin, ejecuta:

```sql
INSERT INTO users (email, password, name, role, status) VALUES
('admin@cms.local', '$2y$10$u/m1GoV54tso1GNlORyPm.VYjs1IY4xm8141tenMESe5QIVj9VIXK', 'Administrador', 'admin', 'active');
```

## 4️⃣ Verificar permisos de archivos

```bash
cd /var/www/html/pamel_edu_pa

# Verificar permisos
ls -la index.php
ls -la composer.json
ls -la Core/Auth.php

# Si es necesario, ajustar permisos
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
```

## 5️⃣ Verificar logs de error

```bash
# Ver logs de Nginx
tail -f /var/log/nginx/error.log

# Ver logs de PHP (si existen)
tail -f /var/www/html/pamel_edu_pa/logs/nginx_error.log

# O logs del sistema
tail -f /var/log/php-fpm/error.log
```

## 6️⃣ Limpiar caché (si existe)

```bash
cd /var/www/html/pamel_edu_pa

# Limpiar cache
rm -rf cache/*

# Reiniciar PHP-FPM (si aplica)
sudo systemctl restart php-fpm
# O
sudo systemctl restart php8.1-fpm
```

## 7️⃣ Verificar rutas en el navegador

Después de hacer lo anterior, prueba:

1. ✅ `http://tu-dominio.com/admin/login` - Debe cargar la página de login
2. ✅ `http://tu-dominio.com/shop` - Debe cargar la tienda
3. ✅ `http://tu-dominio.com/admin` - Debe redirigir a login

---

## ❓ ¿Qué error específico ves en producción?

- [ ] Error 404 en `/admin/login`
- [ ] Error "Controller class not found"
- [ ] Página de login carga pero credenciales no funcionan
- [ ] Error 500
- [ ] Otro: _________________

---

## 🆘 Si sigue sin funcionar

Ejecuta este comando para ver exactamente qué está pasando:

```bash
cd /var/www/html/pamel_edu_pa
php -r "require 'vendor/autoload.php'; var_dump(class_exists('Plugins\\Ecommerce\\Controllers\\ProductController'));"
```

Debe mostrar: `bool(true)`

Si muestra `bool(false)`, entonces el autoloader no se regeneró correctamente.
