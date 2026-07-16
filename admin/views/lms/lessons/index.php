<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <a href="/manager/lms/courses/<?= $course['id'] ?>/edit" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Lecciones del Curso</h1>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($course['title']) ?></p>
        </div>
    </div>
    <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/create" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition shadow-md">
        <i class="fas fa-plus"></i> Añadir Lección
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Orden</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Título</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Duración</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($lessons as $lesson): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 text-xs font-bold"><?= $lesson['order_num'] ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($lesson['title']) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2 py-1 rounded bg-gray-100 text-gray-600">
                            <i class="fas <?= $lesson['type'] === 'video' ? 'fa-play-circle' : ($lesson['type'] === 'file' ? 'fa-paperclip' : 'fa-file-alt') ?> opacity-50"></i>
                            <?= ucfirst($lesson['type']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <?= $lesson['duration'] ?> min
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-[10px] font-black uppercase <?= $lesson['is_active'] ? 'bg-emerald-100 text-emerald-600' : 'bg-gray-100 text-gray-400' ?>">
                            <?= $lesson['is_active'] ? 'Activa' : 'Oculta' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/<?= $lesson['id'] ?>/edit" class="p-2 text-gray-400 hover:text-blue-600 transition">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="/manager/lms/courses/<?= $course['id'] ?>/lessons/<?= $lesson['id'] ?>/delete" onsubmit="return confirm('¿Eliminar esta lección?');" class="inline">
                                <?php echo \Core\Security::getCsrfField(); ?>
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($lessons)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">Este curso aún no tiene lecciones</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
