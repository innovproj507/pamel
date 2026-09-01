<?php
/**
 * Banco de preguntas sin asignar: un bloque por set, con muestra y las
 * acciones de asignar o eliminar.
 */
$sets     = $sets     ?? [];
$summary  = $summary  ?? ['preguntas' => 0, 'sets' => 0];
$targets  = $targets  ?? [];
$page     = (int) ($page ?? 1);
$lastPage = (int) ($lastPage ?? 1);
$total    = (int) ($total ?? 0);
$q        = $q ?? '';

$pagerBase = $q !== '' ? '?q=' . urlencode($q) . '&' : '?';

// Quizzes destino agrupados por curso, para el desplegable
$porCurso = [];
foreach ($targets as $t) {
    $porCurso[$t['course_title']][] = $t;
}
?>

<div class="mb-6 flex flex-col lg:flex-row lg:items-start justify-between gap-4">
    <div class="flex items-start gap-4">
        <a href="/manager/lms/quizzes" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm flex-shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Preguntas sin asignar</h1>
            <p class="text-sm text-gray-500 max-w-2xl">
                Preguntas que quedaron apuntando a un quiz que ya no existe. Vienen agrupadas
                por su set original: asigna cada set al quiz que le corresponde, o elimínalo.
            </p>
        </div>
    </div>
    <div class="flex gap-3 flex-shrink-0">
        <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sets</p>
            <p class="text-xl font-black text-slate-800 mt-0.5"><?= (int) $summary['sets'] ?></p>
        </div>
        <div class="bg-amber-50 rounded-xl border border-amber-200 px-4 py-3 text-center">
            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest">Preguntas</p>
            <p class="text-xl font-black text-amber-700 mt-0.5"><?= (int) $summary['preguntas'] ?></p>
        </div>
    </div>
</div>

<?php if ((int) $summary['preguntas'] === 0): ?>

<div class="bg-white rounded-xl border border-gray-100 p-14 text-center">
    <i class="fas fa-circle-check text-4xl text-emerald-300 mb-4"></i>
    <p class="text-lg font-bold text-slate-700">No queda ninguna pregunta sin asignar</p>
    <p class="text-sm text-gray-400 mt-1">Todas las preguntas pertenecen a un quiz existente.</p>
</div>

<?php else: ?>

<!-- Buscador -->
<form method="GET" action="/manager/lms/questions/unassigned" class="bg-white rounded-xl border border-gray-100 p-4 mb-5 flex flex-col sm:flex-row gap-3">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"
           placeholder="Buscar por texto de la pregunta o por número de set"
           class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold px-5 py-2 rounded-lg transition">
        Buscar
    </button>
    <?php if ($q !== ''): ?>
    <a href="/manager/lms/questions/unassigned" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-bold text-gray-400 hover:text-red-600 hover:border-red-200 transition text-center">
        Limpiar
    </a>
    <?php endif; ?>
</form>

<?php if (empty($sets)): ?>
<div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
    <p class="text-gray-400 font-medium">Ningún set coincide con la búsqueda.</p>
</div>
<?php else: ?>

<div class="space-y-4">
    <?php foreach ($sets as $set):
        $sid   = (int) $set['set_id'];
        $tot   = (int) $set['total'];
        $dup   = (int) $set['duplicadas'];
        $uni   = $tot - $dup;
        $todas = $dup === $tot;
    ?>
    <div class="bg-white rounded-xl border <?= $todas ? 'border-gray-200' : 'border-gray-100' ?> overflow-hidden">

        <!-- Cabecera del set -->
        <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b border-gray-50">
            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-500 text-xs font-black">SET <?= $sid ?></span>
            <span class="text-sm font-bold text-slate-700"><?= $tot ?> pregunta<?= $tot === 1 ? '' : 's' ?></span>

            <?php if ($uni > 0): ?>
            <span class="text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200 px-2 py-1 rounded-full">
                <?= $uni ?> única<?= $uni === 1 ? '' : 's' ?>
            </span>
            <?php endif; ?>

            <?php if ($dup > 0): ?>
            <span class="text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-400 px-2 py-1 rounded-full"
                  title="Ya existen con el mismo texto en un quiz activo">
                <?= $dup ?> duplicada<?= $dup === 1 ? '' : 's' ?>
            </span>
            <?php endif; ?>

            <?php if ($todas): ?>
            <span class="text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-500 border border-rose-200 px-2 py-1 rounded-full">
                Todo duplicado · se puede eliminar
            </span>
            <?php endif; ?>

            <a href="/manager/lms/questions/unassigned/<?= $sid ?>"
               class="ml-auto text-xs font-black text-blue-600 hover:underline whitespace-nowrap">
                Ver las <?= $tot ?> preguntas →
            </a>
        </div>

        <!-- Muestra -->
        <div class="px-5 py-3 space-y-1.5 bg-slate-50/40">
            <?php foreach ($set['muestra'] as $i => $m): ?>
            <p class="text-xs text-slate-500 leading-snug">
                <span class="text-gray-300 font-black"><?= $i + 1 ?>.</span>
                <?= htmlspecialchars(mb_strimwidth(trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode($m['question'], ENT_QUOTES, 'UTF-8')))), 0, 150, '…')) ?>
            </p>
            <?php endforeach; ?>
        </div>

        <!-- Acciones -->
        <div class="px-5 py-3 flex flex-col lg:flex-row gap-3 lg:items-center border-t border-gray-50">
            <form method="POST" action="/manager/lms/questions/unassigned/<?= $sid ?>/assign"
                  class="flex flex-1 flex-col sm:flex-row gap-2"
                  onsubmit="return this.target_quiz_id.value ? true : (alert('Selecciona primero el quiz destino.'), false);">
                <?php echo \Core\Security::getCsrfField(); ?>
                <select name="target_quiz_id"
                        class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
                    <option value="">Asignar estas <?= $tot ?> preguntas a…</option>
                    <?php foreach ($porCurso as $curso => $lista): ?>
                    <optgroup label="<?= htmlspecialchars($curso) ?>">
                        <?php foreach ($lista as $t): ?>
                        <option value="<?= (int) $t['id'] ?>">
                            <?= htmlspecialchars($t['title']) ?>
                            <?= (int) $t['question_count'] === 0 ? ' — vacío' : ' (' . (int) $t['question_count'] . ')' ?>
                        </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2 rounded-lg transition whitespace-nowrap">
                    <i class="fas fa-arrow-right-to-bracket mr-1"></i> Asignar
                </button>
            </form>

            <?php
            $confirmDel = sprintf(
                "Se eliminarán las %d preguntas del set %d, con sus opciones y las respuestas "
                . "de alumnos que las referencien.\n\nEsta acción no se puede deshacer.\n\n¿Continuar?",
                $tot, $sid
            );
            ?>
            <form method="POST" action="/manager/lms/questions/unassigned/<?= $sid ?>/delete"
                  onsubmit="return confirm(<?= htmlspecialchars(json_encode($confirmDel), ENT_QUOTES, 'UTF-8') ?>);">
                <?php echo \Core\Security::getCsrfField(); ?>
                <button type="submit"
                        class="w-full lg:w-auto px-4 py-2 rounded-lg border border-rose-200 text-rose-500 text-sm font-bold hover:bg-rose-500 hover:text-white transition whitespace-nowrap">
                    <i class="fas fa-trash mr-1"></i> Eliminar set
                </button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Paginación -->
<?php if ($lastPage > 1): ?>
<div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
    <p class="text-xs text-gray-400 font-medium">
        Página <strong class="text-slate-600"><?= $page ?></strong> de <strong class="text-slate-600"><?= $lastPage ?></strong>
        · <?= $total ?> set<?= $total === 1 ? '' : 's' ?>
    </p>
    <div class="flex items-center gap-1">
        <?php if ($page > 1): ?>
        <a href="/manager/lms/questions/unassigned<?= $pagerBase ?>page=<?= $page - 1 ?>"
           class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition">Anterior</a>
        <?php endif; ?>
        <?php
        $start = max(1, $page - 2);
        $end   = min($lastPage, $page + 2);
        for ($p = $start; $p <= $end; $p++): ?>
        <a href="/manager/lms/questions/unassigned<?= $pagerBase ?>page=<?= $p ?>"
           class="px-3 py-1.5 rounded-lg text-xs font-bold transition <?= $p === $page
                ? 'bg-blue-600 text-white'
                : 'border border-gray-200 text-slate-500 hover:bg-slate-50' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $lastPage): ?>
        <a href="/manager/lms/questions/unassigned<?= $pagerBase ?>page=<?= $page + 1 ?>"
           class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-slate-500 hover:bg-slate-50 transition">Siguiente</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>
<?php endif; ?>
