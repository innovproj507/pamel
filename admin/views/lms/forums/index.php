<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Foros</h1>
    <p class="text-sm text-gray-500">Crea y administra los foros de discusión del elearning</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Formulario de Creación -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-black text-navy uppercase tracking-wider mb-4">Nuevo Foro</h3>
            <form action="/manager/lms/forums/store" method="POST" class="space-y-4">
                <?php echo \Core\Security::getCsrfField(); ?>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Título <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition" placeholder="Ej: Seguridad Marítima">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition" placeholder="Breve descripción del foro..."></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Orden</label>
                    <input type="number" name="order_num" value="0" min="0" class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 outline-none transition">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-2.5 rounded-lg transition shadow-md">
                    Crear Foro
                </button>
            </form>
        </div>
    </div>

    <!-- Lista de Foros -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Foro</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Temas</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Estado</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($forums)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-sm">No hay foros creados aún.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($forums as $forum): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($forum['title']) ?></p>
                            <?php if ($forum['description']): ?>
                            <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($forum['description']) ?></p>
                            <?php endif; ?>
                            <p class="text-xs text-gray-300 mt-0.5">Slug: <?= htmlspecialchars($forum['slug']) ?></p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold">
                                <?= $forum['topics_count'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if ($forum['is_active']): ?>
                                <span class="px-2.5 py-1 rounded-full bg-green-50 text-green-600 text-xs font-bold">Activo</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-bold">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right flex items-center justify-end gap-1">
                            <form method="POST" action="/manager/lms/forums/<?= $forum['id'] ?>/toggle" class="inline">
                                <?php echo \Core\Security::getCsrfField(); ?>
                                <button type="submit" title="<?= $forum['is_active'] ? 'Desactivar' : 'Activar' ?>" class="p-2 text-gray-400 hover:text-yellow-600 transition">
                                    <i class="fas <?= $forum['is_active'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" action="/manager/lms/forums/<?= $forum['id'] ?>/delete"
                                  onsubmit="return confirm('¿Eliminar este foro y todos sus temas?');" class="inline">
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
