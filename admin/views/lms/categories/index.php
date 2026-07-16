<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Categorías LMS</h1>
    <p class="text-sm text-gray-500">Organiza tus cursos por áreas de especialidad</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Formulario de Creación -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-black text-navy uppercase tracking-wider mb-4">Añadir Nueva Categoría</h3>
            <form action="/manager/lms/categories/store" method="POST" class="space-y-4">
                <?php echo \Core\Security::getCsrfField(); ?>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="name" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition" placeholder="Ej: Seguridad Marítima">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition" placeholder="Breve descripción..."></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 rounded-lg transition shadow-md">
                    Guardar Categoría
                </button>
            </form>
        </div>
    </div>

    <!-- Lista de Categorías -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Cursos</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($categories as $cat): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($cat['name']) ?></p>
                            <p class="text-xs text-gray-400 font-medium">Slug: <?= htmlspecialchars($cat['slug']) ?></p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold">
                                <?= $cat['course_count'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="/manager/lms/categories/<?= $cat['id'] ?>/delete" onsubmit="return confirm('¿Eliminar categoría? Los cursos asociados quedarán sin categoría.');" class="inline">
                                <?php echo \Core\Security::getCsrfField(); ?>
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
