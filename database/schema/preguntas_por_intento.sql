-- ─────────────────────────────────────────────────────────────────────
-- Cuántas preguntas se sortean en cada intento
--
-- Permite tener un banco mayor que el examen: el quiz guarda 20 preguntas
-- y a cada estudiante se le presentan 10 al azar. Dos alumnos sentados
-- juntos no ven el mismo examen.
--
-- NULL o 0 significa "todas", que es como se comportaba hasta ahora, así
-- que ningún quiz existente cambia de conducta.
--
-- Es repetible: si la columna ya está, no hace nada.
-- ─────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

SET @hay := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME   = 'lms_quizzes'
       AND COLUMN_NAME  = 'questions_per_attempt'
);

SET @sql := IF(@hay = 0,
    'ALTER TABLE lms_quizzes
        ADD COLUMN questions_per_attempt SMALLINT UNSIGNED NULL DEFAULT NULL
            COMMENT "Preguntas a sortear por intento. NULL o 0 = todas."
            AFTER pass_percentage',
    'DO 0'
);
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

-- ── Comprobación ─────────────────────────────────────────────────────
SELECT 'columna questions_per_attempt' AS dato,
       IF(COUNT(*) = 1, 'existe', 'FALTA') AS estado
  FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE()
   AND TABLE_NAME   = 'lms_quizzes'
   AND COLUMN_NAME  = 'questions_per_attempt'
UNION ALL
SELECT 'quizzes con sorteo configurado', CAST(COUNT(*) AS CHAR)
  FROM lms_quizzes WHERE questions_per_attempt > 0;
