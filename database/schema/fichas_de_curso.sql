-- ─────────────────────────────────────────────────────────────────────
-- Ficha única de curso, con una oferta por país
--
-- Un mismo curso (Ship Security Officer, Elementary First Aid…) se daba
-- de alta una vez por país, como dos productos casi idénticos: uno de
-- Panamá y otro de India. Corregir un texto obligaba a hacerlo en cada
-- copia.
--
-- Ahora el curso vive una vez en `courses` (nombre, código, categoría,
-- descripción común) y cada fila de `products` pasa a ser la OFERTA de
-- ese curso en un país: conserva su sucursal, precio, horas, modalidad,
-- estado, slug y requisitos de admisión, que SÍ cambian por país (en 12
-- de las 18 parejas actuales los requisitos no coinciden).
-- La tienda, el carrito, los pedidos y el LMS siguen leyendo `products`
-- sin enterarse del cambio.
--
-- Descripción: la de la ficha es la común. Una oferta puede tener la
-- suya (description_custom = 1) y entonces no se pisa al editar la
-- ficha. En las parejas actuales las dos descripciones ya eran
-- distintas, así que la ficha toma la de la oferta más antigua y la
-- otra queda marcada como propia: no se pierde ningún texto.
--
-- Productos con el mismo nombre (ignorando espacios sobrantes) se unen
-- en una ficha. Nombres parecidos pero no iguales, como «Skils» y
-- «Skills», NO se unen: eso es una errata que se corrige a mano.
--
-- Es repetible: sólo procesa los productos que aún no tienen ficha.
-- ─────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS courses (
    id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name                VARCHAR(255) NOT NULL,
    course_code         VARCHAR(50)  NULL DEFAULT NULL,
    imo_model_course_no VARCHAR(20)  NULL DEFAULT NULL,
    category_id         INT          NULL DEFAULT NULL,
    description         TEXT         NULL,
    image               VARCHAR(255) NULL DEFAULT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_courses_name (name),
    KEY idx_courses_code (course_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Columnas nuevas en products ──────────────────────────────────────
SET @hay := (SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'
                AND COLUMN_NAME = 'course_id');
SET @sql := IF(@hay = 0,
    'ALTER TABLE products ADD COLUMN course_id INT UNSIGNED NULL DEFAULT NULL AFTER branch_id,
        ADD KEY idx_products_course (course_id)',
    'DO 0');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

SET @hay := (SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'
                AND COLUMN_NAME = 'description_custom');
SET @sql := IF(@hay = 0,
    'ALTER TABLE products ADD COLUMN description_custom TINYINT(1) NOT NULL DEFAULT 0 AFTER description',
    'DO 0');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

-- ── Una ficha por nombre ─────────────────────────────────────────────
-- Los datos comunes salen de la oferta más antigua de cada nombre.
INSERT INTO courses (name, course_code, imo_model_course_no, category_id, description, image)
SELECT TRIM(p.name), p.course_code, p.imo_model_course_no, p.category_id,
       p.description, p.image
  FROM products p
  JOIN (SELECT TRIM(name) AS n, MIN(id) AS primero
          FROM products
         WHERE course_id IS NULL
         GROUP BY TRIM(name)) m ON m.primero = p.id
 WHERE NOT EXISTS (SELECT 1 FROM courses c WHERE c.name = TRIM(p.name));

UPDATE products p
  JOIN courses c ON c.name = TRIM(p.name)
   SET p.course_id = c.id
 WHERE p.course_id IS NULL;

-- Las ofertas cuya descripción no coincide con la de la ficha la
-- conservan como propia.
UPDATE products p
  JOIN courses c ON c.id = p.course_id
   SET p.description_custom = 1
 WHERE NOT (p.description <=> c.description)
   AND p.description_custom = 0;

-- ── Comprobación ─────────────────────────────────────────────────────
SELECT 'productos'                     AS dato, COUNT(*) AS n FROM products
UNION ALL
SELECT 'fichas de curso',               COUNT(*) FROM courses
UNION ALL
SELECT 'productos sin ficha',           COUNT(*) FROM products WHERE course_id IS NULL
UNION ALL
SELECT 'fichas con más de una oferta',  COUNT(*) FROM
       (SELECT course_id FROM products WHERE course_id IS NOT NULL
         GROUP BY course_id HAVING COUNT(*) > 1) x
UNION ALL
SELECT 'ofertas con descripción propia', COUNT(*) FROM products WHERE description_custom = 1;
