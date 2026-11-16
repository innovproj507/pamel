-- ─────────────────────────────────────────────────────────────────────
-- Orden de las lecciones repetido
--
-- 9 cursos (67 lecciones) importaron todas sus lecciones con order_num
-- = 32767 (el máximo del campo). Con el mismo valor en todas, "la
-- lección anterior" (order_num <) no existe nunca: el curso no exigía
-- completar los módulos en orden y las lecciones salían en cualquier
-- orden. Además la regla "un quiz cada 3 módulos" necesita una posición
-- única por lección.
--
-- Se renumera 1..n SOLO en los cursos que tienen valores repetidos,
-- conservando el orden actual y desempatando por id (en los datos
-- importados el id sigue la numeración del título: 1., 2., 3., …).
--
-- Es repetible: tras la primera vez ya no quedan valores repetidos y no
-- toca nada.
-- ─────────────────────────────────────────────────────────────────────

UPDATE lms_lessons l
JOIN (
    SELECT a.id,
           (SELECT COUNT(*) FROM lms_lessons b
             WHERE b.course_id = a.course_id
               AND (b.order_num < a.order_num
                    OR (b.order_num = a.order_num AND b.id <= a.id))) AS pos
    FROM lms_lessons a
    WHERE a.course_id IN (
        SELECT course_id FROM (
            SELECT course_id FROM lms_lessons
            GROUP BY course_id, order_num
            HAVING COUNT(*) > 1
        ) repetidos
    )
) r ON r.id = l.id
SET l.order_num = r.pos;
