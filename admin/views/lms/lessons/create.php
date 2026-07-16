<div class="mb-6 flex items-center gap-4">
    <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Añadir Lección</h1>
        <p class="text-sm text-gray-500">Nuevo contenido para: <?= htmlspecialchars($course['title']) ?></p>
    </div>
</div>

<form action="/manager/lms/courses/<?= $course['id'] ?>/lessons/store" method="POST" class="max-w-4xl">
    <?php echo \Core\Security::getCsrfField(); ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 mb-1">Título de la Lección</label>
                <input type="text" name="title" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de Contenido</label>
                <select name="type" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                    <option value="text">Texto / Lectura</option>
                    <option value="video">Vídeo (YouTube/Vimeo)</option>
                    <option value="file">Archivo Descargable</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Orden</label>
                <input type="number" name="order_num" value="0" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Duración (minutos)</label>
                <input type="number" name="duration" value="0" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
            </div>

            <div class="flex items-center gap-2 pt-8">
                <input type="checkbox" name="is_active" id="is_active" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="text-sm font-bold text-gray-700">Lección activa / visible</label>
            </div>
        </div>

        <div id="video_field" class="hidden">
            <label class="block text-sm font-bold text-gray-700 mb-1">URL del Vídeo</label>
            <input type="text" name="video_url" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition" placeholder="https://www.youtube.com/watch?v=...">
        </div>

        <div id="file_field" class="hidden">
            <label class="block text-sm font-bold text-gray-700 mb-1">Ruta del Archivo</label>
            <input type="text" name="file_path" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition" placeholder="URL o ruta del archivo...">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Contenido de la Lección (HTML)</label>
            <textarea name="content" rows="15" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition font-mono text-sm"></textarea>
        </div>

        <div class="pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-black px-8 py-3.5 rounded-xl shadow-lg transition">
                Crear Lección
            </button>
        </div>
    </div>
</form>

<script>
    const typeSelect = document.querySelector('select[name="type"]');
    const videoField = document.getElementById('video_field');
    const fileField = document.getElementById('file_field');

    typeSelect.addEventListener('change', () => {
        videoField.classList.toggle('hidden', typeSelect.value !== 'video');
        fileField.classList.toggle('hidden', typeSelect.value !== 'file');
    });
</script>
