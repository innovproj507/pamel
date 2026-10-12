-- Requisitos de admisión por curso.
--
-- Origen: la carpeta Requisitos/ (documentos Word de PAMEL). Cada curso tiene
-- dos listas posibles: la del curso regular (R-C) y la del refresco (R-RF).
--
-- ID/Pasaporte y Certificado de salud siguen siendo campos fijos de
-- admission_requests, porque aparecen en prácticamente todos los cursos;
-- aquí se guardan los requisitos propios de cada curso.

-- ── Requisitos de cada curso ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS course_requirements (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    product_id    INT NOT NULL,
    mode          ENUM('new','refresher') NOT NULL DEFAULT 'new',
    label         TEXT NOT NULL,
    requires_file TINYINT(1) NOT NULL DEFAULT 0,
    order_num     SMALLINT NOT NULL DEFAULT 0,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_product_mode (product_id, mode, order_num),
    CONSTRAINT fk_req_product FOREIGN KEY (product_id)
        REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Documentos que adjunta el solicitante por cada requisito ────────────
CREATE TABLE IF NOT EXISTS admission_request_files (
    id                   INT AUTO_INCREMENT PRIMARY KEY,
    admission_request_id INT NOT NULL,
    requirement_id       INT NULL,
    label                VARCHAR(500) NOT NULL,  -- copia del texto del requisito
    file_path            VARCHAR(500) NOT NULL,
    original_name        VARCHAR(255) NULL,
    size_bytes           INT UNSIGNED NULL,
    uploaded_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_request (admission_request_id),
    CONSTRAINT fk_arf_request FOREIGN KEY (admission_request_id)
        REFERENCES admission_requests(id) ON DELETE CASCADE,
    CONSTRAINT fk_arf_requirement FOREIGN KEY (requirement_id)
        REFERENCES course_requirements(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Importación de los Word: zona de revisión manual ────────────────────
CREATE TABLE IF NOT EXISTS requirement_imports (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    folder       VARCHAR(16) NOT NULL,       -- R-C, R-RF, R-AS
    file_name    VARCHAR(255) NOT NULL,
    doc_title    VARCHAR(500) NOT NULL,
    requirements TEXT NOT NULL,              -- JSON con la lista extraída
    product_id   INT NULL,                   -- curso asignado
    status       ENUM('pending','mapped','ignored') NOT NULL DEFAULT 'pending',
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_doc (folder, file_name),
    KEY idx_status (status),
    CONSTRAINT fk_ri_product FOREIGN KEY (product_id)
        REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── La solicitud registra si es curso nuevo o refresco ──────────────────
ALTER TABLE admission_requests
    ADD COLUMN course_mode ENUM('new','refresher') NOT NULL DEFAULT 'new' AFTER course;
