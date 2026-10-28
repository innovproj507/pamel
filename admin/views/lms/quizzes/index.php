<?php
/**
 * Listado de evaluaciones agrupadas por curso.
 * Los quizzes se muestran dentro de la tarjeta de su curso, como en Moodle,
 * en lugar de una tabla plana que repite el curso en cada renglón.
 */
$courses         = $courses         ?? [];
$quizzesByCourse = $quizzesByCourse ?? [];
$courseOptions   = $courseOptions   ?? [];
$stats           = $stats           ?? ['quizzes' => 0, 'preguntas' => 0, 'vacios' => 0];
$page            = (int) ($page ?? 1);
$perPage         = (int) ($perPage ?? 10);
$lastPage        = (int) ($lastPage ?? 1);
$total           = (int) ($total ?? 0);
$q               = $q ?? '';
$filterCourse    = (int) ($filterCourse ?? 0);
$filterStatus    = $filterStatus ?? '';
$filterEmpty     = $filterEmpty  ?? '';

// ¿Hay algún filtro activo? Si lo hay, las tarjetas se abren solas.
$filtrando = ($q !== '' || $filterCourse > 0 || $filterStatus !== '' || $filterEmpty === '1');
$vaciosTot = (int) $stats['vacios'];
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Gestión de Quizzes</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> curso<?= $total === 1 ? '' : 's' ?> con evaluación · agrupadas por curso
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
<?php if (can('lms.quizzes.manage')): ?>
        <a href="/manager/lms/quizzes/create" class="btn-primario">
            <i data-lucide="plus" class="ic ic-16"></i> Crear Nuevo Quiz
        </a>
<?php endif; ?>
    </div>
</div>

<div class="card overflow-hidden mb-5">

    <!-- Barra de filtros -->
    <form method="GET" action="/manager/lms/quizzes"
          class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <div>
            <label class="filtro-rot" for="f-q">Buscar</label>
            <div class="relative">
                <i data-lucide="search" class="ic ic-14 absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" id="f-q" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES) ?>"
                       placeholder="Título del quiz o del curso…"
                       class="campo" style="padding-left:32px; width:250px; border-radius:999px">
            </div>
        </div>

        <div>
            <label class="filtro-rot" for="f-curso">Curso</label>
            <select id="f-curso" name="course_id" class="campo" onchange="this.form.submit()"
                    style="max-width:260px">
                <option value="">Todos los cursos</option>
                <?php foreach ($courseOptions as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= $filterCourse === (int) $c['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['title']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="filtro-rot" for="f-estado">Estado</label>
            <select id="f-estado" name="status" class="campo" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Activos</option>
                <option value="hidden" <?= $filterStatus === 'hidden' ? 'selected' : '' ?>>Ocultos</option>
            </select>
        </div>

        <button type="submit" class="btn-sec">
            <i data-lucide="search" class="ic ic-14"></i> Filtrar
        </button>

        <?php if ($filtrando): ?>
        <a href="/manager/lms/quizzes" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Limpiar
        </a>
        <?php endif; ?>

        <?php if ($filterEmpty === '1'): ?>
        <input type="hidden" name="empty" value="1">
        <span class="pastilla p-ambar" style="height:34px">
            <i data-lucide="filter" class="ic" style="width:11px;height:11px"></i>
            Sólo evaluaciones sin preguntas
        </span>
        <?php endif; ?>

        <input type="hidden" name="per_page" value="<?= $perPage ?>">
    </form>

    <!-- Resumen -->
    <div class="px-4 py-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="kpi">
            <div class="kpi-label">Evaluaciones</div>
            <div class="kpi-value"><?= number_format((int) $stats['quizzes']) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Preguntas</div>
            <div class="kpi-value"><?= number_format((int) $stats['preguntas']) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Cursos con evaluación</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
        </div>
        <!-- Atajo: lleva al filtro de evaluaciones incompletas. -->
        <a href="/manager/lms/quizzes?empty=1" class="kpi"
           <?= $vaciosTot > 0 ? 'style="background:#fffbeb; border-color:#fde68a"' : '' ?>
           title="Ver sólo las evaluaciones sin preguntas">
            <div class="kpi-label" <?= $vaciosTot > 0 ? 'style="color:#b45309"' : '' ?>>Sin preguntas</div>
            <div class="kpi-value" <?= $vaciosTot > 0 ? 'style="color:#92400e"' : '' ?>><?= number_format($vaciosTot) ?></div>
        </a>
    </div>
</div>

<!-- Cursos con sus evaluaciones -->
<?php if (empty($courses)): ?>
<div class="card">
    <div class="vacio">
        <i data-lucide="flask-conical" class="ic"></i>
        <p><?= $filtrando ? 'Ninguna evaluación coincide con el filtro.' : 'No hay evaluaciones registradas.' ?></p>
        <?php if ($filtrando): ?>
        <a href="/manager/lms/quizzes" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
            Limpiar filtros
        </a>
        <?php endif; ?>
    </div>
</div>
<?php else: ?>

<div class="space-y-3">
    <?php foreach ($courses as $course):
        $cid    = (int) $course['id'];
        $lista  = $quizzesByCourse[$cid] ?? [];
        $vacios = (int) $course['empty_count'];
    ?>
    <details class="group card overflow-hidden" <?= $filtrando ? 'open' : '' ?>>
        <summary class="flex items-center gap-3.5 px-4 py-3.5 cursor-pointer hover:bg-slate-50 transition select-none">
            <i data-lucide="chevron-right" class="ic ic-14 text-slate-300 transition-transform group-open:rotate-90 flex-shrink-0"></i>

            <div class="flex-1 min-w-0">
                <p class="font-bold text-[13px] text-slate-800 truncate"><?= htmlspecialchars($course['title']) ?></p>
                <p class="text-[11px] font-semibold text-slate-400 mt-0.5">
                    <?= (int) $course['quiz_count'] ?> evaluación<?= (int) $course['quiz_count'] === 1 ? '' : 'es' ?>
                    · <?= (int) $course['question_total'] ?> pregunta<?= (int) $course['question_total'] === 1 ? '' : 's' ?>
                </p>
            </div>

            <?php if ($vacios > 0): ?>
            <span class="pastilla p-ambar hidden sm:inline-flex">
                <i data-lucide="triangle-alert" class="ic" style="width:11px;height:11px"></i>
                <?= $vacios ?> sin preguntas
            </span>
            <?php endif; ?>

            <?php if (($course['status'] ?? '') !== 'published'): ?>
            <span class="pastilla p-gris hidden sm:inline-flex"><?= htmlspecialchars($course['status'] ?? '') ?></span>
            <?php endif; ?>

            <a href="/manager/lms/courses/<?= $cid ?>/show" onclick="event.stopPropagation();"
               class="ib ib-fantasma flex-shrink-0" title="Ver el curso">
                <i data-lucide="external-link" class="ic ic-14"></i>
            </a>
        </summary>

        <div class="border-t" style="border-color: var(--card-border)">
            <table class="tabla">
                <tbody>
                <?php foreach ($lista as $quiz):
                    $qc     = (int) $quiz['question_count'];
                    $activo = !empty($quiz['is_active']);
                ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <span class="ib <?= $qc > 0 ? 'ib-azul' : 'ib-ambar' ?>">
                                <i data-lucide="flask-conical" class="ic ic-14"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:420px">
                                    <?= htmlspecialchars($quiz['title']) ?>
                                </p>
                                <div class="flex items-center gap-2.5 flex-wrap mt-0.5 text-[10.5px] font-semibold text-slate-400">
                                    <?php if ($qc > 0): ?>
                                        <span><?= $qc ?> pregunta<?= $qc === 1 ? '' : 's' ?></span>
                                    <?php else: ?>
                                        <span class="text-amber-600 font-bold">Sin preguntas</span>
                                    <?php endif; ?>
                                    <span>·</span>
                                    <span><?= rtrim(rtrim(number_format((float) $quiz['pass_percentage'], 2, '.', ''), '0'), '.') ?>% para aprobar</span>
                                    <?php if ((int) $quiz['time_limit'] > 0): ?>
                                    <span>·</span>
                                    <span class="text-amber-600"><?= (int) $quiz['time_limit'] ?> min</span>
                                    <?php endif; ?>
                                    <?php if ((int) $quiz['attempt_count'] > 0): ?>
                                    <span>·</span>
                                    <span><?= (int) $quiz['attempt_count'] ?> intento<?= (int) $quiz['attempt_count'] === 1 ? '' : 's' ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>

                    <td style="width:1%">
                        <span class="pastilla <?= $activo ? 'p-verde' : 'p-gris' ?>">
                            <span class="punto"></span><?= $activo ? 'Activo' : 'Oculto' ?>
                        </span>
                    </td>

                    <td class="num" style="width:1%">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/lms/quizzes/<?= (int) $quiz['id'] ?>/questions" class="acc acc-azul"
                               title="Ver y editar las preguntas">
                                <i data-lucide="list-checks" class="ic" style="width:12px;height:12px"></i> Preguntas
                            </a>
<?php if (can('lms.quizzes.manage')): ?>
                            <a href="/manager/lms/quizzes/<?= (int) $quiz['id'] ?>/edit" class="acc acc-gris" title="Editar quiz">
                                <i data-lucide="pencil" class="ic" style="width:12px;height:12px"></i> Editar
                            </a>
                            <?php
                            // El borrado arrastra preguntas, opciones e intentos.
                            $_confirmQuiz = $qc > 0
                                ? sprintf(
                                    "Se eliminará el quiz «%s» junto con sus %d pregunta(s), "
                                    . "sus opciones y los intentos de alumnos asociados.\n\n¿Continuar?",
                                    $quiz['title'],
                                    $qc
                                  )
                                : sprintf('¿Eliminar el quiz «%s»?', $quiz['title']);
                            ?>
                            <form method="POST" action="/manager/lms/quizzes/<?= (int) $quiz['id'] ?>/delete"
                                  onsubmit="return confirm(<?= htmlspecialchars(json_encode($_confirmQuiz), ENT_QUOTES, 'UTF-8') ?>);">
                                <?= \Core\Security::getCsrfField() ?>
                                <button type="submit" class="acc acc-rojo" title="Eliminar quiz">
                                    <i data-lucide="trash-2" class="ic" style="width:12px;height:12px"></i> Eliminar
                                </button>
                            </form>
<?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </details>
    <?php endforeach; ?>
</div>

<!-- Pie: rango, filas por página y paginador -->
<div class="card mt-4 overflow-hidden">
    <?php
    $ruta    = '/manager/lms/quizzes';
    $filtros = [
        'q'         => $q,
        'course_id' => $filterCourse ?: '',
        'status'    => $filterStatus,
        'empty'     => $filterEmpty,
    ];
    include \Core\Config::get('paths.root') . '/admin/views/partials/paginador.php';
    ?>
</div>

<?php endif; ?>

<style>
    /* Oculta el triángulo nativo del desplegable */
    details > summary { list-style: none; }
    details > summary::-webkit-details-marker { display: none; }
</style>
