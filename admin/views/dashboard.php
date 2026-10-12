<?php
/**
 * Panel de inicio.
 * Los datos se consultan aquí porque el resto del panel sigue ese patrón;
 * todas las consultas son de solo lectura y están acotadas por LIMIT.
 */
use Core\Database;

$db = Database::getInstance();

$num = static fn($sql) => (int) ($db->fetchOne($sql)['c'] ?? 0);

$m = [
    'estudiantes'  => $num("SELECT COUNT(*) c FROM users WHERE role = 'student'"),
    'cursos'       => $num("SELECT COUNT(*) c FROM lms_courses WHERE status = 'published'"),
    'cursosTotal'  => $num("SELECT COUNT(*) c FROM lms_courses"),
    'inscripciones'=> $num("SELECT COUNT(*) c FROM lms_enrollments"),
    'evaluaciones' => $num("SELECT COUNT(*) c FROM lms_quizzes"),
    'preguntas'    => $num("SELECT COUNT(*) c FROM lms_questions q JOIN lms_quizzes z ON z.id = q.quiz_id"),
    'solicitudes'  => $num("SELECT COUNT(*) c FROM admission_requests WHERE status = 'pending'"),
    'instructores' => $num("SELECT COUNT(*) c FROM users WHERE role = 'teacher'"),
    'encuestas'    => $num("SELECT COUNT(*) c FROM satisfaction_surveys"),
    'intentos'     => $num("SELECT COUNT(*) c FROM lms_quiz_attempts"),
];

// Avance medio de las inscripciones activas
$avance = (float) ($db->fetchOne(
    "SELECT AVG(progress_percentage) a FROM lms_enrollments WHERE status = 'active'"
)['a'] ?? 0);

$inscripciones = $db->fetchAll(
    "SELECT e.enrolled_at, e.progress_percentage, e.status,
            u.name AS estudiante, c.title AS curso
     FROM lms_enrollments e
     JOIN users u        ON u.id = e.student_id
     JOIN lms_courses c  ON c.id = e.course_id
     ORDER BY e.enrolled_at DESC
     LIMIT 6"
);

$solicitudes = $db->fetchAll(
    "SELECT id, given_name, surname, course, status, created_at
     FROM admission_requests
     ORDER BY created_at DESC
     LIMIT 5"
);

// Cursos con más estudiantes
$topCursos = $db->fetchAll(
    "SELECT c.title, COUNT(e.id) AS alumnos
     FROM lms_courses c
     JOIN lms_enrollments e ON e.course_id = c.id
     GROUP BY c.id, c.title
     ORDER BY alumnos DESC
     LIMIT 5"
);
$maxAlumnos = max(1, (int) ($topCursos[0]['alumnos'] ?? 1));

$hora = (int) date('G');
$saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');

$fmtFecha = static function ($fecha) {
    if (!$fecha) return '—';
    $meses = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
    $t = strtotime($fecha);
    return date('d', $t) . ' ' . $meses[(int) date('n', $t)] . ' ' . date('Y', $t);
};
?>

<!-- Saludo -->
<div class="card card-pad mb-5 relative overflow-hidden"
     style="background: linear-gradient(110deg, #eaf1ff 0%, #f6f9ff 55%, #fff 100%)">
    <div class="relative z-10">
        <p class="text-sm font-semibold text-slate-500"><?= $saludo ?>, 👋</p>
        <h1 class="text-2xl font-black text-slate-800 mt-0.5">
            <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
        </h1>
        <p class="text-sm text-slate-500 mt-1.5">
            Esto es lo que ocurre hoy en la plataforma.
            <?php if ($m['solicitudes'] > 0): ?>
            <a href="/manager/admission-requests" class="font-bold text-blue-600 hover:underline">
                Tienes <?= $m['solicitudes'] ?> solicitud<?= $m['solicitudes'] === 1 ? '' : 'es' ?> por revisar.
            </a>
            <?php endif; ?>
        </p>
    </div>
    <i class="fas fa-graduation-cap absolute -right-4 -bottom-6 text-[120px] text-blue-600/8 z-0"></i>
</div>

<!-- Indicadores -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-5">
    <?php
    $indicadores = [
        ['Estudiantes',  $m['estudiantes'],   'fa-user-graduate', 'blue',    $m['instructores'] . ' instructores', 'text-slate-400', '/manager/lms/students'],
        ['Cursos',       $m['cursos'],        'fa-book-open',     'emerald', $m['cursosTotal'] . ' en total',      'text-slate-400', '/manager/lms/courses'],
        ['Inscripciones',$m['inscripciones'], 'fa-clipboard-list','violet',  round($avance) . '% de avance medio', 'text-slate-400', '/manager/lms/students'],
        ['Evaluaciones', $m['evaluaciones'],  'fa-vial-circle-check', 'amber', $m['preguntas'] . ' preguntas',     'text-slate-400', '/manager/lms/quizzes'],
        ['Solicitudes',  $m['solicitudes'],   'fa-file-signature','rose',    $m['solicitudes'] > 0 ? 'Pendientes de revisión' : 'Todo al día', $m['solicitudes'] > 0 ? 'text-rose-500' : 'text-emerald-600', '/manager/admission-requests'],
    ];
    foreach ($indicadores as [$etiqueta, $valor, $icono, $color, $pie, $pieColor, $url]):
    ?>
    <a href="<?= $url ?>" class="card card-pad hover:-translate-y-0.5 transition-transform">
        <div class="stat">
            <div class="tile tile-<?= $color ?>"><i class="fas <?= $icono ?>"></i></div>
            <div class="min-w-0">
                <p class="stat-label"><?= $etiqueta ?></p>
                <p class="stat-value"><?= number_format($valor) ?></p>
            </div>
        </div>
        <p class="stat-foot <?= $pieColor ?>"><?= htmlspecialchars($pie) ?></p>
    </a>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    <!-- Inscripciones recientes -->
    <div class="card xl:col-span-2">
        <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color: var(--card-border)">
            <h2 class="text-sm font-black text-slate-800">Inscripciones recientes</h2>
            <a href="/manager/lms/students" class="text-xs font-bold text-blue-600 hover:underline">Ver todas</a>
        </div>

        <?php if (empty($inscripciones)): ?>
        <p class="px-6 py-10 text-center text-sm text-slate-400">Todavía no hay inscripciones.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3">Estudiante</th>
                        <th class="px-6 py-3">Curso</th>
                        <th class="px-6 py-3 w-32">Avance</th>
                        <th class="px-6 py-3 text-right">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscripciones as $i): $p = (int) $i['progress_percentage']; ?>
                    <tr class="border-t hover:bg-slate-50/60 transition" style="border-color:#f3f6fb">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black flex items-center justify-center flex-shrink-0">
                                    <?= mb_strtoupper(mb_substr($i['estudiante'], 0, 2)) ?>
                                </div>
                                <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars($i['estudiante']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5">
                            <span class="text-xs text-slate-500 font-medium"><?= htmlspecialchars(mb_strimwidth($i['curso'], 0, 42, '…')) ?></span>
                        </td>
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-1.5 rounded-full <?= $p >= 100 ? 'bg-emerald-500' : 'bg-blue-500' ?>" style="width:<?= max(2, $p) ?>%"></div>
                                </div>
                                <span class="text-[11px] font-black <?= $p >= 100 ? 'text-emerald-600' : 'text-slate-400' ?>"><?= $p ?>%</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <span class="text-[11px] font-semibold text-slate-400"><?= $fmtFecha($i['enrolled_at']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Solicitudes de admisión -->
    <div class="card">
        <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color: var(--card-border)">
            <h2 class="text-sm font-black text-slate-800">Solicitudes</h2>
            <a href="/manager/admission-requests" class="text-xs font-bold text-blue-600 hover:underline">Ver todas</a>
        </div>

        <?php if (empty($solicitudes)): ?>
        <p class="px-6 py-10 text-center text-sm text-slate-400">Sin solicitudes.</p>
        <?php else: ?>
        <div class="px-6 py-2">
            <?php foreach ($solicitudes as $s):
                $pendiente = ($s['status'] ?? '') === 'pending';
            ?>
            <a href="/manager/admission-requests/<?= (int) $s['id'] ?>"
               class="flex items-start gap-3 py-3.5 border-b last:border-0 group" style="border-color:#f3f6fb">
                <span class="tile tile-<?= $pendiente ? 'amber' : 'emerald' ?>" style="width:36px;height:36px;border-radius:11px;font-size:13px">
                    <i class="fas <?= $pendiente ? 'fa-hourglass-half' : 'fa-check' ?>"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-700 truncate group-hover:text-blue-600 transition">
                        <?= htmlspecialchars(trim($s['given_name'] . ' ' . $s['surname'])) ?>
                    </p>
                    <p class="text-[11px] text-slate-400 font-medium truncate mt-0.5">
                        <?= htmlspecialchars(mb_strimwidth((string) $s['course'], 0, 34, '…')) ?>
                    </p>
                    <p class="text-[10px] font-bold <?= $pendiente ? 'text-amber-600' : 'text-emerald-600' ?> mt-1">
                        <?= $fmtFecha($s['created_at']) ?>
                    </p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    <!-- Accesos rápidos -->
    <div class="card card-pad">
        <h2 class="text-sm font-black text-slate-800 mb-4">Accesos rápidos</h2>
        <div class="grid grid-cols-2 gap-3">
            <?php
            $accesos = [
                ['Nuevo curso',   'fa-plus',           'blue',    '/manager/lms/courses/create', 'lms.courses.manage'],
                ['Inscribir',     'fa-user-plus',      'emerald', '/manager/lms/students/create','lms.students.manage'],
                ['Evaluaciones',  'fa-vial',           'amber',   '/manager/lms/quizzes',       'lms.quizzes.view'],
                ['Encuestas',     'fa-poll',           'violet',  '/manager/satisfaction-surveys','surveys.view'],
            ];
            foreach ($accesos as [$texto, $icono, $color, $url, $permiso]):
                if (!can($permiso)) continue;
            ?>
            <a href="<?= $url ?>" class="flex flex-col items-center gap-2 py-4 rounded-xl border hover:border-blue-200 hover:bg-blue-50/40 transition" style="border-color: var(--card-border)">
                <span class="tile tile-<?= $color ?>" style="width:38px;height:38px;border-radius:11px;font-size:14px">
                    <i class="fas <?= $icono ?>"></i>
                </span>
                <span class="text-[11px] font-bold text-slate-600 text-center leading-tight"><?= $texto ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Cursos con más estudiantes -->
    <div class="card card-pad xl:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-black text-slate-800">Cursos con más estudiantes</h2>
            <a href="/manager/lms/courses" class="text-xs font-bold text-blue-600 hover:underline">Ver catálogo</a>
        </div>

        <?php if (empty($topCursos)): ?>
        <p class="py-8 text-center text-sm text-slate-400">Sin datos de inscripción.</p>
        <?php else: ?>
        <div class="space-y-3.5">
            <?php
            $colores = ['bg-blue-500', 'bg-emerald-500', 'bg-violet-500', 'bg-amber-500', 'bg-sky-500'];
            foreach ($topCursos as $idx => $c):
                $ancho = round(((int) $c['alumnos'] / $maxAlumnos) * 100);
            ?>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <span class="text-xs font-bold text-slate-600 truncate pr-3"><?= htmlspecialchars(mb_strimwidth($c['title'], 0, 52, '…')) ?></span>
                    <span class="text-xs font-black text-slate-700 flex-shrink-0"><?= (int) $c['alumnos'] ?></span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-2 rounded-full <?= $colores[$idx % 5] ?>" style="width:<?= max(3, $ancho) ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<p class="text-center text-[11px] text-slate-400 font-medium mt-8">
    PAMEL — Panama Maritime E-Learning · Panel de administración
</p>
