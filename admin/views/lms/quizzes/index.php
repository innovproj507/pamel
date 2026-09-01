<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Quizzes</h1>
        <p class="text-sm text-gray-500">Administra las evaluaciones de tus cursos</p>
    </div>
<?php if (can('lms.quizzes.manage')): ?>
    <a href="/manager/lms/quizzes/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition shadow-md">
        <i class="fas fa-plus"></i> Crear Nuevo Quiz
    </a>
<?php endif; ?>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Quiz</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Curso</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Preguntas</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aprobación</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($quizzes as $quiz): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($quiz['title']) ?></p>
                        <?php if ($quiz['time_limit']): ?>
                        <p class="text-xs text-amber-600 font-bold flex items-center gap-1 mt-0.5">
                            <i class="fas fa-clock text-[10px]"></i> <?= $quiz['time_limit'] ?> min
                        </p>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600 font-medium"><?= htmlspecialchars($quiz['course_title'] ?? 'Sin curso') ?></span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 text-xs font-bold inline-flex items-center justify-center">
                            <?= $quiz['question_count'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-sm font-bold text-gray-700"><?= $quiz['pass_percentage'] ?>%</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase <?= $quiz['is_active'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' ?>">
                            <?= $quiz['is_active'] ? 'Activo' : 'Oculto' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
<?php if (can('lms.quizzes.manage')): ?>
                            <a href="/manager/lms/quizzes/<?= $quiz['id'] ?>/edit" class="p-2 text-gray-400 hover:text-blue-600 transition" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php
                            // El borrado arrastra preguntas, opciones e intentos.
                            $_qCount = (int) ($quiz['question_count'] ?? 0);
                            $_confirmQuiz = $_qCount > 0
                                ? sprintf(
                                    "Se eliminará el quiz «%s» junto con sus %d pregunta(s), "
                                    . "sus opciones y los intentos de alumnos asociados.\n\n¿Continuar?",
                                    $quiz['title'],
                                    $_qCount
                                  )
                                : sprintf('¿Eliminar el quiz «%s»?', $quiz['title']);
                            ?>
                            <form method="POST" action="/manager/lms/quizzes/<?= $quiz['id'] ?>/delete" onsubmit="return confirm(<?= htmlspecialchars(json_encode($_confirmQuiz), ENT_QUOTES, 'UTF-8') ?>);" class="inline">
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
                <?php if (empty($quizzes)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">No hay quizzes registrados</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
