-- ─────────────────────────────────────────────────────────────────────
-- Corrige la ruta de los adjuntos de admisión
--
-- Se guardaban como /public/uploads/admissions/…, pero el servidor sirve
-- public/ como raíz del sitio, así que esa URL devuelve 404. Los archivos
-- están donde deben: lo que estaba mal era el enlace. Resultado: ningún
-- adjunto de admisión se podía abrir desde el panel.
--
-- Afecta a las tres columnas que guardan rutas de admisión, incluidas las
-- dos antiguas de solicitudes anteriores.
--
-- Es repetible: una vez quitado el prefijo, no vuelve a tocar nada.
-- ─────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

START TRANSACTION;

UPDATE admission_request_files
   SET file_path = CONCAT('/uploads/', SUBSTRING(file_path, LENGTH('/public/uploads/') + 1))
 WHERE file_path LIKE '/public/uploads/%';

UPDATE admission_requests
   SET id_file = CONCAT('/uploads/', SUBSTRING(id_file, LENGTH('/public/uploads/') + 1))
 WHERE id_file LIKE '/public/uploads/%';

UPDATE admission_requests
   SET health_certificate_file = CONCAT('/uploads/', SUBSTRING(health_certificate_file, LENGTH('/public/uploads/') + 1))
 WHERE health_certificate_file LIKE '/public/uploads/%';

COMMIT;

-- ── Comprobación: las tres filas deben dar 0 ─────────────────────────
SELECT 'adjuntos por requisito con /public' AS dato, COUNT(*) AS n
  FROM admission_request_files WHERE file_path LIKE '/public/%'
UNION ALL
SELECT 'identificaciones con /public', COUNT(*)
  FROM admission_requests WHERE id_file LIKE '/public/%'
UNION ALL
SELECT 'certificados médicos con /public', COUNT(*)
  FROM admission_requests WHERE health_certificate_file LIKE '/public/%'
UNION ALL
SELECT 'rutas ya correctas (/uploads/…)', COUNT(*)
  FROM admission_request_files WHERE file_path LIKE '/uploads/%';
