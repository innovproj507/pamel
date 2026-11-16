-- ─────────────────────────────────────────────────────────────────────
-- Verificación del instructor sobre un examen
--
-- El examen en Word lleva la leyenda «Este examen ha sido verificado y
-- cuenta con la conformidad del instructor». La casilla sale marcada solo
-- cuando alguien con permiso lo ha verificado en el seguimiento: aquí se
-- guarda quién y cuándo.
--
-- Es repetible: añade cada columna solo si no existe.
-- ─────────────────────────────────────────────────────────────────────

SET @hay := (SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lms_quiz_attempts'
                AND COLUMN_NAME = 'verified_by');
SET @sql := IF(@hay = 0,
    'ALTER TABLE lms_quiz_attempts ADD COLUMN verified_by INT UNSIGNED NULL DEFAULT NULL',
    'DO 0');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

SET @hay := (SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'lms_quiz_attempts'
                AND COLUMN_NAME = 'verified_at');
SET @sql := IF(@hay = 0,
    'ALTER TABLE lms_quiz_attempts ADD COLUMN verified_at DATETIME NULL DEFAULT NULL',
    'DO 0');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;
