<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <a href="/manager/lms/quizzes" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Quiz</h1>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($quiz['title']) ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Formulario de Configuración -->
    <div class="lg:col-span-2 space-y-6">
        <form action="/manager/lms/quizzes/<?= $quiz['id'] ?>/update" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
            <?php echo \Core\Security::getCsrfField(); ?>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Título del Quiz</label>
                <input type="text" name="title" required value="<?= htmlspecialchars($quiz['title']) ?>" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Curso Asociado</label>
                <select name="course_id" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                    <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= (int)$quiz['course_id'] === (int)$course['id'] ? 'selected' : '' ?>><?= htmlspecialchars($course['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Descripción / Instrucciones</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition"><?= htmlspecialchars($quiz['description'] ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Puntaje Mínimo de Aprobación (%)</label>
                    <input type="number" name="pass_percentage" value="<?= $quiz['pass_percentage'] ?>" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Límite de Tiempo (minutos)</label>
                    <input type="number" name="time_limit" value="<?= $quiz['time_limit'] ?>" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" <?= $quiz['is_active'] ? 'checked' : '' ?> class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="text-sm font-bold text-gray-700">Quiz activo / visible</label>
            </div>

            <div class="pt-4">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-8 py-3.5 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                    Actualizar Quiz
                </button>
            </div>
        </form>
    </div>

    <!-- Gestión de Preguntas (Próximamente) -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-black text-navy uppercase tracking-wider mb-4">Contenido del Quiz</h3>
            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 text-center">
                    <p class="text-xs text-gray-500 font-medium mb-3">Este quiz tiene <?= $quiz['question_count'] ?? 0 ?> preguntas.</p>
                    <a href="/manager/lms/quizzes/<?= $quiz['id'] ?>/questions" class="inline-flex items-center gap-2 text-blue-600 font-bold text-sm hover:underline">
                        <i class="fas fa-list-check"></i> Gestionar Preguntas
                    </a>
                </div>
            </div>
        </div>
        
        <div class="bg-amber-50 rounded-xl p-6 border border-amber-100">
            <h4 class="text-amber-800 font-black text-sm flex items-center gap-2 mb-2">
                <i class="fas fa-lightbulb"></i> Tip Administrativo
            </h4>
            <p class="text-xs text-amber-700 leading-relaxed">
                Asegúrate de que el curso asociado tenga todas sus lecciones publicadas antes de activar el quiz para los estudiantes.
            </p>
        </div>
    </div>
</div>
