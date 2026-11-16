-- ─────────────────────────────────────────────────────────────────────
-- Chat interno del portal: alumno ↔ instructor/administración
--
-- Una conversación la abre un alumno, sobre un curso suyo o como consulta
-- general (course_id NULL). La responde el personal que puede verla:
--   · administrador y director académico: todas;
--   · instructor: las de los cursos que imparte.
--
-- Lectura: en vez de una fila por mensaje y lector, la conversación guarda
-- hasta qué mensaje ha leído cada parte (student_read_until y
-- staff_read_until). "Sin leer" = mensajes de la otra parte con id mayor.
--
-- Es repetible: si las tablas ya están, no hace nada.
-- ─────────────────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS chat_conversations (
    id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    student_id         INT UNSIGNED NOT NULL,
    course_id          INT UNSIGNED NULL,
    subject            VARCHAR(150) NOT NULL,
    created_at         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_message_at    DATETIME     NULL,
    student_read_until INT UNSIGNED NOT NULL DEFAULT 0,
    staff_read_until   INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_chat_student (student_id, last_message_at),
    KEY idx_chat_course (course_id, last_message_at),
    CONSTRAINT fk_chat_student FOREIGN KEY (student_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_chat_course  FOREIGN KEY (course_id)  REFERENCES lms_courses (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chat_messages (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    conversation_id INT UNSIGNED NOT NULL,
    sender_id       INT UNSIGNED NOT NULL,
    body            TEXT         NOT NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_msg_conv (conversation_id, id),
    KEY idx_msg_sender (sender_id, created_at),
    CONSTRAINT fk_msg_conv   FOREIGN KEY (conversation_id) REFERENCES chat_conversations (id) ON DELETE CASCADE,
    CONSTRAINT fk_msg_sender FOREIGN KEY (sender_id)       REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
