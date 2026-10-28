-- ─────────────────────────────────────────────────────────────────────
-- Varios cursos por solicitud de admisión
--
-- Hasta ahora una solicitud llevaba un único curso, en la columna
-- admission_requests.course, y una sola modalidad en course_mode. El
-- solicitante que quería cuatro cursos tenía que mandar cuatro
-- solicitudes y subir su pasaporte y su certificado médico cuatro veces:
-- esos dos requisitos se repiten en 64 y 63 cursos respectivamente.
--
-- La tabla nueva guarda un renglón por curso elegido, cada uno con su
-- propia modalidad: se puede estar renovando uno y estrenando otro.
--
-- Las columnas course y course_mode NO se tocan. Se siguen rellenando
-- con el resumen de la solicitud (los nombres unidos y la modalidad del
-- primer curso) para que las 101 solicitudes ya guardadas, el listado
-- del panel y las plantillas de Word sigan funcionando sin cambios.
--
-- Es repetible: si la tabla ya está, no hace nada.
-- ─────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admission_request_courses (
    id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    admission_request_id INT          NOT NULL,
    -- Puede quedar en NULL si el curso ya no existe como producto; el
    -- nombre se guarda aparte para que la solicitud siga siendo legible.
    product_id           INT          NULL DEFAULT NULL,
    course_name          VARCHAR(200) NOT NULL,
    mode                 ENUM('new','refresher') NOT NULL DEFAULT 'new',
    -- Orden en que los eligió, para pintarlos siempre igual.
    position             TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at           TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_arc_solicitud (admission_request_id),
    KEY idx_arc_producto  (product_id),
    CONSTRAINT fk_arc_solicitud
        FOREIGN KEY (admission_request_id) REFERENCES admission_requests (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Las solicitudes anteriores pasan a la tabla nueva ────────────────
-- Cada una tenía un curso; se copia tal cual para que la ficha del panel
-- las muestre igual que a las nuevas. NOT EXISTS lo hace repetible.
INSERT INTO admission_request_courses
       (admission_request_id, product_id, course_name, mode, position)
SELECT a.id,
       (SELECT p.id FROM products p WHERE p.name = a.course LIMIT 1),
       a.course,
       a.course_mode,
       0
  FROM admission_requests a
 WHERE a.course IS NOT NULL
   AND a.course <> ''
   AND NOT EXISTS (
       SELECT 1 FROM admission_request_courses c
        WHERE c.admission_request_id = a.id
   );

-- ── Un adjunto puede satisfacer a varios cursos ──────────────────────
-- Si el pasaporte vale para los cuatro cursos elegidos, se sube una vez
-- y se apunta aquí contra qué requisitos cuenta. La columna
-- requirement_id de admission_request_files se queda con el primero,
-- para no romper lo que ya la lee.
CREATE TABLE IF NOT EXISTS admission_request_file_requirements (
    id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    -- INT con signo, igual que admission_request_files.id: si no, MySQL
    -- rechaza la clave foránea por tipos incompatibles.
    file_id        INT          NOT NULL,
    requirement_id INT          NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_arfr (file_id, requirement_id),
    KEY idx_arfr_requisito (requirement_id),
    CONSTRAINT fk_arfr_archivo
        FOREIGN KEY (file_id) REFERENCES admission_request_files (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Comprobación ─────────────────────────────────────────────────────
-- Se cuenta directamente sobre las tablas nuevas, sin pasar por
-- information_schema: en los hostings compartidos el usuario de la base
-- no suele tener permiso sobre ella y la migración moría aquí, con el
-- trabajo ya hecho. Si una tabla no existiera, esta consulta avisaría
-- con un «Table doesn't exist», que es la señal que interesa.
SELECT 'cursos por solicitud'       AS tabla, COUNT(*) AS filas
  FROM admission_request_courses
UNION ALL
SELECT 'enlaces archivo-requisito',  COUNT(*)
  FROM admission_request_file_requirements
UNION ALL
SELECT 'solicitudes con curso',      COUNT(*)
  FROM admission_requests
 WHERE course IS NOT NULL AND course <> '';
