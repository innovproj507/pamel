<?php
/**
 * Ficha de seguimiento de un estudiante: sus cursos con el avance y todos
 * los exámenes que ha realizado, con su acta descargable.
 */
$estudiante = $estudiante ?? [];
$cursos     = $cursos     ?? [];
$examenes   = $examenes   ?? [];

$estados = [
    'completado'  => ['Completado',  'emerald'],
    'en_curso'    => ['En curso',    'blue'],
    'sin_empezar' => ['Sin empezar', 'slate'],
];
?>

<div class="mb-5 flex items-start gap-4">
    <a href="/manager/lms/seguimiento"
       class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm flex-shrink-0">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($estudiante['name']) ?></h1>
        <p class="text-sm text-gray-500"><?= htmlspecialchars($estudiante['email']) ?></p>
    </div>
</div>

<!-- Cursos -->
<div class="card overflow-hidden mb-5">
    <div class="px-4 py-3 border-b" style="border-color: var(--card-border)">
        <h2 class="text-sm font-black text-slate-800">
            Cursos <span class="text-gray-400 font-bold">· <?= count($cursos) ?></span>
        </h2>
    </div>

    <?php if (!$cursos): ?>
    <p class="px-4 py-10 text-center text-sm text-gray-400">No está matriculado en ningún curso.</p>
    <?php else: ?>
    <div class="divide-y divide-gray-100">
        <?php foreach ($cursos as $c):
            [$etiqueta, $color] = $estados[$c['estado']] ?? $estados['sin_empezar'];
        ?>
        <div class="px-4 py-3">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <span class="font-semibold text-gray-800 flex-1 min-w-0 truncate"><?= htmlspecialchars($c['curso']) ?></span>
                <span class="text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-full bg-<?= $color ?>-50 text-<?= $color ?>-600 border border-<?= $color ?>-200">
                    <?= $etiqueta ?>
                </span>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="h-1.5 rounded-full bg-<?= $color ?>-500" style="width: <?= (int) $c['progreso'] ?>%"></div>
                </div>
                <span class="text-[11px] font-black text-<?= $color ?>-600 w-9 text-right"><?= (int) $c['progreso'] ?>%</span>
            </div>
            <p class="text-[11px] text-gray-400 mt-1.5">
                Lecciones <strong class="text-gray-600"><?= (int) $c['lecciones_hechas'] ?>/<?= (int) $c['lecciones'] ?></strong>
                · Exámenes <strong class="text-gray-600"><?= (int) $c['examenes_aprobados'] ?>/<?= (int) $c['examenes'] ?></strong>
                · <?= htmlspecialchars($c['etapa']) ?>
            </p>
            <?php
                $pl = $c['pendientes']['lecciones'] ?? [];
                $pe = $c['pendientes']['examenes'] ?? [];
            ?>
            <?php if ($pl || $pe): ?>
            <details class="mt-2">
                <summary class="text-[11px] font-bold text-blue-600 cursor-pointer select-none">
                    Pendiente: <?= count($pl) ?> <?= count($pl) === 1 ? 'lección' : 'lecciones' ?>
                    · <?= count($pe) ?> <?= count($pe) === 1 ? 'examen' : 'exámenes' ?>
                </summary>
                <div class="mt-2 grid gap-3 sm:grid-cols-2 text-[12px]">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">Lecciones</p>
                        <?php if ($pl): ?>
                        <ul class="list-disc pl-4 space-y-0.5 text-gray-600">
                            <?php foreach ($pl as $l): ?><li><?= htmlspecialchars($l['title']) ?></li><?php endforeach; ?>
                        </ul>
                        <?php else: ?><p class="text-gray-400">Todas completadas.</p><?php endif; ?>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-wider text-gray-400 mb-1">Exámenes</p>
                        <?php if ($pe): ?>
                        <ul class="list-disc pl-4 space-y-0.5 text-gray-600">
                            <?php foreach ($pe as $q): ?>
                            <li><?= htmlspecialchars($q['title']) ?>
                                <span class="text-gray-400">— <?= $q['intentos'] > 0 ? $q['intentos'] . ' ' . ($q['intentos'] === 1 ? 'intento sin aprobar' : 'intentos sin aprobar') : 'sin presentar' ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?><p class="text-gray-400">Todos aprobados.</p><?php endif; ?>
                    </div>
                </div>
            </details>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Exámenes -->
<div class="card overflow-hidden">
    <div class="px-4 py-3 border-b" style="border-color: var(--card-border)">
        <h2 class="text-sm font-black text-slate-800">
            Exámenes realizados <span class="text-gray-400 font-bold">· <?= count($examenes) ?></span>
        </h2>
    </div>

    <?php if (!$examenes): ?>
    <p class="px-4 py-10 text-center text-sm text-gray-400">Todavía no ha realizado ningún examen.</p>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] table-fixed text-[13px]">
            <thead class="bg-gray-50">
                <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                    <th class="px-3 py-2 w-[26%]">Evaluación</th>
                    <th class="px-3 py-2 w-[24%]">Curso</th>
                    <th class="px-3 py-2 w-[13%]">Fecha</th>
                    <th class="px-3 py-2 w-[13%] text-center">Nota</th>
                    <th class="px-3 py-2 w-[10%] text-center">Resultado</th>
                    <th class="px-3 py-2 w-[14%] text-right">Acta</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($examenes as $e): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-3 py-2">
                        <span class="block truncate text-gray-800" title="<?= htmlspecialchars($e['examen']) ?>"><?= htmlspecialchars($e['examen']) ?></span>
                        <span class="text-[11px] text-gray-400"><?= (int) $e['respuestas'] ?> preguntas</span>
                    </td>
                    <td class="px-3 py-2">
                        <span class="block truncate text-gray-600" title="<?= htmlspecialchars($e['curso']) ?>"><?= htmlspecialchars($e['curso']) ?></span>
                    </td>
                    <td class="px-3 py-2 text-gray-500 whitespace-nowrap">
                        <?= $e['completed_at'] ? date('d/m/Y', strtotime($e['completed_at'])) : '—' ?>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <span class="font-black <?= $e['passed'] ? 'text-emerald-600' : 'text-rose-600' ?>">
                            <?= number_format((float) $e['score'], 1) ?>%
                        </span>
                        <span class="block text-[10px] text-gray-400">mín. <?= (int) $e['pass_percentage'] ?>%</span>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full border <?=
                            $e['passed'] ? 'bg-emerald-50 text-emerald-600 border-emerald-200'
                                         : 'bg-rose-50 text-rose-600 border-rose-200' ?>"
                              title="<?= $e['passed'] ? 'Aprobado' : 'No aprobado' ?>">
                            <i class="fas <?= $e['passed'] ? 'fa-check' : 'fa-xmark' ?> text-xs"></i>
                        </span>
                    </td>
                    <td class="px-3 py-2 text-right whitespace-nowrap">
                        <form method="POST" action="/manager/lms/actas/<?= (int) $e['id'] ?>/verificar" class="inline"
                              onsubmit="return confirm('<?= $e['verified_at'] ? 'Retirar la conformidad del instructor' : 'Confirmar que el examen fue verificado y cuenta con la conformidad del instructor' ?>?')">
                            <?= \Core\Security::getCsrfField() ?>
                            <?php if ($e['verified_at']): ?>
                            <input type="hidden" name="accion" value="quitar">
                            <button type="submit" class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-rose-50 hover:text-rose-600 transition"
                                    title="Verificado por <?= htmlspecialchars((string) $e['verified_by_name']) ?> el <?= date('d/m/Y', strtotime($e['verified_at'])) ?>. Clic para retirar">
                                <i class="fas fa-circle-check text-xs"></i>
                            </button>
                            <?php else: ?>
                            <button type="submit" class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-gray-300 hover:bg-emerald-50 hover:text-emerald-600 transition"
                                    title="Marcar como verificado por el instructor">
                                <i class="far fa-circle-check text-xs"></i>
                            </button>
                            <?php endif; ?>
                        </form>
                        <a href="/manager/lms/actas/<?= (int) $e['id'] ?>"
                           class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition"
                           title="Descargar el examen en Word">
                            <i class="fas fa-file-word text-xs"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
