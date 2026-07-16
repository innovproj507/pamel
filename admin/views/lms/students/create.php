<div class="mb-6 flex items-center gap-4">
    <a href="/manager/lms/students" class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
        <i class="fas fa-arrow-left text-sm"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Inscribir en Curso</h1>
        <p class="text-sm text-gray-500">Asigna manualmente un estudiante a un curso</p>
    </div>
</div>

<div class="max-w-xl">
    <form action="/manager/lms/students/store" method="POST" class="space-y-5">
        <?php echo \Core\Security::getCsrfField(); ?>
        <input type="hidden" name="_back_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

        <!-- Student -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                <i class="fas fa-user mr-1"></i>Estudiante
            </label>

            <?php if ($preselectedStudent): ?>
                <!-- Pre-selected: show as card, hidden input -->
                <input type="hidden" name="student_id" value="<?= $preselectedStudent['id'] ?>">
                <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                        <?= strtoupper(substr($preselectedStudent['name'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm"><?= htmlspecialchars($preselectedStudent['name']) ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($preselectedStudent['email']) ?></div>
                    </div>
                    <a href="/manager/lms/students/create" class="ml-auto text-xs text-blue-600 hover:underline">Cambiar</a>
                </div>
            <?php else: ?>
                <select name="student_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">— Seleccionar estudiante —</option>
                    <?php foreach ($students as $s):
                        $alreadyIn = in_array($s['id'], $enrolledStudentIds ?? []);
                    ?>
                    <option value="<?= $s['id'] ?>" <?= $alreadyIn ? 'disabled' : '' ?>>
                        <?= htmlspecialchars($s['name']) ?> — <?= htmlspecialchars($s['email']) ?>
                        <?= $alreadyIn ? '(Ya inscrito)' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <!-- Course -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                <i class="fas fa-graduation-cap mr-1"></i>Curso
            </label>

            <?php if (!empty($preselectedCourse)): ?>
                <input type="hidden" name="course_id" value="<?= $preselectedCourse['id'] ?>">
                <div class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-200 rounded-lg">
                    <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-sm"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 text-sm"><?= htmlspecialchars($preselectedCourse['title']) ?></div>
                        <div class="text-xs text-gray-500">
                            <?= htmlspecialchars($preselectedCourse['course_code'] ?? '') ?>
                            <?php if ($preselectedCourse['category_name']): ?>— <?= htmlspecialchars($preselectedCourse['category_name']) ?><?php endif; ?>
                        </div>
                    </div>
                    <a href="/manager/lms/students/create" class="ml-auto text-xs text-emerald-600 hover:underline">Cambiar</a>
                </div>
            <?php else: ?>
                <select name="course_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">— Seleccionar curso —</option>
                    <?php foreach ($courses as $c):
                        $alreadyEnrolled = in_array($c['id'], $enrolled ?? []);
                    ?>
                    <option value="<?= $c['id'] ?>" <?= $alreadyEnrolled ? 'disabled' : '' ?>>
                        [<?= htmlspecialchars($c['course_code'] ?? '—') ?>]
                        <?= htmlspecialchars($c['title']) ?>
                        <?php if ($c['category_name']): ?>— <?= htmlspecialchars($c['category_name']) ?><?php endif; ?>
                        <?php if ($c['status'] === 'draft'): ?>(Borrador)<?php endif; ?>
                        <?php if ($alreadyEnrolled): ?>(Ya inscrito)<?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-3 p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700">
            <i class="fas fa-info-circle text-amber-500 text-base flex-shrink-0"></i>
            La inscripción manual da acceso inmediato al curso. No genera cobro ni factura.
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg transition shadow">
                <i class="fas fa-check mr-2"></i>Confirmar Inscripción
            </button>
            <a href="/manager/lms/students"
               class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold px-6 py-2.5 rounded-lg transition text-sm">
                Cancelar
            </a>
        </div>

    </form>
</div>
