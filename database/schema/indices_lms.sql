-- ─────────────────────────────────────────────────────────────────────
-- Índices que le faltaban al LMS
--
-- Ninguna de estas columnas de relación estaba indexada: sólo existían
-- las claves primarias. La consecuencia medida en la página del quiz:
-- para armar un quiz de 60 preguntas se hacía una consulta por pregunta
-- sobre lms_question_options (16.794 filas) sin índice, es decir cerca de
-- un millón de lecturas de fila. El endpoint tardaba entre 6 y 12
-- segundos, el portal se cansaba de esperar y el estudiante recibía un
-- 404: ningún quiz se podía abrir.
--
-- Son índices, no cambian datos ni estructura lógica. En las tablas más
-- grandes la creación tarda unos segundos.
--
-- Es repetible: comprueba antes de crear cada uno.
-- ─────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crea el índice sólo si no existe ya.
DROP PROCEDURE IF EXISTS crear_indice_si_falta;
DELIMITER //
CREATE PROCEDURE crear_indice_si_falta(
    IN p_tabla  VARCHAR(64),
    IN p_col    VARCHAR(64),
    IN p_nombre VARCHAR(64)
)
BEGIN
    DECLARE v_hay INT DEFAULT 0;

    SELECT COUNT(*) INTO v_hay
      FROM information_schema.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME   = p_tabla
       AND COLUMN_NAME  = p_col;

    IF v_hay = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', p_tabla, '` ADD INDEX `', p_nombre, '` (`', p_col, '`)');
        PREPARE st FROM @sql;
        EXECUTE st;
        DEALLOCATE PREPARE st;
    END IF;
END //
DELIMITER ;

-- ── Los dos que rompían el quiz ──────────────────────────────────────
CALL crear_indice_si_falta('lms_questions',        'quiz_id',     'idx_quiz');
CALL crear_indice_si_falta('lms_question_options', 'question_id', 'idx_question');

-- ── El resto de relaciones sin indexar ───────────────────────────────
CALL crear_indice_si_falta('lms_quizzes',          'course_id',   'idx_course');
CALL crear_indice_si_falta('lms_quizzes',          'lesson_id',   'idx_lesson');
CALL crear_indice_si_falta('lms_lesson_progress',  'student_id',  'idx_student');
CALL crear_indice_si_falta('lms_lesson_progress',  'lesson_id',   'idx_lesson');
CALL crear_indice_si_falta('lms_courses',          'category_id', 'idx_category');
CALL crear_indice_si_falta('lms_courses',          'teacher_id',  'idx_teacher');

DROP PROCEDURE IF EXISTS crear_indice_si_falta;

-- ── Comprobación: debe decir 0 ───────────────────────────────────────
SELECT 'relaciones sin índice (debe ser 0)' AS dato, COUNT(*) AS n
  FROM (
      SELECT 'lms_questions' t, 'quiz_id' c
      UNION ALL SELECT 'lms_question_options', 'question_id'
      UNION ALL SELECT 'lms_quizzes',          'course_id'
      UNION ALL SELECT 'lms_quizzes',          'lesson_id'
      UNION ALL SELECT 'lms_lesson_progress',  'student_id'
      UNION ALL SELECT 'lms_lesson_progress',  'lesson_id'
      UNION ALL SELECT 'lms_courses',          'category_id'
      UNION ALL SELECT 'lms_courses',          'teacher_id'
  ) AS esperado
 WHERE NOT EXISTS (
     SELECT 1 FROM information_schema.STATISTICS s
      WHERE s.TABLE_SCHEMA = DATABASE()
        AND s.TABLE_NAME   = esperado.t
        AND s.COLUMN_NAME  = esperado.c
 );
