# Configuración de Nginx para CMS en Laragon

## Pasos para configurar:

### 1. Crear el archivo de configuración

Guarda este archivo como: `C:\laragon\etc\nginx\sites-enabled\cms.conf`

```nginx
server {
    listen 8080;
    server_name cms.test;
    
    root C:/laragon/www/cms;
    index index.php index.html;

    # Logs
    access_log C:/laragon/www/cms/logs/nginx_access.log;
    error_log C:/laragon/www/cms/logs/nginx_error.log;

    # Main location - Frontend routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Admin routes - IMPORTANT
    location /admin {
        try_files $uri $uri/ /admin/index.php?$query_string;
    }

    # PHP processing
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Deny access to sensitive files
    location ~* \.(sql|log)$ {
        deny all;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

### 2. Reiniciar Nginx en Laragon

- Abre Laragon
- Click derecho en Nginx
- Selecciona "Reload"

O desde terminal:
```bash
laragon nginx reload
```

### 3. Verificar que funciona

- Frontend: http://cms.test:8080/
- Admin: http://cms.test:8080/admin
- Productos: http://cms.test:8080/admin/products

## Solución alternativa si no funciona:

Si Laragon no carga el archivo de configuración personalizado, edita el archivo principal:

`C:\laragon\etc\nginx\laragon.conf`

Y agrega el bloque `server` completo dentro del archivo.
