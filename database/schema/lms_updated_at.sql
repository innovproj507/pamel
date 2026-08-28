-- Añade updated_at a las tablas LMS cuyos controladores ya escriben esa columna.
-- Sin ella, UPDATE falla con "Unknown column 'updated_at'" y Database::query()
-- se traga la excepción, por lo que el guardado fallaba en silencio.

ALTER TABLE lms_quizzes
    ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

ALTER TABLE lms_lessons
    ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;
