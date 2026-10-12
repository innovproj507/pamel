-- ─────────────────────────────────────────────────────────────────────
-- Nivel educativo en las solicitudes de admisión
--
-- El formulario F-41, el de los cursos de logística y puertos, pide el
-- nivel educativo del solicitante, y el formulario web no lo recogía: el
-- documento salía con ese recuadro en blanco.
--
-- País de nacimiento no necesita columna nueva: ya existía, aunque el
-- controlador la guardaba siempre vacía. Eso se corrige en el código.
--
-- Es repetible: si la columna ya está, no hace nada.
-- ─────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @hay := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME   = 'admission_requests'
       AND COLUMN_NAME  = 'education_level'
);

SET @sql := IF(@hay = 0,
    'ALTER TABLE admission_requests
        ADD COLUMN education_level VARCHAR(60) NULL DEFAULT NULL AFTER country_of_birth',
    'DO 0'
);

PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

-- ── Comprobación ─────────────────────────────────────────────────────
SELECT 'columna education_level' AS dato,
       IF(COUNT(*) = 1, 'existe', 'FALTA') AS estado
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME   = 'admission_requests'
   AND COLUMN_NAME  = 'education_level'
UNION ALL
SELECT 'columna country_of_birth',
       IF(COUNT(*) = 1, 'existe', 'FALTA')
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME   = 'admission_requests'
   AND COLUMN_NAME  = 'country_of_birth';
