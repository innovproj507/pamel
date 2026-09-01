<?php
/**
 * Detalle de un set sin asignar: todas sus preguntas con las opciones,
 * para poder identificar a qué curso pertenece antes de asignarlo.
 */
$questions = $questions ?? [];
$setId     = (int) ($setId ?? 0);
$answers   = (int) ($answers ?? 0);
$targets   = $targets ?? [];

$porCurso = [];
foreach ($targets as $t) {
    $porCurso[$t['course_title']][] = $t;
}

$limpiar = static function ($texto): string {
    $t = html_entity_decode((string) $texto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $t = str_replace(['\\n', '\\r', "\xC2\xA0"], ' ', $t);
    return trim(preg_replace('/\s+/u', ' ', strip_tags($t)));
};
?>

<div class="mb-6 flex items-start gap-4">
    <a href="/manager/lms/questions/unassigned" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm flex-shrink-0">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Set <?= $setId ?></h1>
        <p class="text-sm text-gray-500">
            <?= count($questions) ?> pregunta<?= count($questions) === 1 ? '' : 's' ?> sin asignar
            <?php if ($answers > 0): ?>
            · <span class="text-amber-600 font-bold"><?= $answers ?> respuesta(s) de alumnos</span>
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Acciones -->
<div class="bg-white rounded-xl border border-gray-100 p-4 mb-5 flex flex-col lg:flex-row gap-3 lg:items-center">
    <form method="POST" action="/manager/lms/questions/unassigned/<?= $setId ?>/assign"
          class="flex flex-1 flex-col sm:flex-row gap-2"
          onsubmit="return this.target_quiz_id.value ? true : (alert('Selecciona primero el quiz destino.'), false);">
        <?php echo \Core\Security::getCsrfField(); ?>
        <select name="target_quiz_id" class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
            <option value="">Asignar estas <?= count($questions) ?> preguntas a…</option>
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
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2 rounded-lg transition whitespace-nowrap">
            <i class="fas fa-arrow-right-to-bracket mr-1"></i> Asignar set completo
        </button>
    </form>

    <?php
    $confirmDel = sprintf(
        "Se eliminarán las %d preguntas del set %d, con sus opciones y las respuestas "
        . "de alumnos que las referencien.\n\nEsta acción no se puede deshacer.\n\n¿Continuar?",
        count($questions), $setId
    );
    ?>
    <form method="POST" action="/manager/lms/questions/unassigned/<?= $setId ?>/delete"
          onsubmit="return confirm(<?= htmlspecialchars(json_encode($confirmDel), ENT_QUOTES, 'UTF-8') ?>);">
        <?php echo \Core\Security::getCsrfField(); ?>
        <button type="submit" class="w-full lg:w-auto px-4 py-2 rounded-lg border border-rose-200 text-rose-500 text-sm font-bold hover:bg-rose-500 hover:text-white transition whitespace-nowrap">
            <i class="fas fa-trash mr-1"></i> Eliminar set
        </button>
    </form>
</div>

<!-- Preguntas -->
<div class="space-y-3">
    <?php foreach ($questions as $i => $q): ?>
    <div class="bg-white rounded-xl border border-gray-100 p-5">
        <div class="flex items-start gap-3">
            <span class="w-7 h-7 rounded-lg bg-slate-50 text-slate-400 text-xs font-black flex items-center justify-center flex-shrink-0 mt-0.5">
                <?= $i + 1 ?>
            </span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-700 leading-snug"><?= htmlspecialchars($limpiar($q['question'])) ?></p>

                <?php if (!empty($q['options'])): ?>
                <ul class="mt-3 space-y-1.5">
                    <?php foreach ($q['options'] as $o): ?>
                    <li class="flex items-start gap-2 text-xs <?= (int) $o['is_correct'] === 1 ? 'text-emerald-700 font-bold' : 'text-slate-500' ?>">
                        <i class="fas <?= (int) $o['is_correct'] === 1 ? 'fa-circle-check text-emerald-500' : 'fa-circle text-gray-200' ?> text-[10px] mt-0.5"></i>
                        <span><?= htmlspecialchars($limpiar($o['option_text'])) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="mt-2 text-xs text-amber-600 font-bold">Esta pregunta no tiene opciones.</p>
                <?php endif; ?>
            </div>
            <span class="text-[10px] font-black text-gray-300 uppercase tracking-wider flex-shrink-0"><?= (int) $q['points'] ?> pt</span>
        </div>
    </div>
    <?php endforeach; ?>
</div>
