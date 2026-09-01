<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <a href="/manager/lms/quizzes/<?= $quiz['id'] ?>/edit" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Preguntas del Quiz</h1>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($quiz['title']) ?></p>
        </div>
    </div>
<?php if (can('lms.quizzes.manage')): ?>
    <a href="/manager/lms/quizzes/<?= $quiz['id'] ?>/questions/add" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition shadow-md">
        <i class="fas fa-plus"></i> Añadir Pregunta
    </a>
<?php endif; ?>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Orden</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Pregunta</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Opciones</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Puntos</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($questions as $q): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 text-xs font-bold"><?= $q['order_num'] ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($q['question']) ?></p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm text-gray-600"><?= $q['option_count'] ?></span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold text-blue-600"><?= $q['points'] ?> pt</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
<?php if (can('lms.quizzes.manage')): ?>
                            <?php
                            // Si la pregunta tiene historial, la confirmación lo advierte.
                            $_answers = (int) ($q['answer_count'] ?? 0);
                            $_confirm = $_answers > 0
                                ? sprintf(
                                    "Esta pregunta ya fue respondida por %d alumno(s).\n\n"
                                    . "Al eliminarla se borrarán también esas respuestas y se perderá "
                                    . "el detalle de esas evaluaciones.\n\n¿Continuar?",
                                    $_answers
                                  )
                                : '¿Eliminar esta pregunta?';
                            ?>
                            <form method="POST" action="/manager/lms/quizzes/<?= $quiz['id'] ?>/questions/<?= $q['id'] ?>/delete" onsubmit="return confirm(<?= htmlspecialchars(json_encode($_confirm), ENT_QUOTES, 'UTF-8') ?>);" class="inline">
                                <?php echo \Core\Security::getCsrfField(); ?>
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
<?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($questions)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Este quiz aún no tiene preguntas</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
