-- ─────────────────────────────────────────────────────────────────────
-- Requisitos mínimos para los cursos que no tenían ninguno
--
-- El formulario de admisión pedía la identificación y el certificado
-- médico en dos campos fijos, visibles siempre, antes incluso de elegir
-- curso. Esos campos desaparecen: ahora los documentos salen de los
-- requisitos del curso elegido.
--
-- El problema es que 35 cursos no tenían requisitos para "primera vez" y
-- 39 para "renovación": al quitar los campos fijos no pedirían ningún
-- documento. Este script les aplica las reglas de uso general del
-- catálogo (identificación, estructura del programa, certificado médico),
-- que es lo que todos los cursos exigen de todas formas.
--
-- Sólo toca las combinaciones curso+modalidad que están vacías. Un curso
-- con requisitos propios no se altera.
--
-- Requiere database/schema/requirement_rules.sql aplicado antes.
-- Es repetible: ejecutarlo de nuevo no duplica nada.
-- ─────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

START TRANSACTION;

INSERT INTO course_requirements (product_id, mode, rule_id, label, requires_file, order_num)
SELECT
    p.id,
    m.mode,
    r.id,
    r.label,
    r.requires_file,
    r.order_num
FROM products p
CROSS JOIN (SELECT 'new' AS mode UNION ALL SELECT 'refresher' AS mode) m
CROSS JOIN requirement_rules r
WHERE p.status = 'active'
  AND r.is_default = 1
  AND r.scope IN ('any', m.mode)
  -- Sólo si esa combinación curso+modalidad está vacía
  AND NOT EXISTS (
        SELECT 1 FROM course_requirements c
         WHERE c.product_id = p.id
           AND c.mode       = m.mode
  );

COMMIT;

-- ── Comprobación ─────────────────────────────────────────────────────
SELECT 'cursos activos' AS dato, COUNT(*) AS n FROM products WHERE status = 'active'
UNION ALL
SELECT 'sin requisitos en primera vez (debe ser 0)', COUNT(*)
  FROM products p WHERE p.status = 'active'
   AND NOT EXISTS (SELECT 1 FROM course_requirements c
                    WHERE c.product_id = p.id AND c.mode = 'new')
UNION ALL
SELECT 'sin requisitos en renovación (debe ser 0)', COUNT(*)
  FROM products p WHERE p.status = 'active'
   AND NOT EXISTS (SELECT 1 FROM course_requirements c
                    WHERE c.product_id = p.id AND c.mode = 'refresher')
UNION ALL
SELECT 'requisitos publicados en total', COUNT(*) FROM course_requirements;
