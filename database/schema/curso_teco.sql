-- ─────────────────────────────────────────────────────────────────────
-- Curso TECO — Working Into Enclosed Space
-- Generado el 2026-10-12 desde el material de la carpeta TECO/
--
-- No lleva IDs fijos: busca el producto por course_code y crea o reutiliza
-- la fila de lms_courses. Se puede ejecutar más de una vez sin duplicar.
--
-- Ejecutar con:  mysql -u USUARIO -p BASE < este_archivo.sql
-- ─────────────────────────────────────────────────────────────────────

-- El cotejo de la conexión debe coincidir con el de las tablas:
-- sin esto, comparar una variable con una columna da el error #1267.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET @code := 'TECO';

START TRANSACTION;

-- ── 1. Producto de referencia ────────────────────────────────────────
SET @product_id := (SELECT id FROM products
    WHERE course_code COLLATE utf8mb4_unicode_ci = @code COLLATE utf8mb4_unicode_ci
    LIMIT 1);

-- Diagnóstico. Si sale NO ENCONTRADO el script no escribe nada:
-- todas las inserciones van condicionadas a que exista el curso.
SELECT IF(@product_id IS NULL,
    CONCAT('NO ENCONTRADO: ningún producto con course_code = ', @code),
    CONCAT('OK: producto #', @product_id)) AS comprobacion_producto;

-- ── 2. Categoría LMS equivalente a la del producto ───────────────────
SET @category_id := (
    SELECT lc.id FROM lms_categories lc
    JOIN categories c ON UPPER(c.name) COLLATE utf8mb4_unicode_ci
                      = UPPER(lc.name) COLLATE utf8mb4_unicode_ci
    JOIN products p   ON p.category_id = c.id
    WHERE p.id = @product_id LIMIT 1
);

-- ── 3. Curso: se reutiliza si ya existe ──────────────────────────────
SET @course_id := (SELECT id FROM lms_courses WHERE product_id = @product_id LIMIT 1);

INSERT INTO lms_courses
    (product_id, course_code, category_id, title, slug, description,
     learning_objectives, level, status, pass_percentage, is_free, completion_criteria)
SELECT @product_id, @code, @category_id, p.name,
       CONCAT(p.slug, '-', LOWER(@code)), p.description,
       '["Reconocer los peligros de un espacio confinado.","Definir los requerimientos mínimos para evitar incidentes por exposición a los peligros al trabajar en espacios confinados.","Establecer los roles y responsabilidades de todos los involucrados en trabajos en espacios confinados.","Definir las responsabilidades específicas de los vigías de espacios confinados."]',
       'beginner', 'draft', 70, 0, 'quiz'
FROM products p
WHERE p.id = @product_id AND @course_id IS NULL;

SET @course_id := COALESCE(@course_id, LAST_INSERT_ID());

-- Si ya existía, se actualiza la ficha
UPDATE lms_courses SET
    course_code         = @code,
    category_id         = COALESCE(@category_id, category_id),
    learning_objectives = '["Reconocer los peligros de un espacio confinado.","Definir los requerimientos mínimos para evitar incidentes por exposición a los peligros al trabajar en espacios confinados.","Establecer los roles y responsabilidades de todos los involucrados en trabajos en espacios confinados.","Definir las responsabilidades específicas de los vigías de espacios confinados."]',
    level               = 'beginner',
    pass_percentage     = 70,
    completion_criteria = 'quiz'
WHERE id = @course_id;

-- ── 4. Lecciones ─────────────────────────────────────────────────────
-- Se reemplazan por completo para que el script sea repetible.
DELETE FROM lms_lessons WHERE course_id = @course_id;

INSERT INTO lms_lessons (course_id, title, content, type, order_num, duration, is_active)
SELECT @course_id, 'Introducción y definición de espacio confinado', '<h3>Por qué importa</h3>\n<p>Los accidentes en espacios confinados, en su mayoría mortales, tienen lugar por no reconocer\nlos peligros presentes. <strong>El 60 % de las muertes ocurren durante el auxilio inmediato a las\nprimeras víctimas</strong>: quien entra a rescatar sin preparación suele convertirse en la\nsiguiente víctima.</p>\n\n<h3>¿Qué es un espacio confinado?</h3>\n<p>Es todo aquel que cumpla con una o varias de las siguientes características:</p>\n<ol>\n  <li>No está diseñado para ser ocupado en forma continua por una o varias personas.</li>\n  <li>Cuenta con entradas y salidas limitadas.</li>\n  <li>Es lo suficientemente grande para que ingrese un trabajador y desempeñe una tarea asignada.</li>\n  <li>Dispone de ventilación natural limitada o pobre.</li>\n  <li>Existe riesgo de sofocación o intoxicación por presencia de gases.</li>\n</ol>\n\n<h3>Tamaño y ocupación</h3>\n<p>No están diseñados para ocupación continua porque:</p>\n<ul>\n  <li>Presentan atmósfera peligrosa.</li>\n  <li>Contienen material capaz de atrapar a las personas que ingresan.</li>\n  <li>Contienen material capaz de ahogar a las personas que ingresan.</li>\n  <li>Poseen una configuración interna con paredes convergentes hacia el interior o pisos inclinados hacia abajo.</li>\n</ul>\n\n<h3>Entradas, ventilación y gases</h3>\n<p><strong>Medios de entrada o salida limitados:</strong> son aquellos que no permiten una entrada\nni una salida segura y rápida de todos sus ocupantes — por ejemplo alcantarillas, espacios cuyo\ningreso se haga por escaleras, o espacios a los que se entra con arnés y líneas de elevación o\nretráctiles.</p>\n<p><strong>Atención:</strong> la aplicación de ventilación mediante aire forzado\n<em>no constituye la eliminación de los peligros</em>.</p>\n\n<h3>Espacio confinado que requiere permiso de ingreso</h3>\n<p>Requiere permiso el espacio que no está diseñado para ocupación continua, cuenta con medios de\nentrada o salida limitados, dispone de ventilación natural limitada o pobre y presenta riesgo de\nsofocación o intoxicación por gases.</p>\n\n<h3>Ejemplos</h3>\n<p>Trincheras · Túneles · Ductos · Excavaciones · Tanques · Calderas · Hornos · Alcantarillas ·\nSilos · Cámaras · Tuberías.</p>', 'text', 1, 20, 1 FROM dual WHERE @course_id IS NOT NULL;

INSERT INTO lms_lessons (course_id, title, content, type, order_num, duration, is_active)
SELECT @course_id, 'Identificación de peligros', '<h3>Peligros potenciales en los espacios confinados</h3>\n<p>Antes de autorizar cualquier ingreso hay que identificar a cuáles de estas familias de peligro\nse expone el trabajador:</p>\n<ul>\n  <li><strong>Peligros atmosféricos</strong> — deficiencia o enriquecimiento de oxígeno, gases tóxicos, gases combustibles.</li>\n  <li><strong>Peligros físicos</strong> — aprisionamiento, electrocución, caídas, riesgos posturales, problemas de comunicación, falta de iluminación, calor y ruido.</li>\n  <li><strong>Peligros de caída de material.</strong></li>\n  <li><strong>Peligros químicos corrosivos.</strong></li>\n  <li><strong>Peligros biológicos.</strong></li>\n  <li><strong>Otros peligros</strong> propios de la instalación o de la tarea.</li>\n</ul>\n<p>La identificación de peligros y la evaluación de riesgos son <em>previas</em> al ingreso, y se\ndocumentan en el permiso de entrada.</p>', 'text', 2, 10, 1 FROM dual WHERE @course_id IS NOT NULL;

INSERT INTO lms_lessons (course_id, title, content, type, order_num, duration, is_active)
SELECT @course_id, 'Peligros atmosféricos', '<h3>Qué se mide</h3>\n<ul>\n  <li>Niveles de oxígeno: rango aceptable <strong>19.5 % – 23.5 %</strong> (lo normal en aire es 21 %).</li>\n  <li>Presencia de monóxido de carbono (CO).</li>\n  <li>Presencia de sulfuro de hidrógeno (H₂S).</li>\n  <li>Presencia de otros gases tóxicos: SO₂, gases nitrosos (NO, NO₂), ácido cianhídrico (HCN).</li>\n  <li>Presencia de gases combustibles.</li>\n</ul>\n\n<h3>Deficiencia de oxígeno (&lt; 19.5 %)</h3>\n<p>Se produce por presencia de gases inertes, descomposición de materia orgánica u oxidación de\nmateria orgánica. Efectos fisiológicos según la concentración:</p>\n<table>\n  <tr><th>Oxígeno</th><th>Efecto fisiológico</th></tr>\n  <tr><td>23.5 %</td><td>Enriquecimiento de oxígeno. Peligro de incendio.</td></tr>\n  <tr><td>21 %</td><td>Nivel normal de oxígeno en el aire.</td></tr>\n  <tr><td>19.5 %</td><td>Concentración inocua mínima, no hay efectos visibles.</td></tr>\n  <tr><td>16.0 %</td><td>Incremento del ritmo respiratorio, latidos acelerados, deterioro de la atención, desorientación y pérdida de coordinación.</td></tr>\n  <tr><td>14.0 %</td><td>Juicio defectuoso, pobre coordinación muscular, fatiga rápida, respiración intermitente.</td></tr>\n  <tr><td>8.0 %</td><td>Náusea, vómito, pérdida de la capacidad de movimiento, inconsciencia.</td></tr>\n  <tr><td>&lt; 6.0 %</td><td>Dificultad de respiración, convulsiones y muerte en minutos.</td></tr>\n</table>\n\n<h3>Enriquecimiento de oxígeno (&gt; 23.5 %)</h3>\n<p>El peligro lo representa la generación de combustiones o explosiones incontroladas.</p>\n\n<h3>Monóxido de carbono</h3>\n<ul>\n  <li>200 ppm por 3 h, o 600 ppm por 1 h: dolor de cabeza y malestar.</li>\n  <li>1 000 ppm por 1 h, o 500 ppm por 30 min: latidos fuertes, dolor de cabeza, vértigo, luces destellantes, zumbido de oídos, náuseas.</li>\n  <li>1 500 ppm por 1 h: peligroso para la vida.</li>\n  <li>4 000 ppm: colapso rápido, inconsciencia y muerte en pocos minutos.</li>\n</ul>\n\n<h3>Hidrógeno sulfurado</h3>\n<ul>\n  <li>0.13 – 30 ppm: olor fuerte y desagradable.</li>\n  <li>50 – 150 ppm: pérdida temporal del olfato, irritación de vías respiratorias.</li>\n  <li>200 – 250 ppm: agravamiento rápido; posible muerte en 4 a 8 horas.</li>\n  <li>300 – 500 ppm: excitación, fuerte dolor de cabeza, pérdida del equilibrio; posible muerte en 30 min a 4 h.</li>\n  <li>800 ppm o más: toxicidad grave, parálisis respiratoria.</li>\n</ul>\n<p><strong>Ojo:</strong> a partir de 50 ppm se pierde el olfato. No se puede confiar en el olor\npara detectar H₂S.</p>\n\n<h3>Mezcla de aire y gas combustible</h3>\n<p>En una atmósfera con gases o vapores combustibles existe un rango de seguridad determinado por\nel <strong>LEL</strong> (<em>Lower Explosion Limit</em>). Por debajo del LEL no existe peligro de\ninflamación o explosión inducida o espontánea.</p>', 'text', 3, 30, 1 FROM dual WHERE @course_id IS NOT NULL;

INSERT INTO lms_lessons (course_id, title, content, type, order_num, duration, is_active)
SELECT @course_id, 'Peligros físicos y equipo de rescate', '<h3>Peligros físicos</h3>\n<p>Aprisionamiento · Electrocución · Caídas · Riesgos posturales · Problemas de comunicación ·\nFalta de iluminación · Calor · Ruido.</p>\n\n<h3>Equipo de rescate en atmósferas con valores superiores al LMP</h3>\n<p>Cuando la atmósfera supera el límite máximo permisible, el rescate exige equipo de respiración\nautónoma:</p>\n<ol>\n  <li>Máscara full face.</li>\n  <li>Cilindro de aire comprimido.</li>\n  <li>Aparato básico: arnés y soporte posterior.</li>\n  <li>Regulador de flujo a demanda.</li>\n</ol>\n\n<h3>Equipo de escape</h3>\n<ul>\n  <li>Equipo TransAir 10.</li>\n  <li>Regulador de flujo a demanda.</li>\n  <li>Capucha para escape.</li>\n</ul>\n\n<h3>Regla de oro</h3>\n<p>Nunca se improvisa un rescate. Ante una emergencia, la primera acción es\n<strong>notificar al vigía y activar el plan de rescate</strong>, nunca entrar a auxiliar sin\nequipo: es así como se produce la mayoría de las muertes.</p>', 'text', 4, 20, 1 FROM dual WHERE @course_id IS NOT NULL;

INSERT INTO lms_lessons (course_id, title, content, type, order_num, duration, is_active)
SELECT @course_id, 'Estándar y permiso de trabajo en espacios confinados', '<h3>Permiso de entrada</h3>\n<p>Ningún ingreso a un espacio confinado procede sin un <strong>permiso de entrada a espacio\nconfinado</strong> emitido y firmado. El permiso recoge la identificación de peligros, la\nevaluación de riesgos, las mediciones atmosféricas, el bloqueo de energías y el plan de rescate.</p>\n\n<h3>Antes de entrar</h3>\n<ul>\n  <li>Identificar peligros y realizar la evaluación de riesgos.</li>\n  <li>Desconectar las fuentes de energía y aplicar <strong>bloqueo y etiquetado (LOTO)</strong>.</li>\n  <li>Medir la atmósfera con un medidor multigás, verificado <em>antes de cada uso</em>.</li>\n  <li>Ventilar y volver a medir hasta alcanzar valores seguros.</li>\n  <li>Comprobar que solo ingrese <strong>personal capacitado y autorizado</strong>.</li>\n</ul>\n\n<h3>Durante el trabajo</h3>\n<ul>\n  <li>El <strong>vigía</strong> permanece en el exterior: monitorea al trabajador, mantiene comunicación constante y activa el plan de emergencia si hace falta.</li>\n  <li>Si el detector emite alarma, se <strong>sale inmediatamente</strong> del espacio.</li>\n  <li>Si la atmósfera se vuelve peligrosa, se <strong>evacúa de inmediato</strong>.</li>\n</ul>\n\n<h3>Plan de rescate</h3>\n<p>Debe incluir procedimientos claros, equipo adecuado y personal entrenado. El documento de\nreferencia es el <em>Procedimiento para Trabajo en Espacios Confinados</em> de la organización.</p>', 'text', 5, 20, 1 FROM dual WHERE @course_id IS NOT NULL;

-- ── 5. Examen teórico ────────────────────────────────────────────────
-- Se borra el examen anterior del curso con todo su contenido.
DELETE qa FROM lms_quiz_answers qa
    JOIN lms_questions q ON q.id = qa.question_id
    JOIN lms_quizzes z   ON z.id = q.quiz_id
    WHERE z.course_id = @course_id;
DELETE o FROM lms_question_options o
    JOIN lms_questions q ON q.id = o.question_id
    JOIN lms_quizzes z   ON z.id = q.quiz_id
    WHERE z.course_id = @course_id;
DELETE q FROM lms_questions q
    JOIN lms_quizzes z ON z.id = q.quiz_id
    WHERE z.course_id = @course_id;
DELETE FROM lms_quizzes WHERE course_id = @course_id;

SET @quiz_id := NULL;
INSERT INTO lms_quizzes (course_id, title, description, pass_percentage, time_limit, is_active)
SELECT @course_id, 'Examen Teórico — Espacios Confinados', 'Selección múltiple. Para aprobar se requiere un mínimo de 70 %. La evaluación práctica (uso de SCBA y técnica de evaluación de riesgo) se realiza por separado en el simulador.', 70, 40, 1 FROM dual WHERE @course_id IS NOT NULL;
SET @quiz_id := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);

-- Pregunta 1
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Qué se entiende por un “espacio confinado”?', 'multiple_choice', 5, 1 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Un lugar donde solo se puede entrar con equipo especializado', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Un espacio amplio pero sin iluminación', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Un área con ventilación limitada, difícil acceso y riesgo potencial', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Un lugar bajo tierra, como una cueva', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 2
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Cuál de los siguientes es un ejemplo de espacio confinado?', 'multiple_choice', 5, 2 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Sala de reuniones', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Oficina cerrada', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Tanque de almacenamiento', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Pasillo ventilado', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 3
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Cuál es el principal riesgo al ingresar a un espacio confinado sin la debida autorización y evaluación?', 'multiple_choice', 5, 3 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Daños a la propiedad', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Accidentes mecánicos', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Asfixia, intoxicación o muerte', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Atraso en la producción', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 4
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Qué documento se debe obtener antes de ingresar a un espacio confinado?', 'multiple_choice', 5, 4 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Manual de operaciones', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Permiso de entrada a espacio confinado', 1, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Informe de producción', 0, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Registro de mantenimiento', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 5
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Qué instrumento se utiliza para detectar gases peligrosos antes de ingresar?', 'multiple_choice', 5, 5 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Voltímetro', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Termómetro infrarrojo', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Medidor multigás (detector de atmósferas)', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Linterna de inspección', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 6
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Cuál de los siguientes elementos es parte del equipo de protección personal (EPP) para espacios confinados?', 'multiple_choice', 5, 6 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Gafas oscuras', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Mascarilla quirúrgica', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Arnés de seguridad y línea de vida', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Chaleco reflectivo', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 7
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Cuál es la función del vigía o observador externo durante la entrada a un espacio confinado?', 'multiple_choice', 5, 7 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Supervisar la hora de entrada y salida', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Asistir con herramientas desde afuera', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Monitorear al trabajador, activar planes de emergencia y comunicarse constantemente', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Verificar el cumplimiento del cronograma', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 8
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Qué acción debe tomarse si la atmósfera dentro del espacio confinado se vuelve peligrosa?', 'multiple_choice', 5, 8 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Continuar con precaución', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Llamar al supervisor y esperar instrucciones', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Evacuar inmediatamente', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Apagar todos los equipos', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 9
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, 'Cada cuánto debe revisarse el equipo de medición de gases?', 'multiple_choice', 5, 9 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Cada año', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Solo cuando falla', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Antes de cada uso', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Después de cada turno', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 10
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Qué debe incluir un plan de rescate para espacios confinados?', 'multiple_choice', 5, 10 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Lista de empleados capacitados', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Procedimientos claros, equipo adecuado y personal entrenado', 1, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Cronograma de visitas', 0, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Información de turnos laborales', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 11
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Cuál es el porcentaje mínimo aceptable de oxígeno para ingresar a un espacio confinado?', 'multiple_choice', 5, 11 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, '15.0%', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, '18.0%', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Entre 19.5% y 23.5%', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, '25.0%', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 12
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Qué significa una atmósfera deficiente de oxígeno?', 'multiple_choice', 5, 12 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Contiene demasiado aire', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Tiene menos de 19.5% de oxígeno', 1, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Tiene más de 23.5% de oxígeno', 0, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'No contiene gases inflamables', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 13
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, 'Antes de ingresar a un espacio confinado, se debe:', 'multiple_choice', 5, 13 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Ingresar rápidamente para reducir la exposición', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Verificar únicamente la iluminación', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Identificar peligros y realizar una evaluación de riesgos', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Esperar a que otro trabajador entre primero', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 14
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Quién está autorizado para ingresar a un espacio confinado?', 'multiple_choice', 5, 14 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Cualquier trabajador disponible', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Solo personal capacitado y autorizado', 1, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Personal de mantenimiento únicamente', 0, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Supervisores exclusivamente', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 15
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Qué debe hacerse con las fuentes de energía antes de ingresar a un espacio confinado?', 'multiple_choice', 5, 15 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Mantenerlas activas para facilitar el trabajo', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Desconectarlas y aplicar bloqueo y etiquetado (LOTO)', 1, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Revisarlas únicamente', 0, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Encender equipos de respaldo', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 16
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Qué tipo de gases representan riesgo de explosión en un espacio confinado?', 'multiple_choice', 5, 16 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Gases inertes', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Gases combustibles o inflamables', 1, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Oxígeno puro', 0, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Vapor de agua', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 17
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Cuál es la primera acción en caso de emergencia dentro de un espacio confinado?', 'multiple_choice', 5, 17 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Entrar inmediatamente a rescatar al trabajador', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Notificar al vigía y activar el plan de rescate', 1, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Apagar las luces', 0, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Esperar instrucciones del trabajador afectado', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 18
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Por qué no debe realizarse un rescate improvisado en un espacio confinado?', 'multiple_choice', 5, 18 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Porque retrasa la producción', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Porque puede generar más víctimas', 1, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Porque requiere autorización escrita', 0, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Porque afecta los equipos de medición', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 19
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Qué debe hacerse si el detector de gases emite una alarma?', 'multiple_choice', 5, 19 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Ignorarla si el trabajo está por terminar', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Reiniciar el equipo', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Salir inmediatamente del espacio confinado', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Continuar usando protección respiratoria', 0, 4 FROM dual WHERE @q IS NOT NULL;

-- Pregunta 20
INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
SELECT @quiz_id, '¿Cuál es el propósito de la ventilación en un espacio confinado?', 'multiple_choice', 5, 20 FROM dual WHERE @quiz_id IS NOT NULL;
SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Reducir el ruido', 0, 1 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Mejorar la iluminación', 0, 2 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Mantener una atmósfera segura para los trabajadores', 1, 3 FROM dual WHERE @q IS NOT NULL;
INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
SELECT @q, 'Facilitar el acceso', 0, 4 FROM dual WHERE @q IS NOT NULL;

COMMIT;

-- ── Comprobación ─────────────────────────────────────────────────────
SELECT c.id AS curso, c.title, c.status, c.course_code,
       (SELECT COUNT(*) FROM lms_lessons  WHERE course_id = c.id) AS lecciones,
       (SELECT COUNT(*) FROM lms_quizzes  WHERE course_id = c.id) AS examenes,
       (SELECT COUNT(*) FROM lms_questions q JOIN lms_quizzes z ON z.id = q.quiz_id
         WHERE z.course_id = c.id) AS preguntas
FROM lms_courses c WHERE c.id = @course_id;

-- El curso queda en BORRADOR. Revísalo y publícalo desde el panel.
