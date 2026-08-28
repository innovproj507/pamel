-- Rol Director Académico.
-- Permisos definidos en Core/Permissions.php: todo lo académico y de servicios,
-- sin configuración del sistema, comercio ni gestión de cuentas administrativas.

ALTER TABLE users
    MODIFY COLUMN role ENUM('admin','academic_director','teacher','student','editor')
    COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student';
