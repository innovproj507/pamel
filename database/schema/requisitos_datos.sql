-- ─────────────────────────────────────────────────────────────────────
-- Requisitos de admisión por curso
-- Generado el 2026-10-12 desde la base local
--
-- Requiere que database/schema/course_requirements.sql ya esté aplicado.
--
-- Los cursos se resuelven por SLUG, que es la única clave única de
-- products. Si un slug no existe en destino, esa parte no inserta nada
-- en lugar de fallar o colgarse de un curso equivocado.
--
-- Es repetible: reemplaza los requisitos de cada curso y modalidad.
-- ─────────────────────────────────────────────────────────────────────

-- El cotejo de la conexión debe coincidir con el de las tablas.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

START TRANSACTION;

-- ═══ 1. Requisitos publicados (los que ve el formulario) ═════════════

-- ── Able Seafarer - Deck ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'able-seafarer-deck' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Proficiency certificate in the navigation watchkeeping in according with the Regulation II/4.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'To evidence a minimum of 12 months of experience on maritime service as part of navigation watchkeeping.', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Valid certificate of a proficiency in survival craft and rescue boats other than fast rescue boats.', 1, 6 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Proficiency certificate in the navigation watchkeeping in according with the Regulation II/4.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'To evidence a minimum of 12 months of experience on maritime service as part of navigation watchkeeping.', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Valid certificate of a proficiency in survival craft and rescue boats other than fast rescue boats.', 1, 6 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 7 FROM dual WHERE @p IS NOT NULL;

-- ── Advanced Training in Fire Fighting ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'advanced-training-in-fire-fighting' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Possess the Fire Prevention and Fighting certificate, in accordance with Rule VI/3, Section A-VI/3, and Table A-VI/3.', 1, 4 FROM dual WHERE @p IS NOT NULL;

-- ── Automatic Identification Systems (AIS) ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'automatic-identification-systems-ais' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'The participant must have satisfactorily passed the course on Radar Navigation, Radar Plotting and Use of ARPA', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Training and experience in the use of radar and ARPA and be fully familiar with the collision regulations, established in the COLREG.', 1, 5 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'The participant must have satisfactorily passed the course on Radar Navigation, Radar Plotting and Use of ARPA', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Training and experience in the use of radar and ARPA and be fully familiar with the collision regulations, established in the COLREG.', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired Course', 1, 6 FROM dual WHERE @p IS NOT NULL;

-- ── Basic Training for Oil and Chemical Tanker Cargo Operations ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'basic-training-for-oil-and-chemical-tanker-cargo-operations' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired Course', 1, 4 FROM dual WHERE @p IS NOT NULL;

-- ── Bridge Resource Management ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'bridge-resource-management' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'To be an applicant for a certificate of competence as an officer in charge of the navigation watch, or have a certificate of competence as officer in charge of the navigation watch', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'To be an applicant for a certificate of competence as an officer in charge of the navigation watch, or have a certificate of competence as officer in charge of the navigation watch', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Company Security Officer ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'company-security-officer' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 4 FROM dual WHERE @p IS NOT NULL;

-- ── Elementary First Aid ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'elementary-first-aid-1' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 4 FROM dual WHERE @p IS NOT NULL;

-- ── Engine Room Resource Management ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'engine-room-resource-management' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'To be an applicant for a certificate of competence as an officer in charge of the engine watch with or without not periodically unmanned engine-room, or have a certificate of competence as officer in charge of the engine watch with or without not periodically unmanned engine-room.', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'To be an applicant for a certificate of competence as an officer in charge of the engine watch with or without not periodically unmanned engine-room, or have a certificate of competence as officer in charge of the engine watch with or without not periodically unmanned engine-room.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Fire Prevention and Fire Fighting ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'fire-prevention-and-fire-fighting-1' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;

-- ── General Operator Certificate (GMDSS) ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'general-operator-certificate-gmdss' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Being over 18 years.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Knowledge of the English language.', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Knowledge and skills in the use of computers.', 1, 6 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Being over 18 years.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Knowledge of the English language.', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Knowledge and skills in the use of computers.', 1, 6 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 7 FROM dual WHERE @p IS NOT NULL;

-- ── High Voltage Installation - Operational Level  ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'high-voltage-installation-operational-level' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'To be an applicant for a certificate of competence as an officer in charge of the engine watch with or without not periodically unmanned engine-room, or have a certificate of competence as officer in charge of the engine watch with or without not periodically unmanned engine-room.', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'To be an applicant for a certificate of competence as an officer in charge of the engine watch with or without not periodically unmanned engine-room, or have a certificate of competence as officer in charge of the engine watch with or without not periodically unmanned engine-room.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Leadership and Teamwork ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'leadership-and-teamwork-1' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Submit evidence that they are taking or have taken training according to Tables A-II/1, A-III/1, A-III/6 of the amended STCW ''78 code or its previous version', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Submit evidence that they are taking or have taken training according to Tables A-II/1, A-III/1, A-III/6 of the amended STCW ''78 code or its previous version', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Marine Environmental Awareness ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'marine-environmental-awareness-1' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Submit evidence that they are taking or have taken training according to Tables A-II/1, A-III/1, A-III/6 of the amended STCW ''78 code or its previous version', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Submit evidence that they are taking or have taken training according to Tables A-II/1, A-III/1, A-III/6 of the amended STCW ''78 code or its previous version', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Maritime English ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'maritime-english' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;

-- ── Medical Care ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'medical-care' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Possess the Medical First Aid certificate, in accordance with Rule VI/4, Section A-VI/4, and Table A-VI/4 para 1-3', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Submit a certificate of competency as a deck officer or engineer officer.', 1, 5 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Possess the Medical First Aid certificate, in accordance with Rule VI/4, Section A-VI/4, and Table A-VI/4 para 1-3', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Submit a certificate of competency as a deck officer or engineer officer.', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 6 FROM dual WHERE @p IS NOT NULL;

-- ── Medical First Aid ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'medical-first-aid' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Possess the Elementary First Aid certificate, in accordance with Rule VI/1, Section A-VI/1, and Table A-VI/1-3.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'This course is intended primarily for applicants for a competency degree or those who already hold one. The participant must ensure the training, aptitude and ability to take care of the crew''s medical care during and after launching, as required by Rule VI/4 of the STCW''78 Convention, as amended, Section A- VI/4 paragraph 1-3.', 0, 5 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Possess the Elementary First Aid certificate, in accordance with Rule VI/1, Section A-VI/1, and Table A-VI/1-3.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'This course is intended primarily for applicants for a competency degree or those who already hold one. The participant must ensure the training, aptitude and ability to take care of the crew''s medical care during and after launching, as required by Rule VI/4 of the STCW''78 Convention, as amended, Section A- VI/4 paragraph 1-3.', 0, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 6 FROM dual WHERE @p IS NOT NULL;

-- ── Operational Use of Electronic Chart Display and Information Systems (ECDIS) ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'operational-use-of-electronic-chart-display-and-information-systems-ecdis' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As a minimum, to be a candidate for an officer position or hold competency certificate as navigation watch.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Completed and satisfactorily passed the course on tracing and use of radar navigation.', 1, 5 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As a minimum, to be a candidate for an officer position or hold competency certificate as navigation watch.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Completed and satisfactorily passed the course on tracing and use of radar navigation.', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired Course', 1, 6 FROM dual WHERE @p IS NOT NULL;

-- ── Passenger Safety, Cargo Safety and Hull Integrity Training ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'PassengerSafety-Cargo-Safety-and-Hull-Integrity-Training' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Present the four (4) basic security courses', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Submit evidence that they are taking or have taken training according to Tables A-II/1, A-III/1, A-III/6 of the amended STCW ‘78 code or its previous version.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired Course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Passenger Ship Crisis Management and Human Behaviour Training ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'passenger-ship-crisis-management-and-human-behaviour-training-1' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Have completed the four (4) basic training courses established in the training code in section A-VI/1.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Elementary first aid', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Fire prevention and fire fighting', 1, 6 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Personal safety and social responsibility', 1, 7 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Personal survival techniques', 1, 8 FROM dual WHERE @p IS NOT NULL;

-- ── Personal Safety and Social Responsibilities ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Personal-Safety-and-Social-Responsibilities' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 4 FROM dual WHERE @p IS NOT NULL;

-- ── Personal Survival Techniques ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Personal-Survival-Techniques' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expire course', 1, 4 FROM dual WHERE @p IS NOT NULL;

-- ── Port Facility Security Officer ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'port-facility-security-officer-1' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;

-- ── Radar Navigation, Radar Plotting and Use of ARPA - Operational Level ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'radar-navigation-radar-plotting-and-use-of-arpa-operational-level' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Minimum experience of 6 months of service at sea', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Minimum experience of 6 months of service at sea', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired Course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Security Awareness Training for All Seafarers ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Security-Awareness-Training-for-All-Seafarers' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 4 FROM dual WHERE @p IS NOT NULL;

-- ── Security Awareness Training for Port Facility Personnel with designated security duties ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'security-awareness-training-for-port-facility-personnel-with-designated-security-duties-1' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 4 FROM dual WHERE @p IS NOT NULL;

-- ── Ship Security Officer ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'ship-security-officer' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Have completed the four (4) basic training courses established in the training code in section A-VI/1.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Elementary first aid', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Fire prevention and fire fighting', 1, 6 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Personal safety and social responsibility', 1, 7 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Personal survival techniques', 1, 8 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Have completed the four (4) basic training courses established in the training code in section A-VI/1.', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Elementary first aid', 1, 6 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Fire prevention and fire fighting', 1, 7 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Personal safety and social responsibility', 1, 8 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Personal survival techniques', 1, 9 FROM dual WHERE @p IS NOT NULL;

-- ── Ship Simulator and Bridge Teamwork ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'ship-simulator-and-bridge-teamwork' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Have a minimum experience of six (6) months of service at sea of watchkeeping experience as officer in charge of a navigational watch.', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Have a minimum experience of six (6) months of service at sea of watchkeeping experience as officer in charge of a navigational watch.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Survival Craft and Rescue Boats other than Fast Rescue Boats ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'survival-craft-and-rescue-boats-other-than-fast-rescue-boats' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Have completed the four (4) basic training courses established in the training code in section A-VI/1.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Elementary first aid', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Fire prevention and fire fighting', 1, 6 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Personal safety and social responsibility', 1, 7 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Personal survival techniques', 1, 8 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired curse', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Have completed the four (4) basic training courses established in the training code in section A-VI/1.', 1, 5 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Elementary first aid', 1, 6 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Fire prevention and fire fighting', 1, 7 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Personal safety and social responsibility', 1, 8 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Personal survival techniques', 1, 9 FROM dual WHERE @p IS NOT NULL;

-- ── Train the Simulator Trainer and Assessor ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'train-the-simulator-trainer-and-assessor' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'To have approved the Training Course for instructors.', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'To have approved the Training Course for instructors.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Training Course for Instructors ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'training-course-for-instructors-1' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Must be over 18 years of age', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Submit at least one (1) year of work experience.', 1, 5 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Must be over 18 years of age', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Submit at least one (1) year of work experience.', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ── Use of Leadership and Managerial Skills ──
SET @p := (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Use-of-Leadership-and-Managerial-Skills' COLLATE utf8mb4_unicode_ci LIMIT 1);
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'new';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'new', 'Entry rules must be in accordance with Rules II/2 and III/2 of the STCW''78 Convention, as amended.', 1, 4 FROM dual WHERE @p IS NOT NULL;
DELETE FROM course_requirements WHERE product_id = @p AND mode = 'refresher';
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'ID / PASSPORT', 1, 1 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'As per Structure of the Course Program', 0, 2 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Good health, certified by means of a medical certificate', 1, 3 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Submit evidence that they are taking or have taken training according to Tables A-II/1, A-III/1, A-III/6 of the amended STCW ‘78 code or its previous version.', 1, 4 FROM dual WHERE @p IS NOT NULL;
INSERT INTO course_requirements (product_id, mode, label, requires_file, order_num)
SELECT @p, 'refresher', 'Expired course', 1, 5 FROM dual WHERE @p IS NOT NULL;

-- ═══ 2. Bandeja de revisión de los documentos Word ═══════════════════
-- Permite seguir mapeando los pendientes desde el panel sin necesidad
-- de subir la carpeta Requisitos/ al servidor.

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-AS', '66. UPGRADING FOR MASTERS AND CHIEF MATES ON SHIPS OF 500 GROSS TONNAGE OR MORE.docx', 'UPGRADING FOR MASTERS AND CHIEF MATES ON SHIPS OF 500 GROSS TONNAGE OR MORE', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"To be in possession of a certificate of competence of officer in charge of the navigational watch on ships of 500 gross tonnage or more.","requires_file":1},{"label":"Be in possession of the following course certificates:","requires_file":1},{"label":"Radar, APRA, bridge teamwork and search and rescue.","requires_file":1},{"label":"Operational use of the electronic chart display and information system (ECDIS).","requires_file":1},{"label":"Use of leadership and management skills.","requires_file":1},{"label":"Proficiency in handling survival craft and rescue boats other than fast rescue boats.","requires_file":1},{"label":"Advanced training in firefighting.","requires_file":1},{"label":"Medical care on board.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-AS', '67. UPGRADING FOR CHIEF ENGINEER OFFICES AND SECOND ENGINEERING OFFICERS ON SHIPS POWERED BY MAIN PROPULSION MACHINERY OF 3000 KW PROPULSION POWER OR MORE.docx', 'UPGRADING FOR CHIEF ENGINEER OFFICES AND SECOND ENGINEERING OFFICERS ON SHIPS POWERED BY MAIN PROPULSION MACHINERY OF 3000 KW PROPULSION POWER OR MORE', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"To hold a certificate of competence as Officer in Charge of an engineering watch in a manned engine-room or designated duty engineers’ officers designed in a periodically unmanned engine-room, whose main propeller engine has a power equal and superior to 750 KW.","requires_file":1},{"label":"To have the following course certificates:","requires_file":1},{"label":"Use of leadership and management skills.","requires_file":1},{"label":"Proficiency on handling survival ships and rescue boats that are not fast rescue boats","requires_file":1},{"label":"Advanced training on firefighting.","requires_file":1},{"label":"High voltage installation course.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '1. BASIC TRAINING FOR OIL AND CHEMICAL TANKER CARGO OPERATIONS.docx', 'BASIC TRAINING FOR OIL AND CHEMICAL TANKER CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'basic-training-for-oil-and-chemical-tanker-cargo-operations' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '10. MEDICAL FIRST AID.docx', 'MEDICAL FIRST AID.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Possess the Elementary First Aid certificate, in accordance with Rule VI\\/1, Section A-VI\\/1, and Table A-VI\\/1-3.","requires_file":1},{"label":"This course is intended primarily for applicants for a competency degree or those who already hold one. The participant must ensure the training, aptitude and ability to take care of the crew''s medical care during and after launching, as required by Rule VI\\/4 of the STCW''78 Convention, as amended, Section A- VI\\/4 paragraph 1-3.","requires_file":0}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'medical-first-aid' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '103. SHIP´S COOK ACCORDING TO MARITIME LABOUR CONVENTION.docx', 'SHIP´S COOK ACCORDING TO MARITIME LABOUR CONVENTION', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '104. BRIDGE RESOURCE MANAGEMENT.docx', 'BRIDGE RESOURCE MANAGEMENT', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To be an applicant for a certificate of competence as an officer in charge of the navigation watch, or have a certificate of competence as officer in charge of the navigation watch","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'bridge-resource-management' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '105. ENGINE ROOM RESOURCE MANAGEMENT.docx', 'ENGINE ROOM RESOURCE MANAGEMENT', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To be an applicant for a certificate of competence as an officer in charge of the engine watch with or without not periodically unmanned engine-room, or have a certificate of competence as officer in charge of the engine watch with or without not periodically unmanned engine-room.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'engine-room-resource-management' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '11. MEDICAL CARE.docx', 'MEDICAL CARE.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Possess the Medical First Aid certificate, in accordance with Rule VI\\/4, Section A-VI\\/4, and Table A-VI\\/4 para 1-3","requires_file":1},{"label":"Submit a certificate of competency as a deck officer or engineer officer.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'medical-care' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '12. PROFICIENCY IN PERSONAL SURVIVAL TECHNIQUES.docx', 'PERSONAL SURVIVAL TECHNIQUES. (VI/1-1)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Personal-Survival-Techniques' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '13. FIRE PREVENTION AND FIRE FIGHTING.docx', 'FIRE PREVENTION AND FIRE FIGHTING. (RULE VI/2)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'fire-prevention-and-fire-fighting-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '14. PERSONAL SAFETY AND SOCIAL RESPONSIBILITIES.docx', 'PERSONAL SAFETY AND SOCIAL RESPONSIBILITIES', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Personal-Safety-and-Social-Responsibilities' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '15. SHIP SIMULATOR AND BRIDGE TEAMWORK.docx', 'SHIP SIMULATOR AND BRIDGE TEAMWORK', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have a minimum experience of six (6) months of service at sea of watchkeeping experience as officer in charge of a navigational watch.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'ship-simulator-and-bridge-teamwork' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '16. PROFICIENCY IN ISURVIVAL CRAFT AND RESCUE BOATS OTHER THAN FAST RESCUE BOATS.docx', 'PROFICIENCY IN ISURVIVAL CRAFT AND RESCUE BOATS OTHER THAN FAST RESCUE BOATS.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'survival-craft-and-rescue-boats-other-than-fast-rescue-boats' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '17. PROFICIENCY IN FAST RESCUE BOATS.docx', 'PROFICIENCY IN FAST RESCUE BOATS.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the (4) basic training in survival techniques as per the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1},{"label":"Present the certificates of current basic security courses.","requires_file":1},{"label":"Proficiency course in the handling of survival craft and rescue boats other than fast rescue boats.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '18. GENERAL OPERATOR CERTIFICATE FOR GMDSS.docx', 'GENERAL OPERATOR CERTIFICATE FOR THE GLOBAL MARITIME DISTRESS AND SAFETY SYSTEM', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Being over 18 years.","requires_file":1},{"label":"Knowledge of the English language.","requires_file":1},{"label":"Knowledge and skills in the use of computers.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'general-operator-certificate-gmdss' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '19. RESTRICTED OPERATOR CERTIFICATE FOR GMDSS.docx', 'RESTRICTED OPERATOR CERTIFICATE FOR THE GLOBAL MARITIME DISTRESS AND SAFETY SYSTEM', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Being over 18 years.","requires_file":1},{"label":"Knowledge of the English language.","requires_file":1},{"label":"Knowledge and skills in the use of computers.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '2. ADVANCED TRAINING FOR OIL TANKERS CARGO OPERATIONS.docx', 'ADVANCED TRAINING FOR OIL TANKERS CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have the course and be under possession of a certificate of proficiency of Basic Training for Oil and Chemical Tanker cargo Operations, as per Regulation V\\/1-1, paragraph 2, Section A-V\\/1-1, paragraph 1 and Table A-V\\/1-1-1.","requires_file":1},{"label":"Have a minimum of three (3) months of experience onboard oil tankers vessels.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '20. OPERATIONAL USE OF ELECTRONIC CHART DISPLAY AND INFORMATION SYSTEM.docx', 'OPERATIONAL USE OF ELECTRONIC CHART DISPLAY AND INFORMATION SYSTEM', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"As a minimum, to be a candidate for an officer position or hold competency certificate as navigation watch.","requires_file":1},{"label":"Completed and satisfactorily passed the course on tracing and use of radar navigation.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'operational-use-of-electronic-chart-display-and-information-systems-ecdis' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '21. PASSENGER SAFETY, CARGO SAFETY AND HULL INTEGRITY TRAINING.docx', 'PASSENGER SAFETY, CARGO SAFETY AND HULL INTEGRITY TRAINING.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Present the four (4) basic security courses","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'PassengerSafety-Cargo-Safety-and-Hull-Integrity-Training' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '26. AUTOMATIC IDENTIFICATION SYSTEM.docx', 'AUTOMATIC IDENTIFICATION SYSTEM', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"The participant must have satisfactorily passed the course on Radar Navigation, Radar Plotting and Use of ARPA","requires_file":1},{"label":"Training and experience in the use of radar and ARPA and be fully familiar with the collision regulations, established in the COLREG.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'automatic-identification-systems-ais' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '3. ADVANCED TRAINING FOR CHEMICAL TANKERS CARGO OPERATIONS.docx', 'ADVANCED TRAINING FOR CHEMICAL TANKERS CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have the course and be under possession of a certificate of proficiency of Basic Training for Oil and Chemical Tanker cargo Operations, as per Regulation V\\/1-1, paragraph 2, Section A-V\\/1-1, paragraph 1 and Table A-V\\/1-1-1.","requires_file":1},{"label":"Have a minimum of three (3) months of experience onboard oil tankers vessels.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '30. MARINE ENVIRONMENTAL AWARENESS.docx', 'MARINE ENVIRONMENTAL AWARENESS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Submit evidence that they are taking or have taken training according to Tables A-II\\/1, A-III\\/1, A-III\\/6 of the amended STCW ''78 code or its previous version","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'marine-environmental-awareness-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '31. LEADERSHIP AND TEAMWORK.docx', 'LEADERSHIP AND TEAMWORK.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Submit evidence that they are taking or have taken training according to Tables A-II\\/1, A-III\\/1, A-III\\/6 of the amended STCW ''78 code or its previous version","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'leadership-and-teamwork-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '32. USE OF LEADERSHIP AND MANAGERIAL SKILLS.docx', 'USE OF LEADERSHIP AND MANAGERIAL SKILLS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Entry rules must be in accordance with Rules II\\/2 and III\\/2 of the STCW''78 Convention, as amended.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Use-of-Leadership-and-Managerial-Skills' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '33. PASSENGER SHIPS CROWD CONTROL MANAGEMENT.docx', 'PASSENGER SHIPS CROWD CONTROL MANAGEMENT TRAINING (V/2-1)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '34. PASSENGER SHIP CRISIS MANAGEMENT AND HUMAN BEHAVIOUR.docx', 'PASSENGER SHIP CRISIS MANAGEMENT AND HUMAN BEHAVIOUR.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'passenger-ship-crisis-management-and-human-behaviour-training-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '35. SAFETY TRAINING FOR PERSONNEL PROVIDING DIRECT SERVICE TO PASSENGERS IN PASSENGER SPACES.docx', 'SAFETY TRAINING FOR PERSONNEL PROVIDING DIRECT SERVICE TO PASSENGERS IN PASSENGER SPACES', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '37. ADVANCED TRAINING IN FIRE FIGHTING.docx', 'ADVANCED TRAINING IN FIRE FIGHTING.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Possess the Fire Prevention and Fighting certificate, in accordance with Rule VI\\/3, Section A-VI\\/3, and Table A-VI\\/3.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'advanced-training-in-fire-fighting' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '39. ENGINE ROOM SIMULATOR.docx', 'ENGINE ROOM SIMULATOR', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"To be an applicant for a certificate of competence as an officer in charge of the engine watch with or without not periodically unmanned engine-room, or","requires_file":1},{"label":"To have a certificate of competence as officer in charge of the engine watch with or without not periodically unmanned engine-room.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '4. BASIC TRAINING FOR LIQUEFIED GAS TANKERS CARGO OPERATIONS.docx', 'BASIC TRAINING FOR LIQUEFIED GAS TANKERS CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To present a valid international medical certificate.","requires_file":1},{"label":"To have completed the four (4) basic training courses established in the STCW Code in section A-VI \\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '48. ASSESSMENT, EXAMINATION AND CERTIFICATION OF SEAFARERS.docx', 'TRAINING COURSE FOR ASSESSMENT, EXAMINATION AND CERTIFICATION OF SEAFARERS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '5. ADVANCED TRAINING FOR LIQUEFIED GAS TANKERS CARGO OPERATIONS.docx', 'ADVANCED TRAINING FOR LIQUEFIED GAS TANKERS CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Certificate of basic training course for cargo operations in tankers for the transport of liquefied gas.","requires_file":1},{"label":"Have completed an approved embarkation period of at least three months in tankers for the transport of liquefied gas","requires_file":1},{"label":"Have completed approved training on board a tanker for the transport of liquefied gas for at least one month, including at least three loading and three unloading operations.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '52. MARITIME ENGLISH.docx', 'MARITIME ENGLISH', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'maritime-english' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '54. SHIP SECURITY OFFICER.docx', 'SHIP SECURITY OFFICER.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'ship-security-officer' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '55. COMPANY SECURITY OFFICER.docx', 'COMPANY SECURITY OFFICER.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'company-security-officer' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '56. PORT FACILITY SECURITY OFFICER.docx', 'PORT FACILITY SECURITY OFFICER.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'port-facility-security-officer-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '59. SECURITY AWARENESS TRAINING FOR PORT FACILITY PERSONNEL WITH DESIGNATE SECURITY DUTIES.docx', 'SECURITY AWARENESS TRAINING FOR PORT FACILITY PERSONNEL WITH DESIGNATED SECURITY DUTIES', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '6. RADAR NAVIGATION, RADAR PLOTTING AND USE OF ARPA. OPERATIONAL LEVEL.docx', 'RADAR NAVIGATION, RADAR PLOTTING AND USE OF ARPA. OPERATIONAL LEVEL', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Minimum experience of 6 months of service at sea","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'radar-navigation-radar-plotting-and-use-of-arpa-operational-level' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '60. SECURITY AWARENESS TRAINING FOR PORT FACILITY PERSONNEL.docx', 'SECURITY AWARENESS TRAINING FOR PORT FACILITY PERSONNEL', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '61. SECURITY TRAINING FOR SEAFARERS DESIGNATED SECURITY DUTIES.docx', 'SECURITY TRAINING FOR SEAFARERS DESIGNATED SECURITY DUTIES. (VI/6-2)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '62. SECURITY AWARENESS TRAINING FOR ALL SEAFARERS.docx', 'SECURITY AWARENESS TRAINING FOR ALL SEAFARERS. (VI/6-1)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Security-Awareness-Training-for-All-Seafarers' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '64. TRAINING COURSE FOR INSTRUCTORS.docx', 'TRAINING COURSE FOR INSTRUCTORS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Must be over 18 years of age","requires_file":1},{"label":"Submit at least one (1) year of work experience.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'training-course-for-instructors-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '65. TRAIN THE SIMULATOR TRAINER AND ASSESSOR.docx', 'TRAIN THE SIMULATOR TRAINER AND ASSESSOR', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To have approved the Training Course for instructors.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'train-the-simulator-trainer-and-assessor' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '7. RADAR, APRA, BRIDGE TEAMWORK AND SEARCH AND RESCUE (RADAR NAVIGATION AT MANAGEMENT LEVEL).docx', 'RADAR, APRA, BRIDGE TEAMWORK AND SEARCH AND RESCUE (RADAR NAVIGATION AT MANAGEMENT LEVEL)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Certificate as “Radar Navigation, Radar Plotting and Use of ARPA. Operational Level","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '74. RATING FORMING PART OF AN ENGINEERING WATCH.docx', 'RATING FORMING PART OF AN ENGINEERING WATCH', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '75. ABLE SEAFARER DECK.docx', 'RATINGS AS ABLE SEAFARER DECK', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Proficiency certificate in the navigation watchkeeping in according with the Regulation II\\/4.","requires_file":1},{"label":"To evidence a minimum of 12 months of experience on maritime service as part of navigation watchkeeping.","requires_file":1},{"label":"Valid certificate of a proficiency in survival craft and rescue boats other than fast rescue boats.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'able-seafarer-deck' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '76. BASIC TRAINING FOR SHIPS OPERATING IN POLAR WATERS.docx', 'BASIC TRAINING FOR SHIPS OPERATING IN POLAR WATERS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Certificate of Competency as Captain, First Officer or Officer in charge of the navigation watch.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '77. ADVANCED TRAINING FOR SHIPS OPERATING IN POLAR WATERS.docx', 'ADVANCED TRAINING FOR SHIPS OPERATING IN POLAR WATERS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Certificate as Master, chief mate or officer in charge of a navigation watch qualified in accordance with chapter V of the STCW Convention and the STCW code. The Minimum qualification will be set at STCW Regulation II\\/1 ‐ II\\/2 ‐ II\\/3 or equivalent as determined by the local administration","requires_file":1},{"label":"Meet the requirements for certification in basic training for ships in polar waters","requires_file":1},{"label":"Have at least two (2) months of approved seagoing service in the deck department, at management level or while performing watch-keeping duties in an operational level, within polar waters or other equivalent approved seagoing service.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '78. BASIC TRAINING FOR SHIPS SUBJECT TO THE IGF CODE.docx', 'BASIC TRAINING FOR SHIPS SUBJECT TO THE IGF CODE', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Basic safety courses in accordance with Regulation VI\\/1 of the STCW 1978 Convention, as amended.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '79. ADVANCED TRAINING FOR SHIPS SUBJECT TO THE IGF CODE.docx', 'ADVANCED TRAINING FOR SHIPS SUBJECT TO THE IGF CODE', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have a certificate of basic training for ships subject to the IGF Code.","requires_file":1},{"label":"Have a certificate of competence as deck or engine officer, or captain.","requires_file":1},{"label":"Experience approved on board for at least one month, in which you have participated in the fueling operation on board the ship at least once.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '80. ELECTRO-TECHNICAL RATINGS.docx', 'ELECTRO-TECHNICAL RATINGS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Proficiency certificate in the ratings forming part of an engineering watch in according with the Regulation III\\/5.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '81. ABLE SEAFARER ENGINE.docx', 'RATINGS AS ABLE SEAFARER ENGINE', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Proficiency certificate in the ratings forming part of an engineering watch in according with the Regulation III\\/4.","requires_file":1},{"label":"Must have minimum of 6 months experience as a seafarer on watch in an engine room.","requires_file":1},{"label":"Valid certificate of a proficiency in survival craft and rescue boats other than fast rescue boats.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '9. ELEMENTARY FIRST AID.docx', 'ELEMENTARY FIRST AID. (VI/1-3)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'elementary-first-aid-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '93. HIGH VOLTAGE INSTALLATION. OPERATIONAL LEVEL.docx', 'HIGH VOLTAGE INSTALLATION. OPERATIONAL LEVEL', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To be an applicant for a certificate of competence as an officer in charge of the engine watch with or without not periodically unmanned engine-room, or have a certificate of competence as officer in charge of the engine watch with or without not periodically unmanned engine-room.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'high-voltage-installation-operational-level' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '94. HIGH VOLTAGE INSTALLATION. MANAGEMENTE LEVEL.docx', 'HIGH VOLTAGE INSTALLATION. MANAGEMENTE LEVEL', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To be an applicant for a certificate of competence as a chief engineer officer and second engineer officer (first engineer officers), or have a certificate of competence as officer in charge of the engine watch.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '95. PLEASURE YACHT CAPTAIN.docx', 'PLEASURE YACHT CAPTAIN', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '96. ORDINARY SEAMAN.docx', 'ORDINARY SEAMAN', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-C', '97. RATING FORMING PART OF A NAVIGATIONAL WATCH.docx', 'RATING FORMING PART OF A NAVIGATIONAL WATCH', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '1. BASIC TRAINING FOR OIL AND CHEMICAL TANKER CARGO OPERATIONS.docx', 'BASIC TRAINING FOR OIL AND CHEMICAL TANKER CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired Course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'basic-training-for-oil-and-chemical-tanker-cargo-operations' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '10. MEDICAL FIRST AID.docx', 'MEDICAL FIRST AID.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Possess the Elementary First Aid certificate, in accordance with Rule VI\\/1, Section A-VI\\/1, and Table A-VI\\/1-3.","requires_file":1},{"label":"This course is intended primarily for applicants for a competency degree or those who already hold one. The participant must ensure the training, aptitude and ability to take care of the crew''s medical care during and after launching, as required by Rule VI\\/4 of the STCW''78 Convention, as amended, Section A- VI\\/4 paragraph 1-3.","requires_file":0},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'medical-first-aid' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '104. BRIDGE RESOURCE MANAGEMENT.docx', 'BRIDGE RESOURCE MANAGEMENT', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To be an applicant for a certificate of competence as an officer in charge of the navigation watch, or have a certificate of competence as officer in charge of the navigation watch","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'bridge-resource-management' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '105. ENGINE ROOM RESOURCE MANAGEMENT.docx', 'ENGINE ROOM RESOURCE MANAGEMENT', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To be an applicant for a certificate of competence as an officer in charge of the engine watch with or without not periodically unmanned engine-room, or have a certificate of competence as officer in charge of the engine watch with or without not periodically unmanned engine-room.","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'engine-room-resource-management' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '11. MEDICAL CARE.docx', 'MEDICAL CARE.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Possess the Medical First Aid certificate, in accordance with Rule VI\\/4, Section A-VI\\/4, and Table A-VI\\/4 para 1-3","requires_file":1},{"label":"Submit a certificate of competency as a deck officer or engineer officer.","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'medical-care' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '12. PROFICIENCY IN PERSONAL SURVIVAL TECHNIQUES.docx', 'PROFICIENCY IN PERSONAL SURVIVAL TECHNIQUES. (VI/1-1)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expire course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Personal-Survival-Techniques' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '14. PERSONAL SAFETY AND SOCIAL RESPONSIBILITIES.docx', 'PERSONAL SAFETY AND SOCIAL RESPONSIBILITIES', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Personal-Safety-and-Social-Responsibilities' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '15. SHIP SIMULATOR AND BRIDGE TEAMWORK.docx', 'SHIP SIMULATOR AND BRIDGE TEAMWORK', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have a minimum experience of six (6) months of service at sea of watchkeeping experience as officer in charge of a navigational watch.","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'ship-simulator-and-bridge-teamwork' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '16. PROFICIENCY IN ISURVIVAL CRAFT AND RESCUE BOATS OTHER THAN FAST RESCUE BOATS.docx', 'PROFICIENCY IN ISURVIVAL CRAFT AND RESCUE BOATS OTHER THAN FAST RESCUE BOATS.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired curse","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'survival-craft-and-rescue-boats-other-than-fast-rescue-boats' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '17. PROFICIENCY IN FAST RESCUE BOATS.docx', 'PROFICIENCY IN FAST RESCUE BOATS.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1},{"label":"Have completed the (4) basic training in survival techniques as per the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1},{"label":"Present the certificates of current basic security courses.","requires_file":1},{"label":"Proficiency course in the handling of survival craft and rescue boats other than fast rescue boats.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '18. GENERAL OPERATOR CERTIFICATE FOR GMDSS.docx', 'GENERAL OPERATOR CERTIFICATE FOR THE GLOBAL MARITIME DISTRESS AND SAFETY SYSTEM', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Being over 18 years.","requires_file":1},{"label":"Knowledge of the English language.","requires_file":1},{"label":"Knowledge and skills in the use of computers.","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'general-operator-certificate-gmdss' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '2. ADVANCED TRAINING FOR OIL TANKERS CARGO OPERATIONS.docx', 'ADVANCED TRAINING FOR OIL TANKERS CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have the course and be under possession of a certificate of proficiency of Basic Training for Oil and Chemical Tanker cargo Operations, as per Regulation V\\/1-1, paragraph 2, Section A-V\\/1-1, paragraph 1 and Table A-V\\/1-1-1.","requires_file":1},{"label":"Have a minimum of three (3) months of experience onboard oil tankers vessels.","requires_file":1},{"label":"Expired Course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '20. OPERATIONAL USE OF ELECTRONIC CHART DISPLAY AND INFORMATION SYSTEM.docx', 'OPERATIONAL USE OF ELECTRONIC CHART DISPLAY AND INFORMATION SYSTEM', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"As a minimum, to be a candidate for an officer position or hold competency certificate as navigation watch.","requires_file":1},{"label":"Completed and satisfactorily passed the course on tracing and use of radar navigation.","requires_file":1},{"label":"Expired Course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'operational-use-of-electronic-chart-display-and-information-systems-ecdis' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '21. PASSENGER SAFETY, CARGO SAFETY AND HULL INTEGRITY TRAINING.docx', 'PASSENGER SAFETY, CARGO SAFETY AND HULL INTEGRITY TRAINING.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Submit evidence that they are taking or have taken training according to Tables A-II\\/1, A-III\\/1, A-III\\/6 of the amended STCW ‘78 code or its previous version.","requires_file":1},{"label":"Expired Course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'PassengerSafety-Cargo-Safety-and-Hull-Integrity-Training' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '26. AUTOMATIC IDENTIFICATION SYSTEM.docx', 'AUTOMATIC IDENTIFICATION SYSTEM', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"The participant must have satisfactorily passed the course on Radar Navigation, Radar Plotting and Use of ARPA","requires_file":1},{"label":"Training and experience in the use of radar and ARPA and be fully familiar with the collision regulations, established in the COLREG.","requires_file":1},{"label":"Expired Course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'automatic-identification-systems-ais' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '3. ADVANCED TRAINING FOR CHEMICAL TANKERS CARGO OPERATIONS.docx', 'ADVANCED TRAINING FOR CHEMICAL TANKERS CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have the course and be under possession of a certificate of proficiency of Basic Training for Oil and Chemical Tanker cargo Operations, as per Regulation V\\/1-1, paragraph 2, Section A-V\\/1-1, paragraph 1 and Table A-V\\/1-1-1.","requires_file":1},{"label":"Have a minimum of three (3) months of experience onboard oil tankers vessels.","requires_file":1},{"label":"Expired course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '30. MARINE ENVIRONMENTAL AWARENESS.docx', 'MARINE ENVIRONMENTAL AWARENESS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Submit evidence that they are taking or have taken training according to Tables A-II\\/1, A-III\\/1, A-III\\/6 of the amended STCW ''78 code or its previous version","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'marine-environmental-awareness-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '31. LEADERSHIP AND TEAMWORK.docx', 'LEADERSHIP AND TEAMWORK.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Submit evidence that they are taking or have taken training according to Tables A-II\\/1, A-III\\/1, A-III\\/6 of the amended STCW ''78 code or its previous version","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'leadership-and-teamwork-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '32. USE OF LEADERSHIP AND MANAGERIAL SKILLS.docx', 'USE OF LEADERSHIP AND MANAGERIAL SKILLS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Submit evidence that they are taking or have taken training according to Tables A-II\\/1, A-III\\/1, A-III\\/6 of the amended STCW ‘78 code or its previous version.","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Use-of-Leadership-and-Managerial-Skills' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '33. PASSENGER SHIPS CROWD CONTROL MANAGEMENT.docx', 'PASSENGER SHIPS CROWD CONTROL MANAGEMENT TRAINING (V/2-1)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '35. SAFETY TRAINING FOR PERSONNEL PROVIDING DIRECT SERVICE TO PASSENGERS IN PASSENGER SPACES.docx', 'SAFETY TRAINING FOR PERSONNEL PROVIDING DIRECT SERVICE TO PASSENGERS IN PASSENGER SPACES', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Expired course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '4. BASIC TRAINING FOR LIQUEFIED GAS TANKERS CARGO OPERATIONS.docx', 'BASIC TRAINING FOR LIQUEFIED GAS TANKERS CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To present a valid international medical certificate.","requires_file":1},{"label":"Expired course","requires_file":1},{"label":"To have completed the four (4) basic training courses established in the STCW Code in section A-VI \\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '5. ADVANCED TRAINING FOR LIQUEFIED GAS TANKERS CARGO OPERATIONS.docx', 'ADVANCED TRAINING FOR LIQUEFIED GAS TANKERS CARGO OPERATIONS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Certificate of basic training course for cargo operations in tankers for the transport of liquefied gas.","requires_file":1},{"label":"Have completed an approved embarkation period of at least three months in tankers for the transport of liquefied gas","requires_file":1},{"label":"Have completed approved training on board a tanker for the transport of liquefied gas for at least one month, including at least three loading and three unloading operations.","requires_file":1},{"label":"Expired Course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '54. SHIP SECURITY OFFICER.docx', 'SHIP SECURITY OFFICER.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'ship-security-officer' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '55. COMPANY SECURITY OFFICER.docx', 'COMPANY SECURITY OFFICER.', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'company-security-officer' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '6. RADAR NAVIGATION, RADAR PLOTTING AND USE OF ARPA. OPERATIONAL LEVEL.docx', 'RADAR NAVIGATION, RADAR PLOTTING AND USE OF ARPA OPERATIONAL LEVEL', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Minimum experience of 6 months of service at sea","requires_file":1},{"label":"Expired Course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'radar-navigation-radar-plotting-and-use-of-arpa-operational-level' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '60. SECURITY AWARENESS TRAINING FOR PORT FACILITY PERSONNEL.docx', 'SECURITY AWARENESS TRAINING FOR PORT FACILITY PERSONNEL', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'security-awareness-training-for-port-facility-personnel-with-designated-security-duties-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '61. SECURITY TRAINING FOR SEAFARERS DESIGNATED SECURITY DUTIES.docx', 'SECURITY TRAINING FOR SEAFARERS DESIGNATED SECURITY DUTIES. (VI/6-2)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '62. SECURITY AWARENESS TRAINING FOR ALL SEAFARERS.docx', 'SECURITY AWARENESS TRAINING FOR ALL SEAFARERS. (VI/6-1)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'Security-Awareness-Training-for-All-Seafarers' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '64. TRAINING COURSE FOR INSTRUCTORS.docx', 'TRAINING COURSE FOR INSTRUCTORS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Must be over 18 years of age","requires_file":1},{"label":"Submit at least one (1) year of work experience.","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'training-course-for-instructors-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '65. TRAIN THE SIMULATOR TRAINER AND ASSESSOR.docx', 'TRAIN THE SIMULATOR TRAINER AND ASSESSOR', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To have approved the Training Course for instructors.","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'train-the-simulator-trainer-and-assessor' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '7. RADAR, APRA, BRIDGE TEAMWORK AND SEARCH AND RESCUE (RADAR NAVIGATION AT MANAGEMENT LEVEL).docx', 'RADAR, APRA, BRIDGE TEAMWORK AND SEARCH AND RESCUE (RADAR NAVIGATION AT MANAGEMENT LEVEL)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Certificate as “Radar Navigation, Radar Plotting and Use of ARPA. Operational Level","requires_file":1},{"label":"Expired course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '74. RATING FORMING PART OF AN ENGINEERING WATCH.docx', 'RATING FORMING PART OF AN ENGINEERING WATCH', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '75. ABLE SEAFARER DECK.docx', 'RATINGS AS ABLE SEAFARER DECK', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Proficiency certificate in the navigation watchkeeping in according with the Regulation II\\/4.","requires_file":1},{"label":"To evidence a minimum of 12 months of experience on maritime service as part of navigation watchkeeping.","requires_file":1},{"label":"Valid certificate of a proficiency in survival craft and rescue boats other than fast rescue boats.","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'able-seafarer-deck' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '76. BASIC TRAINING FOR SHIPS OPERATING IN POLAR WATERS.docx', 'BASIC TRAINING FOR SHIPS OPERATING IN POLAR WATERS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Certificate of Competency as Captain, First Officer or Officer in charge of the navigation watch.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '77. ADVANCED TRAINING FOR SHIPS OPERATING IN POLAR WATERS.docx', 'ADVANCED TRAINING FOR SHIPS OPERATING IN POLAR WATERS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Certificate as Master, chief mate or officer in charge of a navigation watch qualified in accordance with chapter V of the STCW Convention and the STCW code. The Minimum qualification will be set at STCW Regulation II\\/1 ‐ II\\/2 ‐ II\\/3 or equivalent as determined by the local administration","requires_file":1},{"label":"Meet the requirements for certification in basic training for ships in polar waters","requires_file":1},{"label":"Have at least two (2) months of approved seagoing service in the deck department, at management level or while performing watch-keeping duties in an operational level, within polar waters or other equivalent approved seagoing service.","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '78. BASIC TRAINING FOR SHIPS SUBJECT TO THE IGF CODE.docx', 'BASIC TRAINING FOR SHIPS SUBJECT TO THE IGF CODE', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Basic safety courses in accordance with Regulation VI\\/1 of the STCW 1978 Convention, as amended.","requires_file":1},{"label":"Expired course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '79. ADVANCED TRAINING FOR SHIPS SUBJECT TO THE IGF CODE.docx', 'ADVANCED TRAINING FOR SHIPS SUBJECT TO THE IGF CODE', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Have a certificate of basic training for ships subject to the IGF Code.","requires_file":1},{"label":"Have a certificate of competence as deck or engine officer, or captain.","requires_file":1},{"label":"Experience approved on board for at least one month, in which you have participated in the fueling operation on board the ship at least once.","requires_file":1},{"label":"Expired course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '80. ELECTRO-TECHNICAL RATINGS.docx', 'ELECTRO-TECHNICAL RATINGS', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Proficiency certificate in the ratings forming part of an engineering watch in according with the Regulation III\\/5.","requires_file":1},{"label":"Expired course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '81. ABLE SEAFARER ENGINE.docx', 'RATINGS AS ABLE SEAFARER ENGINE', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Proficiency certificate in the ratings forming part of an engineering watch in according with the Regulation III\\/4.","requires_file":1},{"label":"Must have minimum of 6 months experience as a seafarer on watch in an engine room.","requires_file":1},{"label":"Valid certificate of a proficiency in survival craft and rescue boats other than fast rescue boats.","requires_file":1},{"label":"Expired course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '82. SHIP´S COOK ACCORDING TO MARITIME LABOUR CONVENTION.docx', 'SHIP´S COOK ACCORDING TO MARITIME LABOUR CONVENTION', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '9. ELEMENTARY FIRST AID.docx', 'ELEMENTARY FIRST AID. (VI/1-3)', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'elementary-first-aid-1' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '93. HIGH VOLTAGE INSTALLATION. OPERATIONAL LEVEL.docx', 'HIGH VOLTAGE INSTALLATION. OPERATIONAL LEVEL', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To be an applicant for a certificate of competence as an officer in charge of the engine watch with or without not periodically unmanned engine-room, or have a certificate of competence as officer in charge of the engine watch with or without not periodically unmanned engine-room.","requires_file":1},{"label":"Expired course","requires_file":1}]', (SELECT id FROM products WHERE slug COLLATE utf8mb4_unicode_ci = 'high-voltage-installation-operational-level' COLLATE utf8mb4_unicode_ci LIMIT 1), 'mapped')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '94. HIGH VOLTAGE INSTALLATION. MANAGEMENTE LEVEL.docx', 'HIGH VOLTAGE INSTALLATION. MANAGEMENTE LEVEL', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"To be an applicant for a certificate of competence as a chief engineer officer and second engineer officer (first engineer officers), or have a certificate of competence as officer in charge of the engine watch.","requires_file":1},{"label":"Expired course","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '95. PLEASURE YACHT CAPTAIN.docx', 'PLEASURE YACHT CAPTAIN', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '96. ORDINARY SEAMAN.docx', 'ORDINARY SEAMAN', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

INSERT INTO requirement_imports (folder, file_name, doc_title, requirements, product_id, status)
VALUES ('R-RF', '97. RATING FORMING PART OF A NAVIGATIONAL WATCH.docx', 'RATING FORMING PART OF A NAVIGATIONAL WATCH', '[{"label":"ID \\/ PASSPORT","requires_file":1},{"label":"As per Structure of the Course Program","requires_file":0},{"label":"Good health, certified by means of a medical certificate","requires_file":1},{"label":"Expired course","requires_file":1},{"label":"Have completed the four (4) basic training courses established in the training code in section A-VI\\/1.","requires_file":1},{"label":"Elementary first aid","requires_file":1},{"label":"Fire prevention and fire fighting","requires_file":1},{"label":"Personal safety and social responsibility","requires_file":1},{"label":"Personal survival techniques","requires_file":1}]', NULL, 'pending')
ON DUPLICATE KEY UPDATE
    doc_title    = VALUES(doc_title),
    requirements = VALUES(requirements),
    product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
    status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status);

COMMIT;

-- ── Comprobación ─────────────────────────────────────────────────────
SELECT 'requisitos publicados' AS dato, COUNT(*) AS n FROM course_requirements
UNION ALL SELECT 'cursos cubiertos', COUNT(DISTINCT product_id) FROM course_requirements
UNION ALL SELECT 'documentos importados', COUNT(*) FROM requirement_imports
UNION ALL SELECT 'por revisar', COUNT(*) FROM requirement_imports WHERE status = 'pending'
UNION ALL SELECT 'sin curso asignado (slug no hallado)', COUNT(*) FROM requirement_imports WHERE status = 'mapped' AND product_id IS NULL;

-- La última fila debe ser 0. Si no, algún slug de este archivo no
-- existe en producción y ese documento quedó sin curso.
