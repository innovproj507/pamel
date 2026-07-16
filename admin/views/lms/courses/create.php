<div class="mb-6 flex items-center gap-4">
    <a href="/manager/lms/courses" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Curso</h1>
        <p class="text-sm text-gray-500">Configura los detalles del nuevo programa formativo</p>
    </div>
</div>

<form action="/manager/lms/courses/store" method="POST" class="max-w-4xl">
    <?php echo \Core\Security::getCsrfField(); ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Título del Curso</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition" placeholder="Ej: Patrón de Navegación Básica">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Slug (URL)</label>
                    <input type="text" name="slug" class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 outline-none" placeholder="Opcional: se generará automáticamente">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                    <textarea name="description" rows="6" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition" placeholder="Describe los objetivos y contenido del curso..."></textarea>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-black text-navy uppercase tracking-wider mb-4">Imagen del Curso</h3>
                <div class="space-y-3">
                    <input type="text" name="image" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition" placeholder="URL de la imagen (ej: https://...)">
                    <p class="text-[10px] text-gray-400">Introduce la URL de la imagen que representará al curso en el catálogo.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Config -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Estado</label>
                    <select name="status" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                        <option value="draft">Borrador</option>
                        <option value="published">Publicado</option>
                        <option value="archived">Archivado</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nivel</label>
                    <select name="level" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                        <option value="beginner">Principiante</option>
                        <option value="intermediate">Intermedio</option>
                        <option value="advanced">Avanzado</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Categoría</label>
                    <select name="category_id" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Profesor Responsable</label>
                    <select name="teacher_id" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                        <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= $teacher['id'] ?>"><?= htmlspecialchars($teacher['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Precio ($)</label>
                    <p class="text-[10px] text-gray-400 mb-1">Se ignora si vinculas un producto</p>
                    <input type="number" name="price" step="0.01" value="0.00" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">% Aprobación</label>
                    <input type="number" name="pass_percentage" value="70" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 space-y-3">
                <label class="block text-xs font-black text-blue-700 uppercase tracking-widest">Vincular Producto</label>
                <p class="text-[10px] text-slate-500">Precio e imagen se toman del producto. Si el producto se desactiva, el curso desaparece del elearning.</p>
                <select name="product_id" class="w-full px-4 py-2 rounded-lg border border-blue-200 bg-white text-sm font-semibold text-slate-700 focus:border-blue-500 outline-none transition">
                    <option value="">— Sin producto —</option>
                    <?php foreach ($products as $prod): ?>
                    <option value="<?= $prod['id'] ?>">
                        <?= htmlspecialchars("[{$prod['course_code']}] {$prod['name']}") ?>
                        <?= $prod['status'] === 'inactive' ? ' ⚠ inactivo' : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl shadow-lg shadow-blue-500/25 transition transform hover:-translate-y-0.5">
                Guardar Curso
            </button>
        </div>
    </div>
</form>
