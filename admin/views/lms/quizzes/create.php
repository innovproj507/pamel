<div class="mb-6 flex items-center gap-4">
    <a href="/manager/lms/quizzes" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Quiz</h1>
        <p class="text-sm text-gray-500">Configura los parámetros de la evaluación</p>
    </div>
</div>

<form action="/manager/lms/quizzes/store" method="POST" class="max-w-3xl">
    <?php echo \Core\Security::getCsrfField(); ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Título del Quiz</label>
            <input type="text" name="title" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition" placeholder="Ej: Examen Final de Navegación">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Curso Asociado</label>
            <select name="course_id" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                <option value="">-- Seleccionar Curso --</option>
                <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>" <?= (!empty($selectedCourseId) && $selectedCourseId == $course['id']) ? 'selected' : '' ?>><?= htmlspecialchars($course['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Descripción / Instrucciones</label>
            <textarea name="description" rows="4" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition" placeholder="Instrucciones para los estudiantes..."></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Puntaje Mínimo de Aprobación (%)</label>
                <input type="number" name="pass_percentage" value="70" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Límite de Tiempo (minutos)</label>
                <input type="number" name="time_limit" value="0" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                <p class="text-[10px] text-gray-400 mt-1">Usa 0 para sin límite de tiempo.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="is_active" class="text-sm font-bold text-gray-700">Quiz activo / visible</label>
        </div>

        <div class="pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-8 py-3.5 rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                Crear Quiz
            </button>
        </div>
    </div>
</form>
