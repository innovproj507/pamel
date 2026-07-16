<div class="mb-6 flex items-center gap-4">
    <a href="/manager/lms/quizzes/<?= $quiz['id'] ?>/questions" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Añadir Pregunta</h1>
        <p class="text-sm text-gray-500">Nuevo contenido para: <?= htmlspecialchars($quiz['title']) ?></p>
    </div>
</div>

<form action="/manager/lms/quizzes/<?= $quiz['id'] ?>/questions/store" method="POST" class="max-w-4xl">
    <?php echo \Core\Security::getCsrfField(); ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Texto de la Pregunta</label>
                    <textarea name="question" required rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition" placeholder="Escribe la pregunta aquí..."></textarea>
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
                    <!-- Opciones dinámicas aquí -->
                    <div class="option-row flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50">
                        <input type="radio" name="correct_option" value="0" checked class="w-4 h-4 text-emerald-500 border-gray-300 focus:ring-emerald-500">
                        <input type="text" name="options[]" required class="flex-1 bg-transparent border-none outline-none text-sm font-medium" placeholder="Opción A (Correcta)">
                    </div>
                    <div class="option-row flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50">
                        <input type="radio" name="correct_option" value="1" class="w-4 h-4 text-emerald-500 border-gray-300 focus:ring-emerald-500">
                        <input type="text" name="options[]" required class="flex-1 bg-transparent border-none outline-none text-sm font-medium" placeholder="Opción B">
                        <button type="button" onclick="this.parentElement.remove()" class="text-gray-300 hover:text-red-500 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 mt-4 italic">Marca el círculo a la izquierda de la opción correcta.</p>
            </div>
        </div>

        <!-- Sidebar Config -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Puntos</label>
                    <input type="number" name="points" value="1" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Orden</label>
                    <input type="number" name="order_num" value="0" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg shadow-blue-500/25 transition transform hover:-translate-y-0.5">
                Guardar Pregunta
            </button>
        </div>
    </div>
</form>

<script>
    let optionCount = 2;

    function addOption() {
        const container = document.getElementById('options-container');
        const div = document.createElement('div');
        div.className = 'option-row flex items-center gap-3 p-3 rounded-xl border border-gray-100 bg-gray-50/50';
        div.innerHTML = `
            <input type="radio" name="correct_option" value="${optionCount}" class="w-4 h-4 text-emerald-500 border-gray-300 focus:ring-emerald-500">
            <input type="text" name="options[]" required class="flex-1 bg-transparent border-none outline-none text-sm font-medium" placeholder="Nueva Opción">
            <button type="button" onclick="this.parentElement.remove()" class="text-gray-300 hover:text-red-500 transition">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
        optionCount++;
    }
</script>
