<?php

namespace Core;

use PhpOffice\PhpWord\TemplateProcessor;

class WordGenerator
{
    /**
     * Generate Word document for satisfaction survey using existing template
     * @param array $surveyData Survey data from database
     * @return string Path to generated file
     */
    public static function generateSurveyDocument($surveyData)
    {
        $templatePath = __DIR__ . '/../public/assets/documents/F-20 Encuesta de satisfacción (ES).docx';
        
        // Check if template exists
        if (!file_exists($templatePath)) {
            error_log("Template not found at: " . $templatePath);
            // Fallback to generating document from scratch if template not found
            return self::generateFromScratch($surveyData);
        }
        
        try {
            // Load template
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Replace placeholders with survey data
            $templateProcessor->setValue('first_name', $surveyData['first_name']);
            $templateProcessor->setValue('last_name', $surveyData['last_name']);
            $templateProcessor->setValue('email', $surveyData['email']);
            $templateProcessor->setValue('course_name', $surveyData['course_name']);
            $templateProcessor->setValue('survey_date', date('d/m/Y', strtotime($surveyData['survey_date'])));
            
            // Staff attention rating
            $templateProcessor->setValue('staff_attention_rating', $surveyData['staff_attention_rating']);
            $templateProcessor->setValue('staff_attention_comments', 
                !empty($surveyData['staff_attention_comments']) ? $surveyData['staff_attention_comments'] : 'N/A');
            
            // Mark checkboxes for staff attention
            $templateProcessor->setValue('staff_check_a', strpos($surveyData['staff_attention_rating'], 'A-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('staff_check_b', strpos($surveyData['staff_attention_rating'], 'B-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('staff_check_c', strpos($surveyData['staff_attention_rating'], 'C-') === 0 ? '☑' : '☐');
            
            // Training quality rating
            $templateProcessor->setValue('training_quality_rating', $surveyData['training_quality_rating']);
            $templateProcessor->setValue('training_quality_comments', 
                !empty($surveyData['training_quality_comments']) ? $surveyData['training_quality_comments'] : 'N/A');
            
            // Mark checkboxes for training quality
            $templateProcessor->setValue('training_check_a', strpos($surveyData['training_quality_rating'], 'A-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('training_check_b', strpos($surveyData['training_quality_rating'], 'B-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('training_check_c', strpos($surveyData['training_quality_rating'], 'C-') === 0 ? '☑' : '☐');
            
            // Instructor performance rating
            $templateProcessor->setValue('instructor_performance_rating', $surveyData['instructor_performance_rating']);
            $templateProcessor->setValue('instructor_performance_comments', 
                !empty($surveyData['instructor_performance_comments']) ? $surveyData['instructor_performance_comments'] : 'N/A');
            
            // Mark checkboxes for instructor performance
            $templateProcessor->setValue('instructor_check_a', strpos($surveyData['instructor_performance_rating'], 'A-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('instructor_check_b', strpos($surveyData['instructor_performance_rating'], 'B-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('instructor_check_c', strpos($surveyData['instructor_performance_rating'], 'C-') === 0 ? '☑' : '☐');
            
            // Infrastructure rating
            $templateProcessor->setValue('infrastructure_rating', $surveyData['infrastructure_rating']);
            $templateProcessor->setValue('infrastructure_comments', 
                !empty($surveyData['infrastructure_comments']) ? $surveyData['infrastructure_comments'] : 'N/A');
            
            // Mark checkboxes for infrastructure
            $templateProcessor->setValue('infrastructure_check_a', strpos($surveyData['infrastructure_rating'], 'A-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('infrastructure_check_b', strpos($surveyData['infrastructure_rating'], 'B-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('infrastructure_check_c', strpos($surveyData['infrastructure_rating'], 'C-') === 0 ? '☑' : '☐');
            
            // Additional fields that might be in template
            $templateProcessor->setValue('student_name', $surveyData['first_name'] . ' ' . $surveyData['last_name']);
            $templateProcessor->setValue('date', date('d/m/Y'));
            $templateProcessor->setValue('current_date', date('d/m/Y'));
            
            // Save to temporary file
            $surveyId = $surveyData['id'] ?? 'temp';
            $filename = 'encuesta_satisfaccion_' . $surveyId . '_' . time() . '.docx';
            $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
            
            $templateProcessor->saveAs($filepath);
            
            return $filepath;
            
        } catch (\Exception $e) {
            error_log("Error using template: " . $e->getMessage());
            // Fallback to generating from scratch
            return self::generateFromScratch($surveyData);
        }
    }
    
    /**
     * Fallback method: Generate document from scratch if template fails
     */
    private static function generateFromScratch($surveyData)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        
        // Set document properties
        $properties = $phpWord->getDocInfo();
        $properties->setCreator('GCL International');
        $properties->setCompany('GCL International');
        $properties->setTitle('Encuesta de Satisfacción');
        
        $section = $phpWord->addSection();
        
        // Title
        $section->addText(
            'ENCUESTA DE SATISFACCIÓN',
            ['bold' => true, 'size' => 18, 'color' => '0066cc'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        
        $section->addTextBreak(1);
        
        $section->addText(
            'GCL International',
            ['bold' => true, 'size' => 14],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        
        $section->addTextBreak(2);
        
        // Student Information
        $section->addText('DATOS DEL ESTUDIANTE', ['bold' => true, 'size' => 14, 'color' => '0066cc']);
        $section->addTextBreak(1);
        
        self::addField($section, 'Nombre:', $surveyData['first_name'] . ' ' . $surveyData['last_name']);
        self::addField($section, 'Email:', $surveyData['email']);
        self::addField($section, 'Curso:', $surveyData['course_name']);
        self::addField($section, 'Fecha:', $surveyData['survey_date']);
        
        $section->addTextBreak(2);
        
        // Ratings
        $section->addText('CALIFICACIONES', ['bold' => true, 'size' => 14, 'color' => '0066cc']);
        $section->addTextBreak(1);
        
        $section->addText('1. Atención del Personal', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(0.5);
        self::addRating($section, $surveyData['staff_attention_rating']);
        if (!empty($surveyData['staff_attention_comments'])) {
            $section->addText('Comentarios:', ['italic' => true]);
            $section->addText($surveyData['staff_attention_comments'], ['size' => 10]);
        }
        $section->addTextBreak(1);
        
        $section->addText('2. Calidad del Entrenamiento', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(0.5);
        self::addRating($section, $surveyData['training_quality_rating']);
        if (!empty($surveyData['training_quality_comments'])) {
            $section->addText('Comentarios:', ['italic' => true]);
            $section->addText($surveyData['training_quality_comments'], ['size' => 10]);
        }
        $section->addTextBreak(1);
        
        $section->addText('3. Desempeño del Instructor', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(0.5);
        self::addRating($section, $surveyData['instructor_performance_rating']);
        if (!empty($surveyData['instructor_performance_comments'])) {
            $section->addText('Comentarios:', ['italic' => true]);
            $section->addText($surveyData['instructor_performance_comments'], ['size' => 10]);
        }
        $section->addTextBreak(1);
        
        // Infrastructure
        $section->addText('4. Infraestructura, Equipos y Simulador', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(0.5);
        self::addRating($section, $surveyData['infrastructure_rating']);
        if (!empty($surveyData['infrastructure_comments'])) {
            $section->addText('Comentarios:', ['italic' => true]);
            $section->addText($surveyData['infrastructure_comments'], ['size' => 10]);
        }
        
        $section->addTextBreak(2);
        
        $section->addText(
            'Documento generado automáticamente el ' . date('d/m/Y H:i:s'),
            ['size' => 9, 'color' => '666666'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        
        $surveyId = $surveyData['id'] ?? 'temp';
        $filename = 'encuesta_satisfaccion_' . $surveyId . '_' . time() . '.docx';
        $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filepath);
        
        return $filepath;
    }
    
    private static function addField($section, $label, $value)
    {
        $section->addText($label, ['bold' => true]);
        $section->addText($value, ['size' => 11]);
        $section->addTextBreak(0.5);
    }
    
    private static function addRating($section, $rating)
    {
        $color = '000000';
        if (strpos($rating, 'A-') === 0) {
            $color = '4CAF50';
        } elseif (strpos($rating, 'B-') === 0) {
            $color = 'FF9800';
        } elseif (strpos($rating, 'C-') === 0) {
            $color = 'f44336';
        }
        
        $section->addText(
            'Calificación: ' . $rating,
            ['bold' => true, 'size' => 12, 'color' => $color]
        );
    }

    /** Categoría de productos cuyos cursos usan el formulario F-41. */
    private const CATEGORIA_LOGISTICA = 10;

    /**
     * Casilla marcada y sin marcar de los formularios.
     *
     * Se usa el mismo símbolo que la plantilla ya trae para el
     * consentimiento: es el que se sabe que la fuente del documento dibuja
     * bien, y así todas las casillas del papel se ven iguales.
     */
    private const MARCA = '☑';
    private const VACIA = '☐';

    /**
     * Plantilla que corresponde al curso de una solicitud.
     *
     * Los cursos de logística y puertos usan el F-41; el resto, el F-46.
     * El curso se localiza por nombre, que es lo que guarda la solicitud.
     */
    private static function plantillaParaCurso(?string $curso): string
    {
        $docs = __DIR__ . '/../public/assets/documents/';
        $f46  = $docs . 'F-46 Solicitud y admisión de curso (ES).docx';
        $f41  = $docs . 'F-41 Inscripción para cursos logísticos y portuarios.docx';

        $curso = trim((string) $curso);
        if ($curso === '' || !file_exists($f41)) {
            return $f46;
        }

        try {
            $fila = \Core\Database::getInstance()->fetchOne(
                'SELECT category_id FROM products WHERE name = ? LIMIT 1',
                [$curso]
            );
        } catch (\Throwable $e) {
            error_log('WordGenerator: no se pudo leer la categoría del curso: ' . $e->getMessage());
            return $f46;
        }

        return (int) ($fila['category_id'] ?? 0) === self::CATEGORIA_LOGISTICA ? $f41 : $f46;
    }

    /**
     * Nombres de los cursos de una solicitud.
     *
     * Las solicitudes nuevas traen la lista en `courses`; las anteriores a
     * esa tabla sólo tienen el nombre suelto en `course`.
     *
     * @return string[]
     */
    private static function nombresDeCursos(array $data): array
    {
        if (!empty($data['courses']) && is_array($data['courses'])) {
            $nombres = array_map(
                static fn($c) => is_array($c) ? ($c['course_name'] ?? '') : (string) $c,
                $data['courses']
            );
            $nombres = array_values(array_filter(array_map('trim', $nombres)));
            if ($nombres) {
                return $nombres;
            }
        }

        $suelto = trim((string) ($data['course'] ?? ''));

        return $suelto === '' ? [] : [$suelto];
    }

    /**
     * Filas de curso de una plantilla, en el orden en que salen en el papel.
     *
     * Busca las filas de tabla que llevan la casilla y saca de cada una su
     * número, el nombre impreso y el código. Hace falta porque la plantilla
     * repite el mismo marcador en todas las filas y no hay forma de saber,
     * desde PhpWord, a qué curso corresponde cada aparición.
     *
     * @return array<array{numero:string,nombre:string,codigo:string}>
     */
    private static function filasDeCursoDeLaPlantilla(string $plantilla): array
    {
        if (!file_exists($plantilla)) {
            return [];
        }

        $zip = new \ZipArchive();
        if ($zip->open($plantilla) !== true) {
            return [];
        }
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!is_string($xml) || $xml === '') {
            return [];
        }

        /*
            Texto plano de un trozo de XML. El patrón exige espacio o cierre
            tras «w:t»: con «<w:t[^>]*>» también casarían «<w:tcPr>» y
            compañía, porque ni la c ni la P ni la r son «>».
        */
        $texto = static function (string $trozo): string {
            preg_match_all('#<w:t(?:\s[^>]*)?>(.*?)</w:t>#su', $trozo, $m);

            return trim(preg_replace(
                '/\s+/u',
                ' ',
                html_entity_decode(implode('', $m[1]), ENT_QUOTES | ENT_XML1, 'UTF-8')
            ));
        };

        preg_match_all('#<w:tr[ >].*?</w:tr>#su', $xml, $filas);

        $salida = [];
        foreach ($filas[0] as $tr) {
            if (!preg_match('/\$\{course_check_\d+\}/u', $tr)) {
                continue;
            }

            preg_match_all('#<w:tc[ >].*?</w:tc>#su', $tr, $celdas);
            $celdas = array_map($texto, $celdas[0]);

            // La columna del nombre es la más larga de la fila; así no
            // depende de que siempre sea la segunda.
            $nombre = '';
            foreach ($celdas as $c) {
                if (mb_strlen($c) > mb_strlen($nombre) && !str_contains($c, '${')) {
                    $nombre = $c;
                }
            }

            $salida[] = [
                'numero' => $celdas[0] ?? '',
                'nombre' => $nombre,
                'codigo' => $celdas[2] ?? '',
            ];
        }

        return $salida;
    }

    /**
     * Clave con la que se compara un nombre de curso.
     *
     * La plantilla escribe «English name / Nombre en español» y el catálogo
     * sólo el inglés, así que se corta por la barra. Después se quita todo
     * lo que no sea letra o número: ahí es justo donde difieren, con guiones
     * largos, paréntesis y espacios de más.
     */
    private static function claveDeCurso(string $texto): string
    {
        $texto = explode('/', $texto)[0];

        // Guiones y comillas tipográficas a su versión simple.
        $texto = str_replace(
            ["\u{2010}", "\u{2011}", "\u{2012}", "\u{2013}", "\u{2014}", "\u{2018}", "\u{2019}"],
            ['-', '-', '-', '-', '-', "'", "'"],
            $texto
        );
        $texto = mb_strtolower(trim($texto), 'UTF-8');

        // «Proficiency in Personal Survival Techniques» y «Personal Survival
        // Techniques» son el mismo curso con distinto encabezado.
        $texto = preg_replace('/^(proficiency in|training in|course on)\s+/u', '', $texto);

        return (string) preg_replace('/[^a-z0-9]+/u', '', $texto);
    }

    /**
     * ¿La fila de la plantilla es uno de los cursos solicitados?
     *
     * Se compara por nombre y no por el código de la columna: el de la
     * plantilla («SSBT», «PST») no es el de los productos («PAM-M-22»), y
     * de 36 filas sólo una casaría.
     *
     * @param string[] $elegidos
     */
    private static function filaCorrespondeACurso(string $nombreFila, array $elegidos): bool
    {
        $fila = self::claveDeCurso($nombreFila);
        if ($fila === '') {
            return false;
        }

        $claves = array_filter(array_map([self::class, 'claveDeCurso'], $elegidos));

        if (in_array($fila, $claves, true)) {
            return true;
        }

        /*
            Segunda pasada, tolerante: plantilla y catálogo tienen erratas
            que no se arreglan normalizando («Skils» por «Skills»,
            «ManagerialSkills» sin espacio). Se exige un parecido muy alto,
            porque marcar un curso que no es sería peor que no marcar nada:
            cursos distintos de la misma familia («Basic» contra «Advanced
            Training for…») se quedan muy por debajo de este umbral.
        */
        foreach ($claves as $clave) {
            similar_text($fila, $clave, $parecido);
            if ($parecido >= 95.0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Documentos de una solicitud, uno por cada formulario que haga falta.
     *
     * Casi siempre es uno solo: el F-46 tiene una casilla por curso, así que
     * varios cursos se marcan en el mismo papel. Si la solicitud mezcla
     * cursos de logística (F-41) con el resto, salen los dos formularios,
     * cada uno con los cursos que le corresponden.
     *
     * @return string[] rutas de los .docx generados
     */
    public static function generateAdmissionDocuments(array $data): array
    {
        $cursos = self::nombresDeCursos($data);
        if (!$cursos) {
            return array_filter([self::generateAdmissionDocument($data)]);
        }

        // Se agrupan por plantilla, conservando el orden de elección.
        $porPlantilla = [];
        foreach ($cursos as $nombre) {
            $porPlantilla[self::plantillaParaCurso($nombre)][] = $nombre;
        }

        $rutas = [];
        foreach ($porPlantilla as $plantilla => $suyos) {
            $parcial = $data;
            $parcial['course']  = $suyos[0];
            $parcial['courses'] = array_map(
                static fn($n) => ['course_name' => $n],
                $suyos
            );

            $ruta = self::generateAdmissionDocument($parcial);
            if ($ruta) {
                $rutas[] = $ruta;
            }
        }

        return $rutas;
    }

    /**
     * Generate Word document for admission request.
     *
     * Usa el F-41 para los cursos de logística y puertos y el F-46 para los
     * demás. Cada plantilla se rellena con los campos que ella misma
     * declara, así que no importa que no tengan los mismos.
     *
     * @param array $data Admission request data from database
     * @return string Path to generated file
     */
    public static function generateAdmissionDocument($data)
    {
        $templatePath = self::plantillaParaCurso($data['course'] ?? null);

        if (!file_exists($templatePath)) {
            return self::generateAdmissionFromScratch($data);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            // Los campos de la plantilla se leen ANTES de rellenar nada:
            // setValue los consume, y en el F-41 se rellenan todos, con lo
            // que después la lista saldría vacía y el documento se
            // descartaría por bueno que fuese.
            $allVars = $tp->getVariables();

            // El F-41 lleva el nombre del curso escrito; el F-46 lo marca
            // con casillas. setValue ignora los campos que no existan.
            // El F-41 escribe el nombre en un hueco: con varios cursos van
            // todos, separados por comas.
            $tp->setValue('course_name', implode(', ', self::nombresDeCursos($data)));

            // Nivel educativo: el F-41 lo pide, pero el formulario de
            // admisión todavía no lo recoge. Se deja en blanco para que el
            // solicitante lo complete al firmar.
            $tp->setValue('education_level', $data['education_level'] ?? '');

            // ── Personal fields ────────────────────────────────────────────
            $tp->setValue('given_name',       $data['given_name']       ?? '');
            $tp->setValue('surname',          $data['surname']          ?? '');
            $tp->setValue('passport_id',      $data['passport_id']      ?? '');
            $tp->setValue('nationality',      $data['nationality']      ?? '');
            $tp->setValue('date_of_birth',    $data['date_of_birth']    ?? '');
            $tp->setValue('country_of_birth', $data['country_of_birth'] ?? '');
            $tp->setValue('email',            $data['email']            ?? '');
            $tp->setValue('phone',            $data['phone']            ?? '');
            $tp->setValue('address',          $data['address']          ?? '');
            $tp->setValue('date',             date('d/m/Y'));
            $tp->setValue('current_date',     date('d/m/Y'));
            $tp->setValue('terms_consent',    '☑ Doy mi consentimiento y acepto los términos.');

            // Estado y fecha de la solicitud (recuadro "Para Uso de Oficina" del
            // F-46). Una solicitud recién enviada aún no tiene fila guardada
            // con estado: se presenta como pendiente y con la fecha de hoy.
            $estado = (string) ($data['status'] ?? 'pending');
            $creada = !empty($data['created_at']) ? strtotime((string) $data['created_at']) : false;
            $tp->setValue('request_status', self::etiquetaDeEstado($estado));
            $tp->setValue('request_date',   date('d/m/Y', $creada ?: time()));

            /*
                ── Casillas de curso ──────────────────────────────────────

                El F-46 trae su propia tabla con los cursos ya impresos: una
                fila por curso, con el nombre en una columna y la casilla en
                otra. Lo que NO trae es una variable por fila: las 36 filas
                repiten el mismo ${course_check_1}, y getVariables() lo
                devuelve una sola vez.

                Por eso hay que recorrer las filas de la plantilla y
                reemplazar de una en una con límite 1, que preg_replace
                aplica en orden de documento. En la casilla va sólo la marca;
                el nombre del curso ya está escrito en su columna.
            */
            $checkVars = array_values(array_unique(array_filter(
                $allVars,
                static fn($v) => str_starts_with($v, 'course_check_')
            )));
            natsort($checkVars);
            $checkVars = array_values($checkVars);

            $elegidos = self::nombresDeCursos($data);
            $filas    = self::filasDeCursoDeLaPlantilla($templatePath);

            if ($filas && count($checkVars) === 1) {
                foreach ($filas as $fila) {
                    $tp->setValue(
                        $checkVars[0],
                        self::filaCorrespondeACurso($fila['nombre'], $elegidos) ? self::MARCA : self::VACIA,
                        1
                    );
                }
            } else {
                // Plantilla con una variable por fila: se marca la que toque.
                foreach ($checkVars as $i => $var) {
                    $nombre = $filas[$i]['nombre'] ?? '';
                    $tp->setValue(
                        $var,
                        self::filaCorrespondeACurso($nombre, $elegidos) ? self::MARCA : self::VACIA
                    );
                }
            }

            if (!empty($allVars)) {
                // uniqid y no time(): una solicitud que mezcla familias genera
                // el F-41 y el F-46 en el mismo segundo, y con time() el
                // segundo documento pisaba al primero.
                $filename = 'solicitud_admision_' . ($data['id'] ?? 'temp')
                          . '_' . uniqid('', true) . '.docx';
                $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
                $tp->saveAs($filepath);
                self::marcarCasillaDeOficina($filepath, $estado);
                return $filepath;
            }
        } catch (\Exception $e) {
            error_log("Admission template error: " . $e->getMessage());
        }

        return self::generateAdmissionFromScratch($data);
    }

    /** Estado de la solicitud como se imprime en el F-46 (inglés / español). */
    private static function etiquetaDeEstado(string $estado): string
    {
        return [
            'pending'             => 'Pending / Pendiente',
            'reviewed'            => 'In review / En revisión',
            'pending_requirement' => 'Pending requirement / Pendiente de requisito',
            'approved'            => 'Approved / Aprobada',
            'rejected'            => 'Not approved / No aprobada',
        ][$estado] ?? $estado;
    }

    /**
     * Marca la casilla "Approved" o "Not Approved" del recuadro de oficina.
     *
     * Son controles de contenido (w14:checkbox), que TemplateProcessor no
     * sabe rellenar, así que se edita el XML del documento ya guardado. Los
     * otros estados no tienen casilla: se leen en la línea de estado.
     */
    private static function marcarCasillaDeOficina(string $ruta, string $estado): void
    {
        $ids = ['approved' => '958918036', 'rejected' => '-281352123'];
        if (!isset($ids[$estado])) {
            return;
        }

        $zip = new \ZipArchive();
        if ($zip->open($ruta) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');
        if ($xml !== false) {
            $patron = '#(<w:id w:val="' . preg_quote($ids[$estado], '#') . '"/><w14:checkbox><w14:checked w14:val=")0(".*?<w:t>)☐(</w:t>)#su';
            $nuevo  = preg_replace($patron, '${1}1${2}☒${3}', $xml, 1);
            if ($nuevo !== null && $nuevo !== $xml) {
                $zip->addFromString('word/document.xml', $nuevo);
            }
        }
        $zip->close();
    }

    /**
     * Fallback: generate admission document from scratch
     */
    private static function generateAdmissionFromScratch($data)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Page setup
        $section = $phpWord->addSection([
            'marginTop'    => 720,
            'marginBottom' => 720,
            'marginLeft'   => 1080,
            'marginRight'  => 1080,
        ]);

        // ── Header ──────────────────────────────────────────────────────────
        $section->addText('PANAMA MARITIME E-LEARNING (PAMEL), S.A.',
            ['bold' => true, 'size' => 14],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('QUALITY MANAGEMENT SYSTEM / SISTEMA DE GESTIÓN DE CALIDAD',
            ['size' => 9],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('FORMULARIO DE SOLICITUD Y ADMISIÓN DE CURSO',
            ['bold' => true, 'size' => 12, 'color' => '006699'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('F-46 / V.00',
            ['size' => 9, 'color' => '888888'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(1);

        // ── Applicant data ──────────────────────────────────────────────────
        $section->addText('APPLICANT INFORMATION / DATOS DEL SOLICITANTE',
            ['bold' => true, 'size' => 11, 'color' => '006699']);
        $section->addTextBreak(1);

        $fields = [
            'GIVEN NAME / NOMBRE'                  => $data['given_name']       ?? '—',
            'SURNAME / APELLIDO'                   => $data['surname']          ?? '—',
            'PASSPORT or ID / PASAPORTE o CÉDULA'  => $data['passport_id']      ?? '—',
            'NATIONALITY / NACIONALIDAD'           => $data['nationality']      ?? '—',
            'DATE OF BIRTH / FECHA DE NACIMIENTO'  => $data['date_of_birth']    ?? '—',
            'COUNTRY OF BIRTH / PAÍS DE NACIMIENTO'=> $data['country_of_birth'] ?? '—',
            'E-MAIL / CORREO'                      => $data['email']            ?? '—',
            'PHONE / TELÉFONO'                     => $data['phone']            ?? '—',
            'ADDRESS / DIRECCIÓN'                  => $data['address']          ?? '—',
        ];

        foreach ($fields as $label => $value) {
            self::addField($section, $label . ':', $value);
        }

        $section->addTextBreak(1);

        // ── Course selection (checkboxes) ───────────────────────────────────
        $section->addText('DESCRIPTION OF THE COURSE / DESCRIPCIÓN DEL CURSO',
            ['bold' => true, 'size' => 11, 'color' => '006699']);
        $section->addTextBreak(1);

        // Get course list from DB; fall back to static list
        // Una solicitud puede llevar varios cursos: se marcan todos.
        $elegidos = array_map(
            static fn($n) => mb_strtolower(trim($n), 'UTF-8'),
            self::nombresDeCursos($data)
        );
        $courses = [];

        try {
            $db = \Core\Database::getInstance();
            $rows = $db->fetchAll("SELECT name FROM products ORDER BY name");
            foreach ($rows as $row) {
                $courses[] = $row['name'];
            }
        } catch (\Exception $e) {
            // Static fallback
            $courses = [
                'Formación Básica para Operaciones de Carga en Petroleros y Quimiqueros/B',
                'Personal Safety and Social Responsibilities',
                'Safety Training For Personnel',
                'Passenger Ship Crowd Management',
                'Use of Leadership and Managerial Skills',
            ];
        }

        foreach ($courses as $courseName) {
            $marcado = in_array(mb_strtolower(trim($courseName), 'UTF-8'), $elegidos, true);
            $section->addText(
                ($marcado ? '☑' : '☐') . '  ' . $courseName,
                ['size' => 10, 'bold' => $marcado]
            );
        }

        $section->addTextBreak(2);

        // ── Consent ─────────────────────────────────────────────────────────
        $section->addText('CONSENT / CONSENTIMIENTO',
            ['bold' => true, 'size' => 11, 'color' => '006699']);
        $section->addTextBreak(1);
        
        $section->addText('☑ Doy mi consentimiento y acepto los términos.',
            ['size' => 10, 'bold' => true, 'color' => '008000']);
        $section->addText('Al enviar la solicitud, el candidato certificó que la información proporcionada es veraz, reemplazando la firma física.',
            ['size' => 9, 'italic' => true, 'color' => '666666']);

        $section->addTextBreak(2);

        // ── Footer ──────────────────────────────────────────────────────────
        $section->addText(
            'Fecha / Date: ' . date('d/m/Y') . '      |      Status: ' . ucfirst($data['status'] ?? ''),
            ['size' => 9, 'color' => '888888']
        );
        $section->addText(
            'Documento generado automáticamente / Automatically generated document',
            ['size' => 8, 'color' => 'aaaaaa', 'italic' => true],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        // uniqid y no time(): dos documentos de la misma solicitud se generan
        // en el mismo segundo y el segundo pisaría al primero.
        $filename = 'solicitud_admision_' . ($data['id'] ?? 'temp')
                  . '_' . uniqid('', true) . '.docx';
        $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filepath);

        return $filepath;
    }

    // ── Examen teórico en el formato de PAMEL ─────────────────────────────

    private const NS_W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

    /**
     * El examen que presentó un estudiante, en el formato oficial de examen
     * teórico (public/assets/documents/Examen teorico (formato).docx): datos del
     * candidato y nota final, las instrucciones, cada pregunta con sus opciones
     * y la marcada por el estudiante, y el recuadro "Para uso de oficina".
     *
     * Se parte de la plantilla y solo se rellenan sus huecos y se sustituye el
     * bloque de preguntas; el resto (logo, instrucciones, tablas, nota final)
     * es el del documento tal cual. La opción elegida va en negrita y
     * resaltada, para que se distinga también al imprimir en blanco y negro.
     *
     * @param array $e Resultado de Progress::examenDeIntento()
     * @return string Ruta del .docx generado (en la carpeta temporal)
     */
    public static function generateExamPaper(array $e): string
    {
        $plantilla = __DIR__ . '/../public/assets/documents/Examen teorico (formato).docx';
        if (!file_exists($plantilla)) {
            throw new \RuntimeException('Falta la plantilla del examen: ' . $plantilla);
        }

        $destino = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'examen_' . ($e['id'] ?? 'x') . '_' . uniqid('', true) . '.docx';
        if (!copy($plantilla, $destino)) {
            throw new \RuntimeException('No se pudo copiar la plantilla del examen.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($destino) !== true) {
            throw new \RuntimeException('No se pudo abrir la plantilla del examen.');
        }
        $xml = $zip->getFromName('word/document.xml');
        $dom = new \DOMDocument();
        if ($xml === false || !$dom->loadXML($xml)) {
            $zip->close();
            throw new \RuntimeException('La plantilla del examen no es un documento Word válido.');
        }
        $xp = new \DOMXPath($dom);
        $xp->registerNamespace('w', self::NS_W);

        $body = $xp->query('//w:body')->item(0);
        $hijos = iterator_to_array($body->childNodes);
        $tablas = array_values(array_filter($hijos, static fn($n) => $n->nodeName === 'w:tbl'));
        if (count($tablas) < 2) {
            $zip->close();
            throw new \RuntimeException('La plantilla del examen cambió: faltan sus tablas.');
        }
        [$tablaCandidato, $tablaOficina] = $tablas;

        // 1) Título: el curso
        $titulo = $xp->query('./w:p', $body)->item(0);
        $t = $xp->query('.//w:t', $titulo)->item(0);
        if ($t) {
            $t->nodeValue = mb_strtoupper((string) ($e['course_title'] ?? ''), 'UTF-8');
            foreach (iterator_to_array($xp->query('.//w:t', $titulo)) as $i => $extra) {
                if ($i > 0) { $extra->parentNode->parentNode->removeChild($extra->parentNode); }
            }
        }

        // 2) Datos del candidato (las filas de valores van bajo sus etiquetas)
        $filas = $xp->query('./w:tr', $tablaCandidato);
        $celda = static fn(int $fila, int $col) => $xp->query('./w:tc', $filas->item($fila))->item($col);
        $fecha = !empty($e['completed_at']) ? date('d/m/Y', strtotime((string) $e['completed_at'])) : date('d/m/Y');
        self::escribirEnCelda($dom, $xp, $celda(1, 0), (string) ($e['given_name'] ?? ''));
        self::escribirEnCelda($dom, $xp, $celda(1, 1), (string) ($e['surname'] ?? ''));
        // La nota va en el recuadro grande (celda combinada bajo «FINAL GRADE»), centrada
        self::escribirEnCelda($dom, $xp, $celda(1, 3), number_format((float) ($e['score'] ?? 0), 2) . ' %', true, 40, true);
        self::centrarVerticalmente($dom, $xp, $celda(1, 3));
        // Lugar y fecha, centrados verticalmente en su casilla
        self::escribirEnCelda($dom, $xp, $celda(3, 0), 'E-learning (PAMEL)');
        self::escribirEnCelda($dom, $xp, $celda(3, 1), $fecha);
        self::centrarVerticalmente($dom, $xp, $celda(3, 0));
        self::centrarVerticalmente($dom, $xp, $celda(3, 1));

        // 3) Bloque de preguntas: se sustituye todo lo que hay entre "PART A" y el recuadro de oficina
        $partA = null;
        foreach ($xp->query('./w:p', $body) as $p) {
            if (stripos(trim($p->textContent), 'PART A') === 0) { $partA = $p; break; }
        }
        if (!$partA) {
            $zip->close();
            throw new \RuntimeException('La plantilla del examen cambió: falta el encabezado "PART A".');
        }
        for ($n = $partA->nextSibling; $n && $n !== $tablaOficina; ) {
            $siguiente = $n->nextSibling;
            $body->removeChild($n);
            $n = $siguiente;
        }

        $w = self::NS_W;
        $xmlEsc = static fn(string $s) => htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $limpio = static fn(string $s) => trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($s), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        $fuente = '<w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>';

        $bloques = '';
        $num = 0;
        foreach ($e['preguntas'] ?? [] as $q) {
            $num++;
            $bloques .= '<w:p xmlns:w="' . $w . '"><w:pPr><w:keepNext/><w:spacing w:before="200" w:after="60" w:line="240" w:lineRule="auto"/></w:pPr>'
                . '<w:r><w:rPr>' . $fuente . '<w:b/><w:bCs/><w:sz w:val="24"/><w:szCs w:val="24"/></w:rPr>'
                . '<w:t xml:space="preserve">' . $num . '- ' . $xmlEsc($limpio((string) $q['texto'])) . '</w:t></w:r></w:p>';

            foreach (($q['opciones'] ?? []) as $i => $o) {
                $elegida = $q['elegida'] !== null && (int) $o['id'] === (int) $q['elegida'];
                $letra = chr(ord('a') + ($i % 26)) . '- ';
                $rpr = '<w:rPr>' . $fuente . ($elegida ? '<w:b/><w:bCs/>' : '') . '<w:sz w:val="24"/><w:szCs w:val="24"/>'
                     . ($elegida ? '<w:highlight w:val="yellow"/>' : '') . '</w:rPr>';
                $bloques .= '<w:p xmlns:w="' . $w . '"><w:pPr><w:keepLines/>' . ($i < count($q['opciones']) - 1 ? '<w:keepNext/>' : '')
                    . '<w:spacing w:after="0" w:line="240" w:lineRule="auto"/><w:ind w:left="720" w:hanging="360"/></w:pPr>'
                    . '<w:r>' . $rpr . '<w:t xml:space="preserve">' . $letra . $xmlEsc($limpio((string) $o['texto'])) . '</w:t></w:r></w:p>';
            }
        }
        // Dos párrafos vacíos antes del recuadro de oficina, que flota respecto al párrafo siguiente
        $bloques .= '<w:p xmlns:w="' . $w . '"/><w:p xmlns:w="' . $w . '"/>';

        $frag = $dom->createDocumentFragment();
        if (!$frag->appendXML($bloques)) {
            $zip->close();
            throw new \RuntimeException('No se pudieron construir las preguntas del examen.');
        }
        $body->insertBefore($frag, $tablaOficina);

        // 4) Recuadro de oficina: casilla de aprobado / no aprobado y fecha
        $id = !empty($e['passed']) ? '958918036' : '-281352123';
        foreach ($xp->query('.//w:sdt', $tablaOficina) as $sdt) {
            $ident = $xp->query('./w:sdtPr/w:id/@w:val', $sdt)->item(0);
            if ($ident && $ident->nodeValue === $id) {
                $chk = $xp->query('./w:sdtPr//*[local-name()="checked"]/@*[local-name()="val"]', $sdt)->item(0);
                if ($chk) { $chk->nodeValue = '1'; }
                $caja = $xp->query('./w:sdtContent//w:t', $sdt)->item(0);
                if ($caja) { $caja->nodeValue = '☒'; }
            }
        }
        $filasOf = $xp->query('./w:tr', $tablaOficina);
        // Conformidad del instructor, con casilla para marcarla al firmar: el documento se
        // genera solo, con lo que respondió el estudiante, y no consta que nadie lo haya revisado.
        if ($filasOf->length > 1) {
            $celdaInstructor = $xp->query('./w:tc', $filasOf->item(1))->item(0);
            $marca = !empty($e['verified_at']) ? '☒' : '☐';
            self::escribirEnCelda($dom, $xp, $celdaInstructor, $marca . ' ESTE EXAMEN HA SIDO VERIFICADO Y CUENTA CON LA CONFORMIDAD DEL INSTRUCTOR.', true, 14);
            self::escribirEnCelda($dom, $xp, $celdaInstructor, "THIS EXAM HAS BEEN VERIFIED AND HAS THE INSTRUCTOR'S APPROVAL.", false, 14);
            if (!empty($e['verified_at'])) {
                $quien = trim((string) ($e['verified_by_name'] ?? ''));
                self::escribirEnCelda($dom, $xp, $celdaInstructor, ($quien !== '' ? $quien . ' · ' : '') . date('d/m/Y', strtotime((string) $e['verified_at'])), true, 16);
            }
        }
        if ($filasOf->length > 1) {
            $celdaFecha = $xp->query('./w:tc', $filasOf->item(1))->item(2);
            if ($celdaFecha) {
                self::escribirEnCelda($dom, $xp, $celdaFecha, $fecha);
            }
        }

        $zip->addFromString('word/document.xml', $dom->saveXML());
        $zip->close();

        return $destino;
    }

    /** Escribe un texto en una celda del formato: un párrafo nuevo con la fuente del documento. */
    private static function escribirEnCelda(\DOMDocument $dom, \DOMXPath $xp, ?\DOMNode $tc, string $texto, bool $negrita = false, int $medioPunto = 20, bool $centrado = false): void
    {
        if (!$tc || $texto === '') {
            return;
        }
        $ultimo = null;
        foreach ($xp->query('./w:p', $tc) as $p) { $ultimo = $p; }
        // Si el único párrafo de la celda ya trae texto (la etiqueta), el valor va en uno nuevo debajo
        $destino = ($ultimo && trim($ultimo->textContent) === '') ? $ultimo : null;

        $w = self::NS_W;
        $xml = '<w:r xmlns:w="' . $w . '"><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/>'
             . ($negrita ? '<w:b/><w:bCs/>' : '') . '<w:sz w:val="' . $medioPunto . '"/><w:szCs w:val="' . $medioPunto . '"/></w:rPr>'
             . '<w:t xml:space="preserve">' . htmlspecialchars($texto, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</w:t></w:r>';
        $frag = $dom->createDocumentFragment();
        $frag->appendXML($xml);

        if ($destino) {
            $destino->appendChild($frag);
            $p = $destino;
        } else {
            $p = $dom->createElementNS($w, 'w:p');
            $p->appendChild($frag);
            $tc->appendChild($p);
        }

        if ($centrado) {
            $pPr = $xp->query('./w:pPr', $p)->item(0);
            if (!$pPr) {
                $pPr = $dom->createElementNS($w, 'w:pPr');
                $p->insertBefore($pPr, $p->firstChild);
            }
            foreach (iterator_to_array($xp->query('./w:jc', $pPr)) as $viejo) { $pPr->removeChild($viejo); }
            $jc = $dom->createElementNS($w, 'w:jc');
            $jc->setAttributeNS($w, 'w:val', 'center');
            $pPr->appendChild($jc);
        }
    }

    /** Centra verticalmente el contenido de una celda del formato. */
    private static function centrarVerticalmente(\DOMDocument $dom, \DOMXPath $xp, ?\DOMNode $tc): void
    {
        if (!$tc) {
            return;
        }
        $w = self::NS_W;
        $tcPr = $xp->query('./w:tcPr', $tc)->item(0);
        if (!$tcPr) {
            $tcPr = $dom->createElementNS($w, 'w:tcPr');
            $tc->insertBefore($tcPr, $tc->firstChild);
        }
        foreach (iterator_to_array($xp->query('./w:vAlign', $tcPr)) as $viejo) { $tcPr->removeChild($viejo); }
        $va = $dom->createElementNS($w, 'w:vAlign');
        $va->setAttributeNS($w, 'w:val', 'center');
        $tcPr->appendChild($va);
    }

    /**
     * Acta de un examen realizado: datos del alumno, calificación y el
     * detalle de cada pregunta con lo que respondió y lo correcto.
     *
     * @param array $datos Intento, con 'answers', 'student', 'quiz' y 'course'.
     * @return string Ruta del .docx generado, en el directorio temporal.
     */
    public static function generateQuizAttemptDocument(array $datos): string
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection([
            'marginTop' => 720, 'marginBottom' => 720,
            'marginLeft' => 1080, 'marginRight' => 1080,
        ]);

        $centro   = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];
        $aprobado = !empty($datos['passed']);

        // ── Encabezado ──────────────────────────────────────────────────
        $section->addText('PANAMA MARITIME E-LEARNING (PAMEL), S.A.', ['bold' => true, 'size' => 14], $centro);
        $section->addText('QUALITY MANAGEMENT SYSTEM / SISTEMA DE GESTIÓN DE CALIDAD', ['size' => 9], $centro);
        $section->addText('ACTA DE EVALUACIÓN / EVALUATION RECORD',
            ['bold' => true, 'size' => 12, 'color' => '006699'], $centro);
        $section->addTextBreak(1);

        // ── Datos ───────────────────────────────────────────────────────
        self::addField($section, 'Estudiante',  $datos['student_name']  ?? '');
        self::addField($section, 'Correo',      $datos['student_email'] ?? '');
        self::addField($section, 'Curso',       $datos['course_title']  ?? '');
        self::addField($section, 'Evaluación',  $datos['quiz_title']    ?? '');
        self::addField($section, 'Fecha',       !empty($datos['completed_at'])
            ? date('d/m/Y H:i', strtotime((string) $datos['completed_at'])) : '');
        $section->addTextBreak(1);

        // ── Calificación ────────────────────────────────────────────────
        $total     = count($datos['answers'] ?? []);
        $correctas = count(array_filter($datos['answers'] ?? [], fn($a) => !empty($a['is_correct'])));

        $section->addText('CALIFICACIÓN', ['bold' => true, 'size' => 11, 'color' => '006699']);
        self::addField($section, 'Puntuación',  number_format((float) ($datos['score'] ?? 0), 2) . ' %');
        self::addField($section, 'Mínimo para aprobar', number_format((float) ($datos['pass_percentage'] ?? 70), 2) . ' %');
        // Intentos importados de la plataforma anterior (o hechos cuando el examen
        // no tenía preguntas) guardan la nota pero no las respuestas: "0 de 0"
        // junto a un 100 % en un documento oficial confunde.
        self::addField(
            $section,
            'Respuestas correctas',
            $total > 0 ? $correctas . ' de ' . $total : 'No disponible (el intento conserva solo la nota)'
        );
        $section->addText(
            'RESULTADO: ' . ($aprobado ? 'APROBADO' : 'NO APROBADO'),
            ['bold' => true, 'size' => 12, 'color' => $aprobado ? '137333' : 'C5221F']
        );
        $section->addTextBreak(1);

        // ── Detalle ─────────────────────────────────────────────────────
        $section->addText('DETALLE DE RESPUESTAS', ['bold' => true, 'size' => 11, 'color' => '006699']);
        $section->addTextBreak(1);
        if ($total === 0) {
            $section->addText(
                'Este intento no conserva el detalle por pregunta (registrado antes de existir la revisión de respuestas, '
                . 'o el examen no tenía preguntas en ese momento). Solo se conservan la nota y el resultado.',
                ['italic' => true, 'size' => 10]
            );
            $section->addTextBreak(1);
        }

        $n = 0;
        foreach ($datos['answers'] ?? [] as $a) {
            $bien = !empty($a['is_correct']);
            $section->addText(
                (++$n) . '. ' . strip_tags((string) ($a['question'] ?? '')),
                ['bold' => true, 'size' => 10]
            );
            $section->addText(
                '    Respondió: ' . (trim((string) ($a['selected_text'] ?? '')) !== ''
                    ? strip_tags((string) $a['selected_text']) : '(sin responder)'),
                ['size' => 10, 'color' => $bien ? '137333' : 'C5221F']
            );
            if (!$bien) {
                $section->addText(
                    '    Correcta:  ' . strip_tags((string) ($a['correct_text'] ?? '')),
                    ['size' => 10, 'color' => '137333']
                );
            }
            $section->addTextBreak(1);
        }

        // ── Firmas ──────────────────────────────────────────────────────
        $section->addTextBreak(2);
        $section->addText('_______________________________        _______________________________', ['size' => 10], $centro);
        $section->addText('Instructor                                             Director', ['size' => 9], $centro);

        $nombre = sprintf('acta_examen_%d_%s.docx', (int) ($datos['id'] ?? 0), date('Ymd_His'));
        $ruta   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nombre;

        \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007')->save($ruta);

        return $ruta;
    }
}
