<?php
/**
 * Seguimiento académico: indicadores generales y avance por matrícula.
 *
 * Todo lo que se ve aquí se calcula desde las lecciones completadas y los
 * intentos de examen. La columna progress_percentage de lms_enrollments
 * no se usa: nunca se ha escrito y daba cero para todos.
 */
$filas   = $filas   ?? [];
$resumen = $resumen ?? [];
$cursos  = $cursos  ?? [];
$filtros = $filtros ?? [];

$estados = [
    'completado'  => ['Completado',  'emerald'],
    'en_curso'    => ['En curso',    'blue'],
    'sin_empezar' => ['Sin empezar', 'slate'],
];
?>

<div class="mb-5">
    <h1 class="text-2xl font-bold text-gray-800">Seguimiento de estudiantes</h1>
    <p class="text-sm text-gray-500">
        Avance real de cada matrícula, calculado desde las lecciones completadas y los exámenes aprobados.
    </p>
</div>

<!-- Indicadores -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">
    <?php
    /*
        La primera tarjeta cuenta MATRÍCULAS, no estudiantes: completados,
        en curso y sin empezar son estados de una matrícula y tienen que
        sumar con ella. Antes mostraba el número de estudiantes distintos
        y la suma no cuadraba, porque hay alumnos matriculados en varios
        cursos. El número de estudiantes va ahora debajo, como aclaración.
    */
    $tarjetas = [
        ['Matrículas',        $resumen['matriculas']   ?? 0, 'slate',   'fa-users',          null,
            ($resumen['estudiantes'] ?? 0) . ' estudiantes'],
        ['Completados',       $resumen['completados']  ?? 0, 'emerald', 'fa-circle-check',   'completado', null],
        ['En curso',          $resumen['en_curso']     ?? 0, 'blue',    'fa-spinner',        'en_curso',   null],
        ['Sin empezar',       $resumen['sin_empezar']  ?? 0, 'amber',   'fa-hourglass-start','sin_empezar',null],
        ['Examen pendiente',  $resumen['con_pendiente_examen']  ?? 0, 'rose',   'fa-file-pen', null,
            'matrículas con examen sin aprobar'],
        ['Progreso medio',    ($resumen['progreso_medio'] ?? 0) . '%', 'violet', 'fa-chart-line', null, null],
    ];
    foreach ($tarjetas as [$texto, $valor, $color, $icono, $filtroEstado, $aclaracion]):
        $activo = $filtroEstado !== null && ($filtros['estado'] ?? '') === $filtroEstado;
        $url = '/manager/lms/seguimiento?' . http_build_query(array_filter([
            'curso'  => $filtros['course_id'] ?? null,
            'buscar' => $filtros['buscar'] ?? null,
            'estado' => $activo ? null : $filtroEstado,
        ]));
    ?>
    <<?= $filtroEstado !== null ? 'a href="' . $url . '"' : 'div' ?>
        class="card p-3 block transition <?= $activo ? 'border-' . $color . '-400 bg-' . $color . '-50/40' : ($filtroEstado !== null ? 'hover:border-' . $color . '-300' : '') ?>">
        <div class="flex items-start justify-between">
            <div class="min-w-0">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest truncate"><?= $texto ?></p>
                <p class="text-xl font-black text-<?= $color ?>-600 mt-0.5"><?= $valor ?></p>
                <?php if ($aclaracion): ?>
                <p class="text-[10px] text-gray-400 truncate" title="<?= htmlspecialchars($aclaracion) ?>"><?= htmlspecialchars($aclaracion) ?></p>
                <?php endif; ?>
            </div>
            <i class="fas <?= $icono ?> text-<?= $color ?>-300 text-sm mt-1"></i>
        </div>
    </<?= $filtroEstado !== null ? 'a' : 'div' ?>>
    <?php endforeach; ?>
</div>

<!-- Filtros -->
<form method="GET" action="/manager/lms/seguimiento" class="card p-2.5 mb-4 flex flex-wrap items-center gap-2">
    <?php if (!empty($filtros['estado'])): ?>
    <input type="hidden" name="estado" value="<?= htmlspecialchars($filtros['estado']) ?>">
    <?php endif; ?>
    <div class="relative flex-1 min-w-[220px]">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
        <input type="search" name="buscar" value="<?= htmlspecialchars($filtros['buscar'] ?? '') ?>"
               placeholder="Nombre o correo del estudiante…"
               class="w-full pl-9 pr-3 py-2 text-[13px] border border-gray-200 rounded-lg focus:border-blue-500 outline-none transition">
    </div>
    <select name="curso" onchange="this.form.submit()"
            class="py-2 pl-3 pr-8 text-[13px] border border-gray-200 rounded-lg bg-white focus:border-blue-500 outline-none transition cursor-pointer max-w-[22rem]">
        <option value="">Todos los cursos</option>
        <?php foreach ($cursos as $c): ?>
        <option value="<?= (int) $c['id'] ?>" <?= (int) ($filtros['course_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars(mb_strimwidth($c['title'], 0, 52, '…')) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="px-4 py-2 text-[13px] font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Buscar</button>
    <?php if ($filtros): ?>
    <a href="/manager/lms/seguimiento" class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:text-rose-500 transition" title="Quitar filtros">
        <i class="fas fa-times text-xs"></i>
    </a>
    <?php endif; ?>
</form>

<?php
$porCurso      = $porCurso      ?? false;
$cursosResumen = $cursosResumen ?? [];
?>

<?php if ($porCurso): ?>
<!--
    Entrada por curso. Una fila por curso con cuánta gente lleva dentro y
    por dónde va; al pulsar se abre el detalle de sus estudiantes, que es
    el mismo listado de siempre filtrado por ese curso.
-->
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] table-fixed text-[13px]">
            <thead class="bg-gray-50">
                <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                    <th class="px-3 py-2 w-[34%]">Curso</th>
                    <th class="px-3 py-2 w-[18%]">Progreso medio</th>
                    <th class="px-3 py-2 w-[10%] text-center">Matrículas</th>
                    <th class="px-3 py-2 w-[10%] text-center">Completados</th>
                    <th class="px-3 py-2 w-[10%] text-center">En curso</th>
                    <th class="px-3 py-2 w-[10%] text-center">Sin empezar</th>
                    <th class="px-3 py-2 w-[8%] text-center">Examen pend.</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!$cursosResumen): ?>
                <tr><td colspan="7" class="px-3 py-12 text-center text-gray-400">
                    <i class="fas fa-book text-3xl mb-2 block text-gray-200"></i>
                    Todavía no hay matrículas en ningún curso.
                </td></tr>
                <?php endif; ?>

                <?php foreach ($cursosResumen as $c):
                    $pct = (int) $c['progreso_medio'];
                ?>
                <tr class="hover:bg-gray-50 align-middle">
                    <td class="px-3 py-2">
                        <a href="/manager/lms/seguimiento?curso=<?= (int) $c['course_id'] ?>"
                           class="font-semibold text-gray-900 hover:text-blue-600 block truncate"
                           title="<?= htmlspecialchars($c['curso']) ?>">
                            <?= htmlspecialchars($c['curso']) ?>
                        </a>
                        <span class="text-[11px] text-gray-400">
                            <?= (int) $c['estudiantes'] ?> estudiante<?= (int) $c['estudiantes'] === 1 ? '' : 's' ?>
                            · <?= (int) $c['lecciones'] ?> lección<?= (int) $c['lecciones'] === 1 ? '' : 'es' ?>
                            · <?= (int) $c['examenes'] ?> examen<?= (int) $c['examenes'] === 1 ? '' : 'es' ?>
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full <?= $pct >= 100 ? 'bg-emerald-500' : 'bg-blue-500' ?>"
                                     style="width:<?= max(0, min(100, $pct)) ?>%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-600 w-9 text-right"><?= $pct ?>%</span>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-center font-bold text-gray-700"><?= (int) $c['matriculas'] ?></td>
                    <td class="px-3 py-2 text-center font-bold text-emerald-600"><?= (int) $c['completados'] ?></td>
                    <td class="px-3 py-2 text-center font-bold text-blue-600"><?= (int) $c['en_curso'] ?></td>
                    <td class="px-3 py-2 text-center font-bold text-amber-600"><?= (int) $c['sin_empezar'] ?></td>
                    <td class="px-3 py-2 text-center font-bold <?= $c['con_pendiente_examen'] ? 'text-rose-600' : 'text-gray-300' ?>">
                        <?= (int) $c['con_pendiente_examen'] ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>

<?php
/*
    Detalle. La vuelta atrás, que si no el único camino es borrar la URL,
    y el nombre del curso al lado: dentro de un curso la columna "Curso"
    repite lo mismo en todas las filas, así que se retira y ese ancho se
    reparte entre el estudiante y el "en qué va".
*/
$unCurso = (int) ($filtros['course_id'] ?? 0);
$nombreCurso = '';
if ($unCurso) {
    foreach ($cursos as $c) {
        if ((int) $c['id'] === $unCurso) { $nombreCurso = $c['title']; break; }
    }
}
?>
<div class="mb-3 flex flex-wrap items-center gap-2">
    <a href="/manager/lms/seguimiento"
       class="inline-flex items-center gap-2 text-[13px] font-semibold text-gray-500 hover:text-blue-600 transition">
        <i class="fas fa-arrow-left text-xs"></i> Todos los cursos
    </a>
    <?php if ($nombreCurso): ?>
    <span class="text-gray-300">/</span>
    <span class="text-[13px] font-bold text-gray-800"><?= htmlspecialchars($nombreCurso) ?></span>
    <?php endif; ?>
</div>

<!-- Listado -->
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[980px] table-fixed text-[13px]">
            <thead class="bg-gray-50">
                <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                    <th class="px-3 py-2 w-[<?= $unCurso ? 30 : 20 ?>%]">Estudiante</th>
                    <?php if (!$unCurso): ?>
                    <th class="px-3 py-2 w-[24%]">Curso</th>
                    <?php endif; ?>
                    <th class="px-3 py-2 w-[<?= $unCurso ? 18 : 16 ?>%]">Progreso</th>
                    <th class="px-3 py-2 w-[10%] text-center">Lecciones</th>
                    <th class="px-3 py-2 w-[10%] text-center">Exámenes</th>
                    <th class="px-3 py-2 w-[<?= $unCurso ? 32 : 20 ?>%]">En qué va</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!$filas): ?>
                <tr><td colspan="<?= $unCurso ? 5 : 6 ?>" class="px-3 py-12 text-center text-gray-400">
                    <i class="fas fa-user-graduate text-3xl mb-2 block text-gray-200"></i>
                    No hay matrículas que coincidan.
                </td></tr>
                <?php endif; ?>

                <?php foreach ($filas as $f):
                    [$etiqueta, $color] = $estados[$f['estado']] ?? $estados['sin_empezar'];
                ?>
                <tr class="hover:bg-gray-50 align-middle">
                    <td class="px-3 py-2">
                        <a href="/manager/lms/seguimiento/estudiante/<?= (int) $f['student_id'] ?>"
                           class="font-semibold text-gray-900 hover:text-blue-600 block truncate"
                           title="<?= htmlspecialchars($f['estudiante']) ?>">
                            <?= htmlspecialchars($f['estudiante']) ?>
                        </a>
                        <span class="text-[11px] text-gray-400 block truncate"><?= htmlspecialchars($f['email']) ?></span>
                        <?php if ($unCurso): /* sin columna de curso, el estado va aquí */ ?>
                        <span class="text-[10px] font-black uppercase tracking-wider text-<?= $color ?>-600"><?= $etiqueta ?></span>
                        <?php endif; ?>
                    </td>
                    <?php if (!$unCurso): ?>
                    <td class="px-3 py-2">
                        <span class="block truncate text-gray-700" title="<?= htmlspecialchars($f['curso']) ?>"><?= htmlspecialchars($f['curso']) ?></span>
                        <span class="text-[10px] font-black uppercase tracking-wider text-<?= $color ?>-600"><?= $etiqueta ?></span>
                    </td>
                    <?php endif; ?>
                    <td class="px-3 py-2">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full bg-<?= $color ?>-500" style="width: <?= (int) $f['progreso'] ?>%"></div>
                            </div>
                            <span class="text-[11px] font-black text-<?= $color ?>-600 w-9 text-right"><?= (int) $f['progreso'] ?>%</span>
                        </div>
                    </td>
                    <td class="px-3 py-2 text-center text-gray-600">
                        <?= (int) $f['lecciones_hechas'] ?>/<?= (int) $f['lecciones'] ?>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <span class="<?= (int) $f['pendiente_examenes'] > 0 ? 'text-rose-600 font-bold' : 'text-gray-600' ?>">
                            <?= (int) $f['examenes_aprobados'] ?>/<?= (int) $f['examenes'] ?>
                        </span>
                    </td>
                    <td class="px-3 py-2">
                        <span class="block truncate text-gray-500" title="<?= htmlspecialchars($f['etapa']) ?>"><?= htmlspecialchars($f['etapa']) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="px-4 py-2.5 border-t border-gray-100 text-[12px] text-gray-400">
        <?= count($filas) ?> matrícula<?= count($filas) === 1 ? '' : 's' ?>
    </div>
</div>
<?php endif; ?>
