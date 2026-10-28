<?php
/**
 * Listado de encuestas de satisfacción.
 *
 * Cuatro columnas de valoración más el curso: el ancho manda. El relleno es
 * corto, los textos largos se cortan con puntos suspensivos y el completo
 * queda en el title.
 *
 * Valores por defecto arriba para que la vista se dibuje aunque el
 * controlador no mande las filas por pantalla.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$success = $_SESSION['success'] ?? '';
$error   = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$surveys        = $surveys ?? [];
$courses        = $courses ?? [];
$filters        = $filters ?? ['course' => '', 'rating' => '', 'date_from' => '', 'date_to' => ''];
$total          = (int) ($total ?? 0);
$page           = (int) ($page ?? 1);
$totalPages     = (int) ($totalPages ?? 1);
$perPage        = (int) ($perPage ?? 20);
$perPageOptions = $perPageOptions ?? [10, 20, 50, 100];

/**
 * Pastilla de valoración.
 *
 * Se pinta sólo la palabra ("A-Buena" → "Buena"): con cuatro columnas de
 * valoración, el prefijo de letra repetido ocupaba un ancho que hacía
 * falta para el nombre del curso. El valor completo queda en el title.
 */
function getRatingBadge($rating) {
    $estilos = [
        'A-Buena'   => ['p-verde', 'Buena'],
        'B-Regular' => ['p-ambar', 'Regular'],
        'C-Mala'    => ['p-rojo',  'Mala'],
    ];
    [$clase, $texto] = $estilos[$rating] ?? ['p-gris', $rating ?: '—'];

    return sprintf(
        '<span class="pastilla %s" title="%s">%s</span>',
        $clase,
        htmlspecialchars((string) $rating),
        htmlspecialchars((string) $texto)
    );
}

/** URL conservando filtros y filas por pantalla. */
$enlace = static function (int $p) use ($filters, $perPage): string {
    return '/manager/satisfaction-surveys?' . http_build_query(array_filter([
        'page'      => $p,
        'course'    => $filters['course'],
        'rating'    => $filters['rating'],
        'date_from' => $filters['date_from'],
        'date_to'   => $filters['date_to'],
        'per_page'  => $perPage,
    ], static fn($v) => $v !== '' && $v !== null));
};

$hayFiltros = $filters['course'] || $filters['rating'] || $filters['date_from'] || $filters['date_to'];
$primero = $total === 0 ? 0 : (($page - 1) * $perPage) + 1;
$ultimo  = min($page * $perPage, $total);

// Reparto de valoraciones de la página visible, para los recuadros.
$buenas   = 0; $regulares = 0; $malas = 0;
$columnas = ['staff_attention_rating', 'training_quality_rating',
             'instructor_performance_rating', 'infrastructure_rating'];
foreach ($surveys as $s) {
    foreach ($columnas as $c) {
        if (($s[$c] ?? '') === 'A-Buena')   { $buenas++; }
        if (($s[$c] ?? '') === 'B-Regular') { $regulares++; }
        if (($s[$c] ?? '') === 'C-Mala')    { $malas++; }
    }
}
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Encuestas de Satisfacción</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> encuesta<?= $total === 1 ? '' : 's' ?> encontrada<?= $total === 1 ? '' : 's' ?>
        </p>
    </div>
</div>

<?php if ($success): ?>
<div class="card card-pad mb-4 flex items-center gap-3" style="border-color:#bbf7d0; background:#f0fdf4">
    <i data-lucide="circle-check" class="ic ic-17 text-emerald-600 flex-shrink-0"></i>
    <p class="text-[12.5px] font-bold text-emerald-800"><?= htmlspecialchars($success) ?></p>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="card card-pad mb-4 flex items-center gap-3" style="border-color:#fecaca; background:#fef2f2">
    <i data-lucide="circle-alert" class="ic ic-17 text-rose-600 flex-shrink-0"></i>
    <p class="text-[12.5px] font-bold text-rose-800"><?= htmlspecialchars($error) ?></p>
</div>
<?php endif; ?>

<div class="card overflow-hidden">

    <!-- Barra de filtros -->
    <form method="GET" action="/manager/satisfaction-surveys"
          class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <div>
            <label class="filtro-rot" for="f-curso">Curso</label>
            <select id="f-curso" name="course" class="campo" onchange="this.form.submit()" style="max-width:300px">
                <option value="">Todos los cursos</option>
                <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course['course_name'], ENT_QUOTES) ?>"
                        <?= $filters['course'] === $course['course_name'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($course['course_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="filtro-rot" for="f-calif">Calificación</label>
            <select id="f-calif" name="rating" class="campo" onchange="this.form.submit()">
                <option value="">Todas las calificaciones</option>
                <option value="A-Buena"   <?= $filters['rating'] === 'A-Buena'   ? 'selected' : '' ?>>A — Buena</option>
                <option value="B-Regular" <?= $filters['rating'] === 'B-Regular' ? 'selected' : '' ?>>B — Regular</option>
                <option value="C-Mala"    <?= $filters['rating'] === 'C-Mala'    ? 'selected' : '' ?>>C — Mala</option>
            </select>
        </div>

        <div>
            <label class="filtro-rot" for="f-desde">Desde</label>
            <input type="date" id="f-desde" name="date_from" class="campo"
                   value="<?= htmlspecialchars($filters['date_from'], ENT_QUOTES) ?>">
        </div>

        <div>
            <label class="filtro-rot" for="f-hasta">Hasta</label>
            <input type="date" id="f-hasta" name="date_to" class="campo"
                   value="<?= htmlspecialchars($filters['date_to'], ENT_QUOTES) ?>">
        </div>

        <button type="submit" class="btn-sec">
            <i data-lucide="search" class="ic ic-14"></i> Filtrar
        </button>

        <?php if ($hayFiltros): ?>
        <a href="/manager/satisfaction-surveys" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Limpiar
        </a>
        <?php endif; ?>

        <input type="hidden" name="per_page" value="<?= $perPage ?>">
    </form>

    <!-- Reparto de valoraciones de esta página -->
    <div class="px-4 py-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="kpi">
            <div class="kpi-label">Encuestas</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Valoraciones buenas</div>
            <div class="kpi-value"><?= number_format($buenas) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Regulares</div>
            <div class="kpi-value"><?= number_format($regulares) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Malas</div>
            <div class="kpi-value"><?= number_format($malas) ?></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Estudiante</th>
                    <th>Curso</th>
                    <th class="centro">Personal</th>
                    <th class="centro">Calidad</th>
                    <th class="centro">Instruct.</th>
                    <th class="centro">Infraest.</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($surveys)): ?>
                <tr>
                    <td colspan="8" class="vacio">
                        <i data-lucide="inbox" class="ic"></i>
                        <p>No se encontraron encuestas.</p>
                        <?php if ($hayFiltros): ?>
                        <a href="/manager/satisfaction-surveys" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Limpiar filtros
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($surveys as $survey):
                    $nombre = trim($survey['first_name'] . ' ' . $survey['last_name']);
                    $curso  = (string) $survey['course_name'];
                    $fecha  = strtotime($survey['survey_date']);
                ?>
                <tr>
                    <td class="text-slate-400 text-[11.5px] whitespace-nowrap" title="<?= date('d/m/Y', $fecha) ?>">
                        <?= date('d/m/Y', $fecha) ?>
                    </td>
                    <td>
                        <div class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:200px"
                             title="<?= htmlspecialchars($nombre, ENT_QUOTES) ?>">
                            <?= htmlspecialchars($nombre) ?>
                        </div>
                        <a href="mailto:<?= htmlspecialchars($survey['email'], ENT_QUOTES) ?>"
                           class="block text-[11px] font-semibold text-slate-400 truncate hover:text-blue-600"
                           style="max-width:200px" title="<?= htmlspecialchars($survey['email'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars($survey['email']) ?>
                        </a>
                    </td>
                    <td>
                        <div class="truncate" style="max-width:230px" title="<?= htmlspecialchars($curso, ENT_QUOTES) ?>">
                            <?= htmlspecialchars($curso) ?>
                        </div>
                    </td>
                    <td class="centro"><?= getRatingBadge($survey['staff_attention_rating']) ?></td>
                    <td class="centro"><?= getRatingBadge($survey['training_quality_rating']) ?></td>
                    <td class="centro"><?= getRatingBadge($survey['instructor_performance_rating']) ?></td>
                    <td class="centro"><?= getRatingBadge($survey['infrastructure_rating']) ?></td>
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/satisfaction-surveys/<?= $survey['id'] ?>" class="ib ib-azul" title="Ver detalles">
                                <i data-lucide="eye" class="ic ic-14"></i>
                            </a>
                            <a href="/manager/satisfaction-surveys/<?= $survey['id'] ?>/download-word"
                               class="ib ib-verde" title="Descargar Word">
                                <i data-lucide="file-text" class="ic ic-14"></i>
                            </a>
                            <button onclick="deleteSurvey(<?= $survey['id'] ?>)" class="ib ib-rojo" title="Eliminar encuesta">
                                <i data-lucide="trash-2" class="ic ic-14"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!--
        Pie con el recuento y el paginador. La ventana de páginas sigue a la
        actual en vez de pintarlas todas en una fila.
    -->
    <?php
    $desde = max(1, min($page - 2, $totalPages - 4));
    $hasta = min($totalPages, max($page + 2, 5));
    ?>
    <div class="px-4 py-3.5 border-t flex flex-wrap items-center justify-between gap-3"
         style="border-color: var(--card-border)">

        <div class="flex items-center gap-3 flex-wrap">
            <p class="text-[12px] font-semibold text-slate-400">
                <?php if ($total): ?>
                Mostrando <?= number_format($primero) ?>-<?= number_format($ultimo) ?> de <?= number_format($total) ?>
                <?php else: ?>
                Sin resultados
                <?php endif; ?>
            </p>

            <form method="GET" action="/manager/satisfaction-surveys" class="flex items-center gap-2">
                <input type="hidden" name="course"    value="<?= htmlspecialchars($filters['course'], ENT_QUOTES) ?>">
                <input type="hidden" name="rating"    value="<?= htmlspecialchars($filters['rating'], ENT_QUOTES) ?>">
                <input type="hidden" name="date_from" value="<?= htmlspecialchars($filters['date_from'], ENT_QUOTES) ?>">
                <input type="hidden" name="date_to"   value="<?= htmlspecialchars($filters['date_to'], ENT_QUOTES) ?>">
                <span class="text-[11.5px] font-semibold text-slate-400">Mostrar</span>
                <select name="per_page" class="campo" onchange="this.form.submit()" aria-label="Filas por página">
                    <?php foreach ($perPageOptions as $n): ?>
                    <option value="<?= $n ?>" <?= $perPage === (int) $n ? 'selected' : '' ?>><?= $n ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="text-[11.5px] font-semibold text-slate-400">filas</span>
            </form>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="flex items-center gap-1.5">
            <a href="<?= $enlace(max(1, $page - 1)) ?>" class="pag <?= $page <= 1 ? 'inerte pointer-events-none' : '' ?>" title="Anterior">
                <i data-lucide="chevron-left" class="ic ic-14"></i>
            </a>

            <?php for ($i = $desde; $i <= $hasta; $i++): ?>
            <a href="<?= $enlace($i) ?>" class="pag <?= $i === $page ? 'activa' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <a href="<?= $enlace(min($totalPages, $page + 1)) ?>" class="pag <?= $page >= $totalPages ? 'inerte pointer-events-none' : '' ?>" title="Siguiente">
                <i data-lucide="chevron-right" class="ic ic-14"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Formulario oculto de borrado -->
<form id="deleteForm" method="POST" style="display: none;">
    <?= \Core\Security::getCsrfField() ?>
</form>

<script>
function deleteSurvey(id) {
    if (confirm('¿Eliminar esta encuesta? Esta acción no se puede deshacer.')) {
        const form = document.getElementById('deleteForm');
        form.action = '/manager/satisfaction-surveys/' + id + '/delete';
        form.submit();
    }
}
</script>
