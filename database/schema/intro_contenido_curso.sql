-- ─────────────────────────────────────────────────────────────────────
-- Texto introductorio del contenido del curso
--
-- Los cursos de evaluación de competencia llevan una justificación que va
-- dentro del bloque de contenido, encima de las funciones y competencias,
-- no en la descripción general del curso.
--
-- No encajaba en ninguna columna existente: description es la descripción
-- del curso, learning_objectives son las competencias y requirements son
-- los requisitos. Se añade una propia, que sirve para cualquier curso que
-- necesite un texto antes de su temario.
--
-- Es repetible: si la columna ya está, no hace nada.
-- ─────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @hay := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME   = 'lms_courses'
       AND COLUMN_NAME  = 'content_intro'
);

SET @sql := IF(@hay = 0,
    'ALTER TABLE lms_courses
        ADD COLUMN content_intro TEXT NULL DEFAULT NULL
            COMMENT "Texto que se muestra encima del contenido del curso"
            AFTER description',
    'DO 0'
);
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

-- ── Comprobación ─────────────────────────────────────────────────────
SELECT 'columna content_intro' AS dato,
       IF(COUNT(*) = 1, 'existe', 'FALTA') AS estado
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME   = 'lms_courses'
   AND COLUMN_NAME  = 'content_intro';
