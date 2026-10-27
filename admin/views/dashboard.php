<?php
/**
 * Panel de inicio.
 * Los datos se consultan aquí porque el resto del panel sigue ese patrón;
 * todas las consultas son de solo lectura y están acotadas por LIMIT.
 *
 * Nota de portabilidad: MariaDB rechaza un alias de agregado usado dentro
 * de una expresión en ORDER BY, así que los agregados se repiten.
 */
use Core\Database;

$db = Database::getInstance();

$num = static fn($sql, $p = []) => (int) ($db->fetchOne($sql, $p)['c'] ?? 0);

$mesIni    = date('Y-m-01 00:00:00');
$mesSig    = date('Y-m-01 00:00:00', strtotime('first day of next month'));
$mesAntIni = date('Y-m-01 00:00:00', strtotime('first day of last month'));

/** Serie diaria de los últimos N días, para las líneas de las tarjetas. */
$serie = static function (string $tabla, string $col, int $dias = 14, string $extra = '', array $params = []) use ($db): array {
    $desde = date('Y-m-d 00:00:00', strtotime('-' . ($dias - 1) . ' days'));
    $filas = $db->fetchAll(
        "SELECT DATE({$col}) d, COUNT(*) c FROM {$tabla}
          WHERE {$col} >= ? {$extra} GROUP BY DATE({$col})",
        array_merge([$desde], $params)
    );
    $mapa = [];
    foreach ($filas as $f) { $mapa[$f['d']] = (int) $f['c']; }

    $out = [];
    for ($i = $dias - 1; $i >= 0; $i--) {
        $k = date('Y-m-d', strtotime("-{$i} days"));
        $out[] = $mapa[$k] ?? 0;
    }
    return $out;
};

/**
 * Mini gráfica SVG para la esquina de cada tarjeta de color.
 * Si el periodo no tuvo movimiento la línea sería una raya plana que no
 * dice nada, así que en ese caso no se dibuja.
 */
$chispa = static function (array $v): string {
    if (count($v) < 2) { return ''; }
    $min = min($v); $max = max($v);
    if ($max === $min) { return ''; }
    $rango = $max - $min;
    $w = 86; $h = 30; $n = count($v);
    $pts = [];
    foreach (array_values($v) as $i => $y) {
        $pts[] = round($i * ($w / ($n - 1)), 1) . ',' . round($h - 2 - (($y - $min) / $rango) * ($h - 6), 1);
    }
    $linea = implode(' ', $pts);
    return '<svg class="statx-spark" viewBox="0 0 ' . $w . ' ' . $h . '" preserveAspectRatio="none" aria-hidden="true">'
         . '<polygon points="0,' . $h . ' ' . $linea . ' ' . $w . ',' . $h . '" fill="rgba(255,255,255,.20)"/>'
         . '<polyline points="' . $linea . '" fill="none" stroke="rgba(255,255,255,.9)" stroke-width="2"'
         . ' stroke-linejoin="round" stroke-linecap="round"/></svg>';
};

// ── Cifras de cabecera ───────────────────────────────────────────────
$m = [
    'insHoy'      => $num("SELECT COUNT(*) c FROM lms_enrollments WHERE DATE(enrolled_at) = CURDATE()"),
    'insMes'      => $num("SELECT COUNT(*) c FROM lms_enrollments WHERE enrolled_at >= ? AND enrolled_at < ?", [$mesIni, $mesSig]),
    'insMesAnt'   => $num("SELECT COUNT(*) c FROM lms_enrollments WHERE enrolled_at >= ? AND enrolled_at < ?", [$mesAntIni, $mesIni]),
    'insTotal'    => $num("SELECT COUNT(*) c FROM lms_enrollments"),
    'estudiantes' => $num("SELECT COUNT(*) c FROM users WHERE role = 'student'"),
    'estActivos'  => $num("SELECT COUNT(*) c FROM users WHERE role = 'student' AND status = 'active'"),
    'cursos'      => $num("SELECT COUNT(*) c FROM lms_courses WHERE status = 'published'"),
    'cursosTotal' => $num("SELECT COUNT(*) c FROM lms_courses"),
    'intentos'    => $num("SELECT COUNT(*) c FROM lms_quiz_attempts WHERE completed_at IS NOT NULL"),
    'aprobados'   => $num("SELECT COUNT(*) c FROM lms_quiz_attempts WHERE passed = 1"),
    'certis'      => $num("SELECT COUNT(*) c FROM lms_certificates"),
    'certisMes'   => $num("SELECT COUNT(*) c FROM lms_certificates WHERE issued_at >= ? AND issued_at < ?", [$mesIni, $mesSig]),
    'solicitudes' => $num("SELECT COUNT(*) c FROM admission_requests WHERE status = 'pending'"),
    'instructores'=> $num("SELECT COUNT(*) c FROM users WHERE role = 'teacher'"),
];

$avance   = (float) ($db->fetchOne("SELECT AVG(progress_percentage) a FROM lms_enrollments WHERE status = 'active'")['a'] ?? 0);
$pctAprob = $m['intentos'] > 0 ? round($m['aprobados'] / $m['intentos'] * 100) : 0;

// ── Reparto de las inscripciones por estado real ─────────────────────
// Una inscripción 'active' al 100 % cuenta como terminada aunque nadie
// la haya cerrado a mano.
$e = $db->fetchOne(
    "SELECT
        SUM(status = 'active' AND progress_percentage <= 0)                           sinIniciar,
        SUM(status = 'active' AND progress_percentage > 0 AND progress_percentage < 100) enProgreso,
        SUM(status = 'completed' OR (status = 'active' AND progress_percentage >= 100)) terminadas,
        SUM(status = 'cancelled')                                                     canceladas,
        SUM(status = 'expired')                                                       expiradas
     FROM lms_enrollments"
) ?: [];

$estados = [
    ['Sin iniciar',  (int) ($e['sinIniciar'] ?? 0), '#f59e0b'],
    ['En progreso',  (int) ($e['enProgreso'] ?? 0), '#2563eb'],
    ['Terminadas',   (int) ($e['terminadas'] ?? 0), '#10b981'],
    ['Canceladas',   (int) ($e['canceladas'] ?? 0), '#f43f5e'],
    ['Expiradas',    (int) ($e['expiradas']  ?? 0), '#94a3b8'],
];
$estados    = array_values(array_filter($estados, fn($x) => $x[1] > 0));
$totalEstad = array_sum(array_column($estados, 1));

// ── Inscripciones por categoría LMS, este mes ────────────────────────
$porCategoria = $db->fetchAll(
    "SELECT COALESCE(cat.name, 'Sin categoría') nombre, COUNT(e.id) n
       FROM lms_enrollments e
       JOIN lms_courses c       ON c.id = e.course_id
       LEFT JOIN lms_categories cat ON cat.id = c.category_id
      WHERE e.enrolled_at >= ? AND e.enrolled_at < ?
      GROUP BY cat.id, cat.name
      ORDER BY COUNT(e.id) DESC
      LIMIT 6",
    [$mesIni, $mesSig]
);
$totalCategoria = array_sum(array_map('intval', array_column($porCategoria, 'n')));

// ── Serie diaria del mes en curso: inscripciones y lecciones ─────────
$diasMes = (int) date('t');
$ejeMes  = range(1, $diasMes);
$sInscr  = array_fill(1, $diasMes, 0);
$sLecc   = array_fill(1, $diasMes, 0);

foreach ($db->fetchAll(
    "SELECT DAY(enrolled_at) d, COUNT(*) c FROM lms_enrollments
      WHERE enrolled_at >= ? AND enrolled_at < ? GROUP BY DAY(enrolled_at)", [$mesIni, $mesSig]
) as $f) { $sInscr[(int) $f['d']] = (int) $f['c']; }

foreach ($db->fetchAll(
    "SELECT DAY(completed_at) d, COUNT(*) c FROM lms_lesson_progress
      WHERE completed_at >= ? AND completed_at < ? GROUP BY DAY(completed_at)", [$mesIni, $mesSig]
) as $f) { $sLecc[(int) $f['d']] = (int) $f['c']; }

// ── Cursos con más estudiantes ───────────────────────────────────────
$topCursos = $db->fetchAll(
    "SELECT c.title, COUNT(e.id) alumnos
       FROM lms_courses c
       JOIN lms_enrollments e ON e.course_id = c.id
      GROUP BY c.id, c.title
      ORDER BY COUNT(e.id) DESC
      LIMIT 5"
);

// ── Situación del alumnado ───────────────────────────────────────────
$conInscripcion = $num("SELECT COUNT(DISTINCT student_id) c FROM lms_enrollments");
$alumnado = [
    ['Activos',            $m['estActivos'],                         'circle-check',  'tile-emerald'],
    ['Inactivos',          $m['estudiantes'] - $m['estActivos'],     'circle-pause',  'tile-amber'],
    ['Con inscripción',    $conInscripcion,                          'user-check',    'tile-blue'],
    ['Sin inscripción',    max(0, $m['estudiantes'] - $conInscripcion), 'user-x', 'tile-rose'],
    ['Con certificado',    $num("SELECT COUNT(DISTINCT student_id) c FROM lms_certificates"), 'award', 'tile-violet'],
];

// ── Inscripciones más paradas ────────────────────────────────────────
$paradas = $db->fetchAll(
    "SELECT u.name estudiante, c.title curso, e.progress_percentage p,
            DATEDIFF(NOW(), COALESCE(e.last_accessed_at, e.enrolled_at)) dias
       FROM lms_enrollments e
       JOIN users u       ON u.id = e.student_id
       JOIN lms_courses c ON c.id = e.course_id
      WHERE e.status = 'active' AND e.progress_percentage < 100
      ORDER BY COALESCE(e.last_accessed_at, e.enrolled_at) ASC
      LIMIT 6"
);

// ── Listados de apoyo ────────────────────────────────────────────────
$inscripciones = $db->fetchAll(
    "SELECT e.enrolled_at, e.progress_percentage, u.name estudiante, c.title curso
       FROM lms_enrollments e
       JOIN users u       ON u.id = e.student_id
       JOIN lms_courses c ON c.id = e.course_id
      ORDER BY e.enrolled_at DESC
      LIMIT 6"
);

$solicitudes = $db->fetchAll(
    "SELECT id, given_name, surname, course, status, created_at
       FROM admission_requests ORDER BY created_at DESC LIMIT 5"
);

$hora   = (int) date('G');
$saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');

$meses = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
$fmtFecha = static function ($fecha) use ($meses) {
    if (!$fecha) { return '—'; }
    $t = strtotime($fecha);
    return date('d', $t) . ' ' . $meses[(int) date('n', $t)] . ' ' . date('Y', $t);
};
$mesActual = ucfirst($meses[(int) date('n')]) . ' ' . date('Y');

// ── Tarjetas de color ────────────────────────────────────────────────
$tarjetas = [
    ['Inscripciones hoy', $m['insHoy'], 'user-plus', 'sx-indigo',
     number_format($m['insMes']) . ' en ' . $mesActual,
     $serie('lms_enrollments', 'enrolled_at'), '/manager/lms/students'],

    ['Inscripciones del mes', $m['insMes'], 'chart-column', 'sx-emerald',
     number_format($m['insMesAnt']) . ' el mes anterior',
     $serie('lms_enrollments', 'enrolled_at', 30), '/manager/lms/seguimiento'],

    ['Estudiantes', $m['estudiantes'], 'users', 'sx-amber',
     number_format($m['estActivos']) . ' activos',
     $serie('users', 'created_at', 14, "AND role = 'student'"), '/manager/lms/students'],

    ['Cursos publicados', $m['cursos'], 'book-open', 'sx-blue',
     number_format($m['cursosTotal']) . ' en total',
     $serie('lms_courses', 'created_at', 30), '/manager/lms/courses'],

    ['Exámenes realizados', $m['intentos'], 'flask-conical', 'sx-rose',
     number_format($m['aprobados']) . ' aprobados (' . $pctAprob . '%)',
     $serie('lms_quiz_attempts', 'completed_at', 14, 'AND completed_at IS NOT NULL'), '/manager/lms/quizzes'],

    ['Certificados', $m['certis'], 'award', 'sx-teal',
     number_format($m['certisMes']) . ' este mes',
     $serie('lms_certificates', 'issued_at', 30), '/manager/lms/students'],
];
?>

<!-- Cabecera -->
<div class="flex items-start justify-between gap-4 mb-5 flex-wrap">
    <div>
        <h1 class="text-2xl font-black text-slate-800">Dashboard</h1>
        <p class="text-sm text-slate-500 mt-0.5">
            <?= $saludo ?>, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> — resumen general de la operación.
            <?php if ($m['solicitudes'] > 0): ?>
            <a href="/manager/admission-requests" class="font-bold text-blue-600 hover:underline">
                Tienes <?= $m['solicitudes'] ?> solicitud<?= $m['solicitudes'] === 1 ? '' : 'es' ?> por revisar.
            </a>
            <?php endif; ?>
        </p>
    </div>
    <?php if (can('lms.courses.manage')): ?>
    <a href="/manager/lms/courses/create" class="btn-primario"><i data-lucide="plus" class="ic ic-16"></i> Nuevo curso</a>
    <?php endif; ?>
</div>

<!-- Indicadores -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6 gap-4 mb-5">
    <?php foreach ($tarjetas as [$etiqueta, $valor, $icono, $color, $pie, $datos, $url]): ?>
    <a href="<?= $url ?>" class="statx <?= $color ?>">
        <div class="flex items-start justify-between gap-2">
            <p class="statx-label"><?= $etiqueta ?></p>
            <span class="statx-ico"><i data-lucide="<?= $icono ?>" class="ic ic-16"></i></span>
        </div>
        <div class="flex items-end justify-between gap-2">
            <p class="statx-value"><?= number_format($valor) ?></p>
            <?= $chispa($datos) ?>
        </div>
        <p class="statx-foot"><?= htmlspecialchars($pie) ?></p>
    </a>
    <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    <!-- Actividad del mes -->
    <div class="card card-pad">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-sm font-black text-slate-800">Actividad del mes</h2>
            <span class="text-[11px] font-bold text-slate-400"><?= $mesActual ?></span>
        </div>
        <div class="flex items-center gap-4 mb-2">
            <span class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>Inscripciones
            </span>
            <span class="flex items-center gap-1.5 text-[11px] font-bold text-slate-500">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Lecciones completadas
            </span>
        </div>
        <div style="height:228px"><canvas id="grafActividad"></canvas></div>
    </div>

    <!-- Inscripciones por estado -->
    <div class="card card-pad flex flex-col">
        <h2 class="text-sm font-black text-slate-800 mb-3">Inscripciones por estado</h2>
        <?php if ($totalEstad === 0): ?>
        <p class="py-16 text-center text-sm text-slate-400">Todavía no hay inscripciones.</p>
        <?php else: ?>
        <div class="flex-1 flex items-center gap-5">
            <div class="relative flex-shrink-0" style="width:150px;height:150px">
                <canvas id="grafEstados"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-xl font-black text-slate-800"><?= number_format($totalEstad) ?></span>
                    <span class="text-[10px] font-bold text-slate-400">inscripciones</span>
                </div>
            </div>
            <div class="flex-1 min-w-0 space-y-2.5">
                <?php foreach ($estados as [$nombre, $n, $hex]): ?>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:<?= $hex ?>"></span>
                    <span class="text-xs font-semibold text-slate-600 truncate"><?= $nombre ?></span>
                    <span class="ml-auto text-xs font-black text-slate-700"><?= round($n / $totalEstad * 100) ?>%</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Por categoría -->
    <div class="card card-pad flex flex-col">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-black text-slate-800">Por categoría</h2>
            <span class="text-[11px] font-bold text-slate-400">este mes</span>
        </div>

        <?php if (empty($porCategoria)): ?>
        <p class="py-14 text-center text-sm text-slate-400">Sin inscripciones este mes.</p>
        <?php else: ?>
        <?php
        $tilesCat = ['tile-blue', 'tile-emerald', 'tile-violet', 'tile-amber', 'tile-rose', 'tile-sky'];
        foreach ($porCategoria as $idx => $c): $n = (int) $c['n'];
        ?>
        <div class="flex items-center gap-3 py-2.5 border-b last:border-0" style="border-color:#f3f6fb">
            <span class="tile <?= $tilesCat[$idx % 6] ?>" style="width:34px;height:34px;border-radius:10px;font-size:13px">
                <i data-lucide="layers" class="ic ic-16"></i>
            </span>
            <span class="text-xs font-semibold text-slate-600 truncate flex-1"><?= htmlspecialchars($c['nombre']) ?></span>
            <span class="text-sm font-black text-slate-800"><?= number_format($n) ?></span>
            <span class="text-[11px] font-bold text-slate-400 w-10 text-right">
                <?= $totalCategoria > 0 ? round($n / $totalCategoria * 100) : 0 ?>%
            </span>
        </div>
        <?php endforeach; ?>
        <div class="flex-1"></div>
        <div class="flex items-center justify-between pt-3 mt-1 border-t" style="border-color:#f3f6fb">
            <span class="text-xs font-black text-slate-700">Total del mes</span>
            <span class="text-base font-black text-emerald-600"><?= number_format($totalCategoria) ?></span>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    <!-- Top 5 cursos -->
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color: var(--card-border)">
            <h2 class="text-sm font-black text-slate-800">Top 5 cursos</h2>
            <a href="/manager/lms/courses" class="text-xs font-bold text-blue-600 hover:underline">Catálogo</a>
        </div>
        <?php if (empty($topCursos)): ?>
        <p class="px-5 py-12 text-center text-sm text-slate-400">Sin datos de inscripción.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-[9.5px] font-black text-slate-400 uppercase tracking-wider">
                    <th class="pl-5 pr-2 py-2.5 text-left w-8">#</th>
                    <th class="px-2 py-2.5 text-left">Curso</th>
                    <th class="pr-5 pl-2 py-2.5 text-right">Alumnos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topCursos as $i => $c): ?>
                <tr class="border-t hover:bg-slate-50/60 transition" style="border-color:#f3f6fb">
                    <td class="pl-5 pr-2 py-3 text-xs font-black text-slate-300"><?= $i + 1 ?></td>
                    <td class="px-2 py-3 text-xs font-semibold text-slate-600">
                        <?= htmlspecialchars(mb_strimwidth($c['title'], 0, 44, '…')) ?>
                    </td>
                    <td class="pr-5 pl-2 py-3 text-right text-sm font-black text-slate-800"><?= (int) $c['alumnos'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Situación del alumnado -->
    <div class="card card-pad">
        <h2 class="text-sm font-black text-slate-800 mb-1">Situación del alumnado</h2>
        <?php foreach ($alumnado as [$nombre, $n, $icono, $tile]): ?>
        <div class="flex items-center gap-3 py-2.5 border-b last:border-0" style="border-color:#f3f6fb">
            <span class="tile <?= $tile ?>" style="width:32px;height:32px;border-radius:9px;font-size:12px">
                <i data-lucide="<?= $icono ?>" class="ic ic-16"></i>
            </span>
            <span class="text-xs font-semibold text-slate-600 flex-1"><?= $nombre ?></span>
            <span class="text-sm font-black text-slate-800"><?= number_format($n) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Más tiempo sin avanzar -->
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color: var(--card-border)">
            <h2 class="text-sm font-black text-slate-800">
                <i data-lucide="clock" class="ic ic-16 text-amber-400 inline-block align-[-3px] mr-1"></i>Más tiempo sin avanzar
            </h2>
            <a href="/manager/lms/seguimiento" class="text-xs font-bold text-blue-600 hover:underline">Seguimiento</a>
        </div>
        <?php if (empty($paradas)): ?>
        <p class="px-5 py-12 text-center text-sm text-slate-400">Nadie con la matrícula parada.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-[9.5px] font-black text-slate-400 uppercase tracking-wider">
                    <th class="pl-5 pr-2 py-2.5 text-left">Estudiante</th>
                    <th class="px-2 py-2.5 text-left">Curso</th>
                    <th class="pr-5 pl-2 py-2.5 text-right">Inactivo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paradas as $p):
                    $d = (int) $p['dias'];
                    $tono = $d >= 180 ? 'bg-rose-100 text-rose-700'
                          : ($d >= 90 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500');
                ?>
                <tr class="border-t hover:bg-slate-50/60 transition" style="border-color:#f3f6fb">
                    <td class="pl-5 pr-2 py-3 text-xs font-bold text-slate-700">
                        <?= htmlspecialchars(mb_strimwidth((string) $p['estudiante'], 0, 20, '…')) ?>
                    </td>
                    <td class="px-2 py-3 text-[11px] font-medium text-slate-400">
                        <?= htmlspecialchars(mb_strimwidth((string) $p['curso'], 0, 24, '…')) ?>
                    </td>
                    <td class="pr-5 pl-2 py-3 text-right">
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-black <?= $tono ?>">
                            <?= $d ?> días
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-5">

    <!-- Inscripciones recientes -->
    <div class="card xl:col-span-2">
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color: var(--card-border)">
            <h2 class="text-sm font-black text-slate-800">Inscripciones recientes</h2>
            <a href="/manager/lms/students" class="text-xs font-bold text-blue-600 hover:underline">Ver todas</a>
        </div>

        <?php if (empty($inscripciones)): ?>
        <p class="px-5 py-10 text-center text-sm text-slate-400">Todavía no hay inscripciones.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[9.5px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="px-5 py-2.5">Estudiante</th>
                        <th class="px-5 py-2.5">Curso</th>
                        <th class="px-5 py-2.5 w-32">Avance</th>
                        <th class="px-5 py-2.5 text-right">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscripciones as $i): $p = (int) $i['progress_percentage']; ?>
                    <tr class="border-t hover:bg-slate-50/60 transition" style="border-color:#f3f6fb">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black flex items-center justify-center flex-shrink-0">
                                    <?= mb_strtoupper(mb_substr($i['estudiante'], 0, 2)) ?>
                                </div>
                                <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars($i['estudiante']) ?></span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="text-xs text-slate-500 font-medium"><?= htmlspecialchars(mb_strimwidth($i['curso'], 0, 38, '…')) ?></span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-1.5 rounded-full <?= $p >= 100 ? 'bg-emerald-500' : 'bg-blue-500' ?>" style="width:<?= max(2, $p) ?>%"></div>
                                </div>
                                <span class="text-[11px] font-black <?= $p >= 100 ? 'text-emerald-600' : 'text-slate-400' ?>"><?= $p ?>%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-right">
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
        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color: var(--card-border)">
            <h2 class="text-sm font-black text-slate-800">Solicitudes</h2>
            <a href="/manager/admission-requests" class="text-xs font-bold text-blue-600 hover:underline">Ver todas</a>
        </div>

        <?php if (empty($solicitudes)): ?>
        <p class="px-5 py-10 text-center text-sm text-slate-400">Sin solicitudes.</p>
        <?php else: ?>
        <div class="px-5 py-1">
            <?php foreach ($solicitudes as $s): $pendiente = ($s['status'] ?? '') === 'pending'; ?>
            <a href="/manager/admission-requests/<?= (int) $s['id'] ?>"
               class="flex items-start gap-3 py-3 border-b last:border-0 group" style="border-color:#f3f6fb">
                <span class="tile tile-<?= $pendiente ? 'amber' : 'emerald' ?>" style="width:34px;height:34px;border-radius:10px;font-size:12px">
                    <i data-lucide="<?= $pendiente ? 'hourglass' : 'check' ?>" class="ic ic-16"></i>
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-slate-700 truncate group-hover:text-blue-600 transition">
                        <?= htmlspecialchars(trim($s['given_name'] . ' ' . $s['surname'])) ?>
                    </p>
                    <p class="text-[11px] text-slate-400 font-medium truncate mt-0.5">
                        <?= htmlspecialchars(mb_strimwidth((string) $s['course'], 0, 32, '…')) ?>
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

<!-- Accesos rápidos -->
<div class="card card-pad">
    <h2 class="text-sm font-black text-slate-800 mb-4">Accesos rápidos</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <?php
        $accesos = [
            ['Nuevo curso',  'plus',              'blue',    '/manager/lms/courses/create',   'lms.courses.manage'],
            ['Inscribir',    'user-plus',         'emerald', '/manager/lms/students/create',  'lms.students.manage'],
            ['Evaluaciones', 'flask-conical',              'amber',   '/manager/lms/quizzes',          'lms.quizzes.view'],
            ['Seguimiento',  'chart-line',        'sky',     '/manager/lms/seguimiento',      'lms.students.view'],
            ['Encuestas',    'clipboard-list',              'violet',  '/manager/satisfaction-surveys', 'surveys.view'],
            ['Solicitudes',  'file-signature',    'rose',    '/manager/admission-requests',   'admissions.view'],
        ];
        foreach ($accesos as [$texto, $icono, $color, $url, $permiso]):
            if (!can($permiso)) { continue; }
        ?>
        <a href="<?= $url ?>" class="flex flex-col items-center gap-2 py-4 rounded-xl border hover:border-blue-200 hover:bg-blue-50/40 transition" style="border-color: var(--card-border)">
            <span class="tile tile-<?= $color ?>" style="width:38px;height:38px;border-radius:11px;font-size:14px">
                <i data-lucide="<?= $icono ?>" class="ic ic-16"></i>
            </span>
            <span class="text-[11px] font-bold text-slate-600 text-center leading-tight"><?= $texto ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<p class="text-center text-[11px] text-slate-400 font-medium mt-8">
    PAMEL — Panama Maritime E-Learning · Panel de administración
</p>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    if (typeof Chart === 'undefined') { return; }

    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size   = 10;
    Chart.defaults.color       = '#94a3b8';

    // ── Actividad del mes ────────────────────────────────────────────
    const ctx = document.getElementById('grafActividad');
    if (ctx) {
        const degradado = (c, color) => {
            const g = c.createLinearGradient(0, 0, 0, 220);
            g.addColorStop(0, color + '38');
            g.addColorStop(1, color + '00');
            return g;
        };
        const lienzo = ctx.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($ejeMes) ?>,
                datasets: [
                    {
                        label: 'Inscripciones',
                        data: <?= json_encode(array_values($sInscr)) ?>,
                        borderColor: '#2563eb',
                        backgroundColor: degradado(lienzo, '#2563eb'),
                        fill: true, tension: .35, borderWidth: 2,
                        pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: '#2563eb'
                    },
                    {
                        label: 'Lecciones completadas',
                        data: <?= json_encode(array_values($sLecc)) ?>,
                        borderColor: '#10b981',
                        backgroundColor: degradado(lienzo, '#10b981'),
                        fill: true, tension: .35, borderWidth: 2,
                        pointRadius: 0, pointHoverRadius: 4, pointHoverBackgroundColor: '#10b981'
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a', padding: 10, cornerRadius: 8,
                        titleFont: { size: 11, weight: '700' }, bodyFont: { size: 11 },
                        callbacks: { title: it => 'Día ' + it[0].label }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 16 } },
                    y: { beginAtZero: true, border: { display: false },
                         grid: { color: '#f1f5f9' }, ticks: { precision: 0, maxTicksLimit: 6 } }
                }
            }
        });
    }

    // ── Inscripciones por estado ─────────────────────────────────────
    const ctxE = document.getElementById('grafEstados');
    if (ctxE) {
        new Chart(ctxE, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($estados, 0)) ?>,
                datasets: [{
                    data: <?= json_encode(array_map('intval', array_column($estados, 1))) ?>,
                    backgroundColor: <?= json_encode(array_column($estados, 2)) ?>,
                    borderWidth: 0, hoverOffset: 6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8,
                               bodyFont: { size: 11 } }
                }
            }
        });
    }
})();
</script>
