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
$lastPage        = (int) ($lastPage ?? 1);
$total           = (int) ($total ?? 0);
$q               = $q ?? '';
$filterCourse    = (int) ($filterCourse ?? 0);
$filterStatus    = $filterStatus ?? '';
$filterEmpty     = $filterEmpty  ?? '';

// ¿Hay algún filtro activo? Si lo hay, las tarjetas se abren solas.
$filtrando = ($q !== '' || $filterCourse > 0 || $filterStatus !== '' || $filterEmpty === '1');

$pagerBase = http_build_query(array_filter([
    'q'         => $q,
    'course_id' => $filterCourse ?: null,
    'status'    => $filterStatus ?: null,
    'empty'     => $filterEmpty ?: null,
]));
$pagerBase = $pagerBase ? "?{$pagerBase}&" : '?';
?>

<!-- Encabezado -->
<div class="mb-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Quizzes</h1>
        <p class="text-sm text-gray-500">Las evaluaciones se agrupan por curso</p>
    </div>
<?php if (can('lms.quizzes.manage')): ?>
    <a href="/manager/lms/quizzes/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition shadow-md whitespace-nowrap">
        <i class="fas fa-plus"></i> Crear Nuevo Quiz
    </a>
<?php endif; ?>
</div>

<!-- Resumen -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    <div class="bg-white rounded-xl border border-gray-100 px-4 py-3">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Evaluaciones</p>
        <p class="text-xl font-black text-slate-800 mt-0.5"><?= (int) $stats['quizzes'] ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 px-4 py-3">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Preguntas</p>
        <p class="text-xl font-black text-slate-800 mt-0.5"><?= (int) $stats['preguntas'] ?></p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 px-4 py-3">
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cursos con evaluación</p>
        <p class="text-xl font-black text-slate-800 mt-0.5"><?= $total ?></p>
    </div>
    <a href="/manager/lms/quizzes?empty=1"
       class="rounded-xl border px-4 py-3 transition <?= (int) $stats['vacios'] > 0
            ? 'bg-amber-50 border-amber-200 hover:bg-amber-100'
            : 'bg-white border-gray-100' ?>">
        <p class="text-[10px] font-black uppercase tracking-widest <?= (int) $stats['vacios'] > 0 ? 'text-amber-600' : 'text-gray-400' ?>">Sin preguntas</p>
        <p class="text-xl font-black mt-0.5 <?= (int) $stats['vacios'] > 0 ? 'text-amber-700' : 'text-slate-800' ?>"><?= (int) $stats['vacios'] ?></p>
    </a>
</div>

<!-- Filtros -->
<form method="GET" action="/manager/lms/quizzes"
      class="bg-white rounded-xl border border-gray-100 p-4 mb-5 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
    <div class="md:col-span-4">
        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Buscar</label>
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"
               placeholder="Título del quiz o del curso"
               class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
    </div>
    <div class="md:col-span-4">
        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Curso</label>
        <select name="course_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
            <option value="">Todos los cursos</option>
            <?php foreach ($courseOptions as $c): ?>
            <option value="<?= (int) $c['id'] ?>" <?= $filterCourse === (int) $c['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['title']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estado</label>
        <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
            <option value="">Todos</option>
            <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Activos</option>
            <option value="hidden" <?= $filterStatus === 'hidden' ? 'selected' : '' ?>>Ocultos</option>
        </select>
    </div>
    <div class="md:col-span-2 flex items-center gap-2">
        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold px-4 py-2 rounded-lg transition">
            Filtrar
        </button>
        <?php if ($filtrando): ?>
        <a href="/manager/lms/quizzes" title="Limpiar filtros"
           class="px-3 py-2 rounded-lg border border-gray-200 text-gray-400 hover:text-red-600 hover:border-red-200 transition">
            <i class="fas fa-times"></i>
        </a>
        <?php endif; ?>
    </div>
    <?php if ($filterEmpty === '1'): ?>
    <input type="hidden" name="empty" value="1">
    <div class="md:col-span-12">
        <span class="inline-flex items-center gap-2 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg">
            <i class="fas fa-filter text-[10px]"></i> Mostrando solo evaluaciones sin preguntas
        </span>
    </div>
    <?php endif; ?>
</form>

<!-- Cursos con sus evaluaciones -->
<?php if (empty($courses)): ?>
<div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
    <i class="fas fa-vial-circle-check text-3xl text-gray-200 mb-3"></i>
    <p class="text-gray-400 font-medium">
        <?= $filtrando ? 'Ninguna evaluación coincide con el filtro.' : 'No hay evaluaciones registradas.' ?>
    </p>
</div>
<?php else: ?>

<div class="space-y-3">
    <?php foreach ($courses as $course):
        $cid     = (int) $course['id'];
        $lista   = $quizzesByCourse[$cid] ?? [];
        $vacios  = (int) $course['empty_count'];
    ?>
    <details class="group bg-white rounded-xl border border-gray-100 overflow-hidden" <?= $filtrando ? 'open' : '' ?>>
        <summary class="flex items-center gap-4 px-5 py-4 cursor-pointer hover:bg-slate-50 transition select-none">
            <i class="fas fa-chevron-right text-xs text-gray-300 transition-transform group-open:rotate-90"></i>

            <div class="flex-1 min-w-0">
                <p class="font-bold text-slate-800 truncate"><?= htmlspecialchars($course['title']) ?></p>
                <p class="text-xs text-gray-400 mt-0.5">
                    <?= (int) $course['quiz_count'] ?> evaluación<?= (int) $course['quiz_count'] === 1 ? '' : 'es' ?>
                    · <?= (int) $course['question_total'] ?> pregunta<?= (int) $course['question_total'] === 1 ? '' : 's' ?>
                </p>
            </div>

            <?php if ($vacios > 0): ?>
            <span class="hidden sm:inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-200 px-2 py-1 rounded-full whitespace-nowrap">
                <i class="fas fa-triangle-exclamation text-[9px]"></i> <?= $vacios ?> sin preguntas
            </span>
            <?php endif; ?>

            <?php if (($course['status'] ?? '') !== 'published'): ?>
            <span class="hidden sm:inline-block text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-400 px-2 py-1 rounded-full">
                <?= htmlspecialchars($course['status'] ?? '') ?>
            </span>
            <?php endif; ?>

            <a href="/manager/lms/courses/<?= $cid ?>/show" onclick="event.stopPropagation();"
               class="text-gray-300 hover:text-blue-600 transition p-1" title="Ver el curso">
                <i class="fas fa-arrow-up-right-from-square text-xs"></i>
            </a>
        </summary>

        <div class="border-t border-gray-50 divide-y divide-gray-50">
            <?php foreach ($lista as $quiz):
                $qc = (int) $quiz['question_count'];
            ?>
            <div class="flex items-center gap-4 px-5 py-3 pl-12 hover:bg-blue-50/30 transition">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 <?= $qc > 0 ? 'bg-blue-50 text-blue-500' : 'bg-amber-50 text-amber-500' ?>">
                    <i class="fas fa-vial text-xs"></i>
                </span>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-700 truncate"><?= htmlspecialchars($quiz['title']) ?></p>
                    <div class="flex items-center gap-3 mt-0.5 text-[11px] text-gray-400 font-medium">
                        <?php if ($qc > 0): ?>
                            <span><?= $qc ?> pregunta<?= $qc === 1 ? '' : 's' ?></span>
                        <?php else: ?>
                            <span class="text-amber-600 font-bold">Sin preguntas</span>
                        <?php endif; ?>
                        <span>·</span>
                        <span><?= rtrim(rtrim(number_format((float) $quiz['pass_percentage'], 2, '.', ''), '0'), '.') ?>% para aprobar</span>
                        <?php if ((int) $quiz['time_limit'] > 0): ?>
                        <span>·</span>
                        <span class="text-amber-600"><i class="fas fa-clock text-[9px]"></i> <?= (int) $quiz['time_limit'] ?> min</span>
                        <?php endif; ?>
                        <?php if ((int) $quiz['attempt_count'] > 0): ?>
                        <span>·</span>
                        <span><?= (int) $quiz['attempt_count'] ?> intento<?= (int) $quiz['attempt_count'] === 1 ? '' : 's' ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <span class="hidden md:inline-block px-2 py-1 rounded-full text-[10px] font-black uppercase <?= $quiz['is_active'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' ?>">
                    <?= $quiz['is_active'] ? 'Activo' : 'Oculto' ?>
                </span>

                <div class="flex items-center gap-1 flex-shrink-0">
                    <a href="/manager/lms/quizzes/<?= (int) $quiz['id'] ?>/questions"
                       class="px-3 py-1.5 rounded-lg bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-wider hover:bg-slate-100 transition"
                       title="Ver y editar las preguntas">
                        Preguntas
                    </a>
<?php if (can('lms.quizzes.manage')): ?>
                    <a href="/manager/lms/quizzes/<?= (int) $quiz['id'] ?>/edit"
                       class="p-2 text-gray-400 hover:text-blue-600 transition" title="Editar">
                        <i class="fas fa-edit"></i>
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
                          onsubmit="return confirm(<?= htmlspecialchars(json_encode($_confirmQuiz), ENT_QUOTES, 'UTF-8') ?>);" class="inline">
                        <?php echo \Core\Security::getCsrfField(); ?>
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
<?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </details>
    <?php endforeach; ?>
</div>

<!-- Paginación -->
<?php if ($lastPage > 1): ?>
<div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
    <p class="text-xs text-gray-400 font-medium">
        Página <strong class="text-slate-600"><?= $page ?></strong> de <strong class="text-slate-600"><?= $lastPage ?></strong>
        · <?= $total ?> curso<?= $total === 1 ? '' : 's' ?>
    </p>
    <div class="flex items-center gap-1">
        <?php if ($page > 1): ?>
        <a href="/manager/lms/quizzes<?= $pagerBase ?>page=<?= $page - 1 ?>"
           class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition">
            Anterior
        </a>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end   = min($lastPage, $page + 2);
        if ($start > 1): ?>
        <a href="/manager/lms/quizzes<?= $pagerBase ?>page=1"
           class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition">1</a>
        <?php if ($start > 2): ?><span class="px-1 text-gray-300">…</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($p = $start; $p <= $end; $p++): ?>
        <a href="/manager/lms/quizzes<?= $pagerBase ?>page=<?= $p ?>"
           class="px-3 py-1.5 rounded-lg text-xs font-bold transition <?= $p === $page
                ? 'bg-blue-600 text-white'
                : 'border border-gray-200 text-slate-500 hover:bg-slate-50' ?>"><?= $p ?></a>
        <?php endfor; ?>

        <?php if ($end < $lastPage): ?>
        <?php if ($end < $lastPage - 1): ?><span class="px-1 text-gray-300">…</span><?php endif; ?>
        <a href="/manager/lms/quizzes<?= $pagerBase ?>page=<?= $lastPage ?>"
           class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition"><?= $lastPage ?></a>
        <?php endif; ?>

        <?php if ($page < $lastPage): ?>
        <a href="/manager/lms/quizzes<?= $pagerBase ?>page=<?= $page + 1 ?>"
           class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition">
            Siguiente
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<style>
    /* Oculta el triángulo nativo del desplegable */
    details > summary { list-style: none; }
    details > summary::-webkit-details-marker { display: none; }
</style>
