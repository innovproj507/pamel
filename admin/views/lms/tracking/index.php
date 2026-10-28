<?php
/**
 * Seguimiento académico: indicadores generales y avance por matrícula.
 *
 * Todo lo que se ve aquí se calcula desde las lecciones completadas y los
 * intentos de examen. La columna progress_percentage de lms_enrollments
 * no se usa: nunca se ha escrito y daba cero para todos.
 */
$filas         = $filas   ?? [];
$resumen       = $resumen ?? [];
$cursos        = $cursos  ?? [];
$filtros       = $filtros ?? [];
$porCurso      = $porCurso      ?? false;
$cursosResumen = $cursosResumen ?? [];

// Etiqueta, pastilla y color de barra de cada estado de matrícula.
$estados = [
    'completado'  => ['Completado',  'p-verde', 'b-verde'],
    'en_curso'    => ['En curso',    'p-azul',  ''],
    'sin_empezar' => ['Sin empezar', 'p-gris',  'b-gris'],
];

// El título dice qué se está mirando: la pantalla de entrada es por
// curso y, al abrir uno, pasa a ser por estudiante.
$esPorCurso = $porCurso;

// Dentro de un curso, su nombre para la miga de pan.
$unCurso     = (int) ($filtros['course_id'] ?? 0);
$nombreCurso = '';
if ($unCurso) {
    foreach ($cursos as $c) {
        if ((int) $c['id'] === $unCurso) { $nombreCurso = $c['title']; break; }
    }
}
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="<?= $esPorCurso ? '/manager' : '/manager/lms/seguimiento' ?>" class="btn-atras"
       title="<?= $esPorCurso ? 'Volver al panel' : 'Volver a todos los cursos' ?>">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">
            <?= $esPorCurso ? 'Seguimiento por curso' : 'Seguimiento por estudiante' ?>
        </h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5 truncate" style="max-width:640px">
            <?php if ($esPorCurso): ?>
            Cómo va cada curso. Pulsa uno para ver el avance de sus estudiantes.
            <?php elseif ($nombreCurso): ?>
            <?= htmlspecialchars($nombreCurso) ?>
            <?php else: ?>
            Avance real de cada matrícula, calculado desde las lecciones completadas y los exámenes aprobados.
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Barra de filtros -->
    <form method="GET" action="/manager/lms/seguimiento"
          class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <?php if (!empty($filtros['estado'])): ?>
        <input type="hidden" name="estado" value="<?= htmlspecialchars($filtros['estado'], ENT_QUOTES) ?>">
        <?php endif; ?>

        <div>
            <label class="filtro-rot" for="f-buscar">Buscar</label>
            <div class="relative">
                <i data-lucide="search" class="ic ic-14 absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="search" id="f-buscar" name="buscar" value="<?= htmlspecialchars($filtros['buscar'] ?? '', ENT_QUOTES) ?>"
                       placeholder="Nombre o correo del estudiante…"
                       class="campo" style="padding-left:32px; width:260px; border-radius:999px">
            </div>
        </div>

        <div>
            <label class="filtro-rot" for="f-curso">Curso</label>
            <select id="f-curso" name="curso" class="campo" onchange="this.form.submit()" style="max-width:300px">
                <option value="">Todos los cursos</option>
                <?php foreach ($cursos as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= $unCurso === (int) $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars(mb_strimwidth($c['title'], 0, 52, '…')) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-sec">
            <i data-lucide="search" class="ic ic-14"></i> Buscar
        </button>

        <?php if ($filtros): ?>
        <a href="/manager/lms/seguimiento" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Limpiar
        </a>
        <?php endif; ?>
    </form>

    <!-- Indicadores. Los que filtran son enlaces y se marcan al estar activos. -->
    <div class="px-4 py-4 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
        <?php
        /*
            La primera tarjeta cuenta MATRÍCULAS, no estudiantes: completados,
            en curso y sin empezar son estados de una matrícula y tienen que
            sumar con ella. El número de estudiantes va debajo, como aclaración.
        */
        $tarjetas = [
            ['Matrículas',       number_format($resumen['matriculas'] ?? 0), null,
                number_format($resumen['estudiantes'] ?? 0) . ' estudiantes'],
            ['Completados',      number_format($resumen['completados'] ?? 0), 'completado',  null],
            ['En curso',         number_format($resumen['en_curso']    ?? 0), 'en_curso',    null],
            ['Sin empezar',      number_format($resumen['sin_empezar'] ?? 0), 'sin_empezar', null],
            ['Examen pendiente', number_format($resumen['con_pendiente_examen'] ?? 0), null,
                'sin aprobar todavía'],
            ['Progreso medio',   ($resumen['progreso_medio'] ?? 0) . '%', null, null],
        ];
        foreach ($tarjetas as [$texto, $valor, $filtroEstado, $aclaracion]):
            $activo = $filtroEstado !== null && ($filtros['estado'] ?? '') === $filtroEstado;
            $url = '/manager/lms/seguimiento?' . http_build_query(array_filter([
                'curso'  => $filtros['course_id'] ?? null,
                'buscar' => $filtros['buscar'] ?? null,
                'estado' => $activo ? null : $filtroEstado,
            ]));
            $etiqueta = $filtroEstado !== null ? 'a' : 'div';
        ?>
        <<?= $etiqueta ?> <?= $filtroEstado !== null ? 'href="' . $url . '"' : '' ?>
            class="kpi block<?= $filtroEstado !== null ? ' transition' : '' ?>"
            <?= $activo ? 'style="border-color:#2563eb; background:#f5f8ff"' : '' ?>
            <?= $filtroEstado !== null ? 'title="' . ($activo ? 'Quitar este filtro' : 'Filtrar por ' . strtolower($texto)) . '"' : '' ?>>
            <div class="kpi-label truncate"><?= $texto ?></div>
            <div class="kpi-value"><?= $valor ?></div>
            <?php if ($aclaracion): ?>
            <div class="text-[10px] font-semibold text-slate-400 truncate mt-0.5"
                 title="<?= htmlspecialchars($aclaracion, ENT_QUOTES) ?>"><?= htmlspecialchars($aclaracion) ?></div>
            <?php endif; ?>
        </<?= $etiqueta ?>>
        <?php endforeach; ?>
    </div>

<?php if ($porCurso): ?>
    <!--
        Entrada por curso. Una fila por curso con cuánta gente lleva dentro y
        por dónde va; al pulsar se abre el detalle de sus estudiantes, que es
        el mismo listado de siempre filtrado por ese curso.
    -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Curso</th>
                    <th style="min-width:170px">Progreso medio</th>
                    <th class="centro">Matrículas</th>
                    <th class="centro">Completados</th>
                    <th class="centro">En curso</th>
                    <th class="centro">Sin empezar</th>
                    <th class="centro">Examen pend.</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$cursosResumen): ?>
                <tr>
                    <td colspan="7" class="vacio">
                        <i data-lucide="book-open" class="ic"></i>
                        <p>Todavía no hay matrículas en ningún curso.</p>
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach ($cursosResumen as $c):
                    $pct = (int) $c['progreso_medio'];
                ?>
                <tr>
                    <td>
                        <a href="/manager/lms/seguimiento?curso=<?= (int) $c['course_id'] ?>"
                           class="block font-bold text-[12.5px] text-slate-800 hover:text-blue-700 hover:underline truncate"
                           style="max-width:340px" title="<?= htmlspecialchars($c['curso'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars($c['curso']) ?>
                        </a>
                        <div class="text-[10.5px] font-semibold text-slate-400 mt-0.5">
                            <?= (int) $c['estudiantes'] ?> estudiante<?= (int) $c['estudiantes'] === 1 ? '' : 's' ?>
                            · <?= (int) $c['lecciones'] ?> lección<?= (int) $c['lecciones'] === 1 ? '' : 'es' ?>
                            · <?= (int) $c['examenes'] ?> examen<?= (int) $c['examenes'] === 1 ? '' : 'es' ?>
                        </div>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <span class="barra <?= $pct >= 100 ? 'b-verde' : '' ?>">
                                <span style="width:<?= max(0, min(100, $pct)) ?>%"></span>
                            </span>
                            <span class="text-[11.5px] font-bold text-slate-600 w-9 text-right"><?= $pct ?>%</span>
                        </div>
                    </td>
                    <td class="centro font-bold text-slate-700"><?= (int) $c['matriculas'] ?></td>
                    <td class="centro font-bold text-emerald-600"><?= (int) $c['completados'] ?></td>
                    <td class="centro font-bold text-blue-600"><?= (int) $c['en_curso'] ?></td>
                    <td class="centro font-bold text-amber-600"><?= (int) $c['sin_empezar'] ?></td>
                    <td class="centro font-bold <?= $c['con_pendiente_examen'] ? 'text-rose-600' : 'text-slate-300' ?>">
                        <?= (int) $c['con_pendiente_examen'] ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <!--
        Detalle. Dentro de un curso la columna "Curso" repite lo mismo en
        todas las filas, así que se retira y ese ancho se reparte entre el
        estudiante y el "en qué va".
    -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <?php if (!$unCurso): ?>
                    <th>Curso</th>
                    <?php endif; ?>
                    <th>Estado</th>
                    <th style="min-width:160px">Progreso</th>
                    <th class="centro">Lecciones</th>
                    <th class="centro">Exámenes</th>
                    <th>En qué va</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$filas): ?>
                <tr>
                    <td colspan="<?= $unCurso ? 6 : 7 ?>" class="vacio">
                        <i data-lucide="user-round-search" class="ic"></i>
                        <p>No hay matrículas que coincidan.</p>
                        <?php if ($filtros): ?>
                        <a href="/manager/lms/seguimiento" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Limpiar filtros
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>

                <?php foreach ($filas as $f):
                    [$etiqueta, $clasePastilla, $claseBarra] = $estados[$f['estado']] ?? $estados['sin_empezar'];
                    $pct = (int) $f['progreso'];
                ?>
                <tr>
                    <td>
                        <a href="/manager/lms/seguimiento/estudiante/<?= (int) $f['student_id'] ?>"
                           class="block font-bold text-[12.5px] text-slate-800 hover:text-blue-700 hover:underline truncate"
                           style="max-width:230px" title="<?= htmlspecialchars($f['estudiante'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars($f['estudiante']) ?>
                        </a>
                        <div class="text-[10.5px] font-semibold text-slate-400 truncate" style="max-width:230px">
                            <?= htmlspecialchars($f['email']) ?>
                        </div>
                    </td>
                    <?php if (!$unCurso): ?>
                    <td>
                        <span class="block truncate" style="max-width:230px"
                              title="<?= htmlspecialchars($f['curso'], ENT_QUOTES) ?>"><?= htmlspecialchars($f['curso']) ?></span>
                    </td>
                    <?php endif; ?>
                    <td>
                        <span class="pastilla <?= $clasePastilla ?>"><span class="punto"></span><?= $etiqueta ?></span>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <span class="barra <?= $claseBarra ?>"><span style="width:<?= max(0, min(100, $pct)) ?>%"></span></span>
                            <span class="text-[11.5px] font-bold text-slate-600 w-9 text-right"><?= $pct ?>%</span>
                        </div>
                    </td>
                    <td class="centro font-bold text-slate-700 whitespace-nowrap">
                        <?= (int) $f['lecciones_hechas'] ?>/<?= (int) $f['lecciones'] ?>
                    </td>
                    <td class="centro whitespace-nowrap <?= (int) $f['pendiente_examenes'] > 0 ? 'text-rose-600 font-bold' : 'font-bold text-slate-700' ?>">
                        <?= (int) $f['examenes_aprobados'] ?>/<?= (int) $f['examenes'] ?>
                    </td>
                    <td>
                        <span class="block truncate text-slate-400" style="max-width:220px"
                              title="<?= htmlspecialchars($f['etapa'], ENT_QUOTES) ?>"><?= htmlspecialchars($f['etapa']) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3.5 border-t" style="border-color: var(--card-border)">
        <p class="text-[12px] font-semibold text-slate-400">
            <?= count($filas) ?> matrícula<?= count($filas) === 1 ? '' : 's' ?>
        </p>
    </div>
<?php endif; ?>
</div>
