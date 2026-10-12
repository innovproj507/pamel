-- ─────────────────────────────────────────────────────────────────────
-- Catálogo de reglas de requisitos
--
-- Hasta ahora los requisitos de cada curso se escribían uno a uno, y sólo
-- podían entrar importando un documento Word. Resultado: 271 requisitos
-- publicados con apenas 37 textos distintos (86% repetidos), y 34 de los
-- 65 cursos activos sin ningún requisito, porque nadie escribió su Word.
--
-- Este catálogo guarda cada texto una vez. En la ficha del curso se marcan
-- las reglas que le aplican y se copian a course_requirements, que sigue
-- siendo la tabla que lee el formulario de admisión: nada de lo que ya
-- funciona cambia de sitio.
--
-- La copia es deliberada. Corregir una regla no reescribe a espaldas de
-- nadie los requisitos ya publicados: la ficha avisa de qué cursos se
-- quedaron con la versión antigua y se actualizan cuando se decida.
--
-- Es repetible: puede ejecutarse más de una vez sin duplicar nada.
-- ─────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

START TRANSACTION;

-- ═══ 1. El catálogo ══════════════════════════════════════════════════
CREATE TABLE IF NOT EXISTS requirement_rules (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    label         TEXT NOT NULL,
    -- Si el solicitante debe adjuntar un documento o es sólo informativo
    requires_file TINYINT(1) NOT NULL DEFAULT 1,
    -- Modalidad a la que se ofrece: 'any' aparece en las dos
    scope         ENUM('any','new','refresher') NOT NULL DEFAULT 'any',
    -- Se marca sola al crear un curso nuevo (pasaporte, certificado médico…)
    is_default    TINYINT(1) NOT NULL DEFAULT 0,
    order_num     SMALLINT NOT NULL DEFAULT 0,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    -- El texto identifica la regla: evita que se siembre dos veces
    UNIQUE KEY uq_label (label(255)),
    KEY idx_orden (order_num, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══ 2. De qué regla salió cada requisito publicado ══════════════════
-- Permite saber qué cursos usan una versión vieja del texto y volver a
-- marcar las casillas correctas al abrir la ficha de un curso.
-- NULL = línea suelta, escrita a mano para ese curso.
SET @hay := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'course_requirements'
      AND COLUMN_NAME  = 'rule_id'
);
SET @sql := IF(@hay = 0,
    'ALTER TABLE course_requirements
        ADD COLUMN rule_id INT UNSIGNED NULL DEFAULT NULL AFTER mode,
        ADD KEY idx_rule (rule_id)',
    'DO 0'
);
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

-- ═══ 3. Sembrar el catálogo con lo que ya está publicado ═════════════
-- Cada texto distinto pasa a ser una regla. requires_file toma el valor
-- mayoritario; scope queda en 'any' salvo que el texto sólo se haya usado
-- en una de las dos modalidades.
INSERT INTO requirement_rules (label, requires_file, scope, is_default, order_num)
SELECT
    t.label,
    t.pide_doc,
    CASE WHEN t.modos = 1 THEN t.unico_modo ELSE 'any' END,
    -- Las que están en todos los cursos se marcan solas en uno nuevo
    IF(t.cursos >= (SELECT COUNT(DISTINCT product_id) FROM course_requirements), 1, 0),
    t.orden
FROM (
    SELECT
        label,
        MAX(requires_file)              AS pide_doc,
        COUNT(DISTINCT mode)            AS modos,
        MIN(mode)                       AS unico_modo,
        COUNT(DISTINCT product_id)      AS cursos,
        MIN(order_num)                  AS orden
    FROM course_requirements
    GROUP BY label
) AS t
ON DUPLICATE KEY UPDATE
    -- Si ya existía, no se pisa lo que alguien haya ajustado a mano
    requirement_rules.label = requirement_rules.label;

-- ═══ 4. Enlazar lo ya publicado con su regla ═════════════════════════
UPDATE course_requirements cr
  JOIN requirement_rules rr
    ON rr.label COLLATE utf8mb4_unicode_ci = cr.label COLLATE utf8mb4_unicode_ci
   SET cr.rule_id = rr.id
 WHERE cr.rule_id IS NULL;

COMMIT;

-- ── Comprobación ─────────────────────────────────────────────────────
SELECT 'reglas en el catálogo'        AS dato, COUNT(*) AS n FROM requirement_rules
UNION ALL SELECT 'marcadas por defecto',       COUNT(*) FROM requirement_rules WHERE is_default = 1
UNION ALL SELECT 'requisitos publicados',      COUNT(*) FROM course_requirements
UNION ALL SELECT 'ya enlazados a una regla',   COUNT(*) FROM course_requirements WHERE rule_id IS NOT NULL
UNION ALL SELECT 'sin enlazar (debe ser 0)',   COUNT(*) FROM course_requirements WHERE rule_id IS NULL;
