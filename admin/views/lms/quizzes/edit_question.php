<div class="mb-6 flex items-center gap-4">
    <a href="/manager/lms/quizzes/<?= $quiz['id'] ?>/questions" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Editar Pregunta</h1>
        <p class="text-sm text-gray-500">Pregunta #<?= (int) $pregunta['id'] ?> de: <?= htmlspecialchars($quiz['title']) ?></p>
    </div>
</div>

<?php if ($respondida > 0): ?>
<div class="max-w-4xl mb-6 flex items-start gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200">
    <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5"></i>
    <p class="text-sm text-amber-800">
        <span class="font-bold"><?= (int) $respondida ?> alumno(s) ya respondieron esta pregunta.</span>
        Si cambias la respuesta correcta, las notas ya calculadas <strong>no</strong> se recalculan solas:
        los intentos antiguos conservan la nota que obtuvieron.
    </p>
</div>
<?php endif; ?>

<form action="/manager/lms/quizzes/<?= $quiz['id'] ?>/questions/<?= (int) $pregunta['id'] ?>/update" method="POST" class="max-w-4xl">
    <?php echo \Core\Security::getCsrfField(); ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Texto de la Pregunta</label>
                    <textarea name="question" required rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition"><?= htmlspecialchars($pregunta['question']) ?></textarea>
                </div>
            </div>

            <!-- Opciones de Respuesta -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-navy uppercase tracking-wider">Opciones de Respuesta</h3>
                    <button type="button" onclick="addOption()" class="text-blue-600 font-bold text-xs hover:underline flex items-center gap-1">
                        <i class="fas fa-plus"></i> Añadir Opción
                    </button>
                </div>

                <div id="options-container" class="space-y-3">
                    <?php foreach ($opciones as $i => $op): ?>
                    <div class="option-row flex items-center gap-3 p-3 rounded-xl border <?= (int) $op['is_correct'] === 1 ? 'border-emerald-200 bg-emerald-50/50' : 'border-gray-100 bg-gray-50/50' ?>">
                        <input type="radio" name="correct_option" value="<?= $i ?>" <?= (int) $op['is_correct'] === 1 ? 'checked' : '' ?> class="w-4 h-4 text-emerald-500 border-gray-300 focus:ring-emerald-500">
                        <input type="text" name="options[]" required value="<?= htmlspecialchars($op['option_text']) ?>" class="flex-1 bg-transparent border-none outline-none text-sm font-medium">
                        <button type="button" onclick="removeOption(this)" class="text-gray-300 hover:text-red-500 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-[10px] text-gray-400 mt-4 italic">Marca el círculo a la izquierda de la opción correcta.</p>
            </div>
        </div>

        <!-- Sidebar Config -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Puntos</label>
                    <input type="number" name="points" value="<?= (int) $pregunta['points'] ?>" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Orden</label>
                    <input type="number" name="order_num" value="<?= (int) $pregunta['order_num'] ?>" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg shadow-blue-500/25 transition transform hover:-translate-y-0.5">
                Guardar Cambios
            </button>
            <a href="/manager/lms/quizzes/<?= $quiz['id'] ?>/questions" class="block text-center text-xs font-bold text-gray-400 hover:text-gray-600 transition">
                Cancelar
            </a>
        </div>
    </div>
</form>

<script>
    /**
     * El valor de cada radio debe coincidir siempre con la posición de su
     * campo dentro de options[], que es como el servidor identifica la
     * respuesta correcta. Se reindexa tras cada alta o baja de opción.
     */
    function reindexOptions() {
        const rows = document.querySelectorAll('#options-container .option-row');
        rows.forEach((row, i) => {
            const radio = row.querySelector('input[type="radio"]');
            if (radio) radio.value = i;
        });

        // Si se eliminó la fila marcada, la primera pasa a ser la correcta.
        if (!document.querySelector('#options-container input[type="radio"]:checked')) {
            const first = document.querySelector('#options-container input[type="radio"]');
            if (first) first.checked = true;
        }
    }

    function removeOption(button) {
        const rows = document.querySelectorAll('#options-container .option-row');
        if (rows.length <= 2) {
            alert('Una pregunta necesita al menos dos opciones.');
            return;
        }
        button.closest('.option-row').remove();
        reindexOptions();
    }

    function addOption() {
        const container = document.getElementById('options-container');
        const div = document.createElement('div');
        div.className = 'option-row flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50';
        div.innerHTML = `
            <input type="radio" name="correct_option" class="w-4 h-4 text-emerald-500 border-gray-300 focus:ring-emerald-500">
            <input type="text" name="options[]" required class="flex-1 bg-transparent border-none outline-none text-sm font-medium" placeholder="Nueva Opción">
            <button type="button" onclick="removeOption(this)" class="text-gray-300 hover:text-red-500 transition">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
        reindexOptions();
    }

    // Resaltar en verde la fila marcada como correcta
    document.addEventListener('change', (e) => {
        if (e.target.name !== 'correct_option') return;
        document.querySelectorAll('#options-container .option-row').forEach((row) => {
            const marcada = row.querySelector('input[type="radio"]').checked;
            row.classList.toggle('border-emerald-200', marcada);
            row.classList.toggle('bg-emerald-50/50', marcada);
            row.classList.toggle('border-gray-100', !marcada);
            row.classList.toggle('bg-gray-50/50', !marcada);
        });
    });

    document.addEventListener('DOMContentLoaded', reindexOptions);
</script>
