<?php
/**
 * Listado de encuestas de satisfacción.
 *
 * La tabla se salía de la pantalla por la derecha y la columna de acciones
 * quedaba fuera: ocho columnas con px-6 py-4 y el nombre del curso sin
 * límite de ancho. Ahora el relleno es menor, los anchos van en porcentaje
 * y lo que no cabe se corta con puntos suspensivos, con el texto completo
 * en el title.
 *
 * Valores por defecto arriba para que la vista se dibuje aunque el
 * controlador no mande las filas por pantalla.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

$perPage        = $perPage        ?? 20;
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
        'A-Buena'   => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Buena'],
        'B-Regular' => ['bg-amber-50 text-amber-700 border-amber-200',       'Regular'],
        'C-Mala'    => ['bg-rose-50 text-rose-700 border-rose-200',          'Mala'],
    ];
    [$clase, $texto] = $estilos[$rating] ?? ['bg-gray-50 text-gray-500 border-gray-200', $rating ?: '—'];

    return sprintf(
        '<span class="inline-block px-2 py-0.5 rounded-md border text-[11px] font-semibold %s" title="%s">%s</span>',
        $clase,
        htmlspecialchars((string) $rating),
        htmlspecialchars((string) $texto)
    );
}

/** URL conservando filtros y filas por pantalla. */
$enlace = static function (int $p) use ($filters, $perPage): string {
    return '?' . http_build_query(array_filter([
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
?>

<div class="container mx-auto px-4 py-5">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-900">
            <i class="fas fa-poll text-blue-600 mr-2"></i>Encuestas de Satisfacción
        </h1>
        <span class="text-sm text-gray-600">Total: <?= (int) $total ?></span>
    </div>

    <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!--
        Filtros en una sola línea. Antes ocupaban una rejilla de cuatro
        columnas más una fila de botones, y con la tarjeta de "Mostrando
        X de Y" encima de la tabla había que bajar la pantalla entera para
        ver las primeras encuestas.
    -->
    <form method="GET" action="/manager/satisfaction-surveys"
          class="bg-white rounded-lg shadow p-2.5 mb-4 flex flex-wrap items-center gap-2">

        <select name="course" onchange="this.form.submit()"
                class="flex-1 min-w-[200px] max-w-[320px] py-2 pl-3 pr-8 text-[13px] border border-gray-200 rounded-lg bg-white focus:border-blue-500 focus:outline-none transition cursor-pointer">
            <option value="">Todos los cursos</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course['course_name']) ?>"
                        <?= $filters['course'] === $course['course_name'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($course['course_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="rating" onchange="this.form.submit()"
                class="py-2 pl-3 pr-8 text-[13px] border border-gray-200 rounded-lg bg-white focus:border-blue-500 focus:outline-none transition cursor-pointer">
            <option value="">Todas las calificaciones</option>
            <option value="A-Buena"   <?= $filters['rating'] === 'A-Buena'   ? 'selected' : '' ?>>A - Buena</option>
            <option value="B-Regular" <?= $filters['rating'] === 'B-Regular' ? 'selected' : '' ?>>B - Regular</option>
            <option value="C-Mala"    <?= $filters['rating'] === 'C-Mala'    ? 'selected' : '' ?>>C - Mala</option>
        </select>

        <label class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
            Desde
            <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>"
                   class="py-2 px-2.5 text-[13px] font-normal normal-case tracking-normal text-gray-700 border border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition">
        </label>

        <label class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
            Hasta
            <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>"
                   class="py-2 px-2.5 text-[13px] font-normal normal-case tracking-normal text-gray-700 border border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none transition">
        </label>

        <label class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
            Mostrar
            <select name="per_page" onchange="this.form.submit()"
                    class="py-2 pl-2.5 pr-7 text-[13px] font-normal normal-case tracking-normal text-gray-700 border border-gray-200 rounded-lg bg-white focus:border-blue-500 focus:outline-none transition cursor-pointer">
                <?php foreach ($perPageOptions as $n): ?>
                <option value="<?= $n ?>" <?= (int) $perPage === $n ? 'selected' : '' ?>><?= $n ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit"
                class="px-4 py-2 text-[13px] font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <i class="fas fa-filter mr-1.5"></i>Filtrar
        </button>

        <?php if ($hayFiltros): ?>
        <a href="/manager/satisfaction-surveys"
           class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-rose-200 hover:text-rose-500 transition"
           title="Limpiar filtros">
            <i class="fas fa-times text-xs"></i>
        </a>
        <?php endif; ?>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <!--
                w-full, no min-w-full: con min-width la tabla crece hasta caber
                el texto más largo, el recorte nunca se aplica y las últimas
                columnas quedan fuera de la pantalla. El min-w en píxeles es
                el suelo por debajo del cual aparece la barra horizontal.
            -->
            <table class="w-full min-w-[920px] table-fixed divide-y divide-gray-200 text-[13px]">
                <thead class="bg-gray-50">
                    <!-- Anchos en porcentaje y sumando 100 exactos. -->
                    <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="px-3 py-2 w-[9%]">Fecha</th>
                        <th class="px-3 py-2 w-[22%]">Estudiante</th>
                        <th class="px-3 py-2 w-[27%]">Curso</th>
                        <th class="px-2 py-2 w-[9%] text-center">Personal</th>
                        <th class="px-2 py-2 w-[9%] text-center">Calidad</th>
                        <th class="px-2 py-2 w-[9%] text-center">Instructor</th>
                        <th class="px-2 py-2 w-[9%] text-center">Infraestr.</th>
                        <th class="px-3 py-2 w-[6%] text-right">Acc.</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if (empty($surveys)): ?>
                        <tr>
                            <td colspan="8" class="px-3 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>No se encontraron encuestas</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($surveys as $survey):
                            $nombre = trim($survey['first_name'] . ' ' . $survey['last_name']);
                            $curso  = (string) $survey['course_name'];
                        ?>
                            <tr class="hover:bg-gray-50 align-middle">
                                <td class="px-3 py-2 whitespace-nowrap text-gray-500">
                                    <?= date('d/m/Y', strtotime($survey['survey_date'])) ?>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="font-semibold text-gray-900 truncate" title="<?= htmlspecialchars($nombre) ?>">
                                        <?= htmlspecialchars($nombre) ?>
                                    </div>
                                    <a href="mailto:<?= htmlspecialchars($survey['email']) ?>"
                                       class="block text-[11px] text-gray-400 truncate hover:text-blue-600"
                                       title="<?= htmlspecialchars($survey['email']) ?>">
                                        <?= htmlspecialchars($survey['email']) ?>
                                    </a>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="truncate text-gray-800" title="<?= htmlspecialchars($curso) ?>">
                                        <?= htmlspecialchars($curso) ?>
                                    </div>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <?= getRatingBadge($survey['staff_attention_rating']) ?>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <?= getRatingBadge($survey['training_quality_rating']) ?>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <?= getRatingBadge($survey['instructor_performance_rating']) ?>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <?= getRatingBadge($survey['infrastructure_rating']) ?>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-0.5">
                                        <a href="/manager/satisfaction-surveys/<?= $survey['id'] ?>"
                                           class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition"
                                           title="Ver detalles">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="/manager/satisfaction-surveys/<?= $survey['id'] ?>/download-word"
                                           class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition"
                                           title="Descargar Word">
                                            <i class="fas fa-file-word text-xs"></i>
                                        </a>
                                        <button onclick="deleteSurvey(<?= $survey['id'] ?>)"
                                                class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-gray-400 hover:bg-rose-50 hover:text-rose-600 transition"
                                                title="Eliminar">
                                            <i class="fas fa-trash text-xs"></i>
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
            Pie con el recuento y el paginador. Antes el recuento iba en una
            tarjeta aparte encima de la tabla y el paginador pintaba TODAS
            las páginas en una fila; ahora la ventana sigue a la actual.
        -->
        <?php
        $desde = max(1, min($page - 2, $totalPages - 4));
        $hasta = min($totalPages, max($page + 2, 5));
        ?>
        <div class="px-4 py-2.5 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 text-[12px]">
            <p class="text-gray-400">
                Mostrando <strong class="text-gray-600"><?= $primero ?>–<?= $ultimo ?></strong>
                de <strong class="text-gray-600"><?= (int) $total ?></strong> encuestas
            </p>

            <?php if ($totalPages > 1): ?>
            <nav class="inline-flex items-center gap-1">
                <a href="<?= $enlace(max(1, $page - 1)) ?>"
                   class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-gray-200 <?=
                       $page <= 1 ? 'text-gray-200 pointer-events-none' : 'text-gray-400 hover:bg-gray-50' ?>"
                   title="Anterior">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </a>

                <?php for ($i = $desde; $i <= $hasta; $i++): ?>
                <a href="<?= $enlace($i) ?>"
                   class="min-w-[1.75rem] h-7 px-2 inline-flex items-center justify-center rounded-lg border font-semibold <?=
                       $i === $page
                           ? 'bg-blue-600 border-blue-600 text-white'
                           : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <a href="<?= $enlace(min($totalPages, $page + 1)) ?>"
                   class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-gray-200 <?=
                       $page >= $totalPages ? 'text-gray-200 pointer-events-none' : 'text-gray-400 hover:bg-gray-50' ?>"
                   title="Siguiente">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </a>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    <?= \Core\Security::getCsrfField() ?>
</form>

<script>
function deleteSurvey(id) {
    if (confirm('¿Está seguro de que desea eliminar esta encuesta?')) {
        const form = document.getElementById('deleteForm');
        form.action = '/manager/satisfaction-surveys/' + id + '/delete';
        form.submit();
    }
}
</script>
