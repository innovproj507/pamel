<div>
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Products / Courses</h1>
            <p class="text-gray-500 text-sm mt-0.5">Manage all maritime training courses</p>
        </div>
        <a href="/manager/products/create" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-2.5 px-5 rounded-lg transition shadow flex items-center gap-2 text-sm">
            <i class="fas fa-plus"></i>Add New Course
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" action="/manager/products" class="flex flex-wrap gap-2 mb-4">
        <div class="relative flex-1 min-w-[180px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" name="search"
                   value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>"
                   placeholder="Nombre o código..."
                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400">
        </div>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 bg-white">
            <option value="">Todos los estados</option>
            <option value="active"   <?php echo ($filters['status'] ?? '') === 'active'   ? 'selected' : ''; ?>>Activo</option>
            <option value="inactive" <?php echo ($filters['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
        </select>

        <?php
        $selCat = (string)($filters['category_id'] ?? '');
        $selSub = (string)($filters['subcategory_id'] ?? '');
        // Build subcategory map as JSON for JS
        $subMap = [];
        foreach ($subcategories as $s) {
            $subMap[$s['parent_id']][] = ['id' => $s['id'], 'name' => $s['name']];
        }
        ?>
        <!-- Parent category -->
        <select id="filter-category" name="category_id"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 bg-white">
            <option value="">Todas las categorías</option>
            <?php foreach ($parentCats as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"
                    <?php echo $selCat === (string)$cat['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Subcategory (hidden when no parent selected) -->
        <select id="filter-subcategory" name="subcategory_id"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 bg-white <?php echo $selCat === '' ? 'hidden' : ''; ?>">
            <option value="">Todas las subcategorías</option>
            <?php foreach ($subcategories as $s): ?>
                <option value="<?php echo $s['id']; ?>"
                        data-parent="<?php echo $s['parent_id']; ?>"
                    <?php echo $selSub === (string)$s['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($s['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fas fa-filter mr-1"></i>Filtrar
        </button>
        <?php if (!empty($filters)): ?>
            <a href="/manager/products" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1">
                <i class="fas fa-times"></i>Limpiar
            </a>
        <?php endif; ?>
    </form>

    <script>
    (function () {
        const catSel = document.getElementById('filter-category');
        const subSel = document.getElementById('filter-subcategory');
        const allOpts = Array.from(subSel.querySelectorAll('option[data-parent]'));

        function updateSubs(parentId) {
            allOpts.forEach(opt => {
                opt.hidden = parentId !== '' && opt.dataset.parent !== parentId;
            });
            if (parentId === '') {
                subSel.classList.add('hidden');
                subSel.value = '';
            } else {
                subSel.classList.remove('hidden');
            }
        }

        catSel.addEventListener('change', function () {
            updateSubs(this.value);
        });

        // Init on page load
        updateSubs(catSel.value);
    })();
    </script>

    <?php if (empty($products)): ?>
        <div class="text-center py-20 text-gray-400">
            <i class="fas fa-box-open text-5xl mb-3"></i>
            <p class="text-base">No se encontraron productos.</p>
            <a href="/manager/products/create" class="mt-3 inline-block text-cyan-600 hover:underline text-sm font-medium">
                <i class="fas fa-plus mr-1"></i>Crear el primero
            </a>
        </div>
    <?php else: ?>

        <!-- Table — table-fixed keeps it inside its container, no overflow -->
        <table class="w-full table-fixed border-collapse text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-xs uppercase tracking-wider">
                    <th class="w-14 px-3 py-3 text-left">Img</th>
                    <th class="px-3 py-3 text-left">Curso</th>
                    <th class="w-28 px-3 py-3 text-left">Categoría</th>
                    <th class="w-16 px-3 py-3 text-center">Stock</th>
                    <th class="w-20 px-3 py-3 text-center">Estado</th>
                    <th class="w-32 px-3 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <!-- Image -->
                    <td class="px-3 py-3">
                        <?php if (!empty($product['image'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                 alt=""
                                 class="w-10 h-10 object-cover rounded-lg border border-gray-200">
                        <?php else: ?>
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-300 text-sm"></i>
                            </div>
                        <?php endif; ?>
                    </td>

                    <!-- Name + code -->
                    <td class="px-3 py-3 min-w-0">
                        <div class="font-semibold text-gray-900 truncate" title="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </div>
                        <?php if (!empty($product['course_code'])): ?>
                            <span class="text-xs text-gray-500 font-mono bg-gray-100 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                                <?php echo htmlspecialchars($product['course_code']); ?>
                            </span>
                        <?php endif; ?>
                    </td>

                    <!-- Category -->
                    <td class="px-3 py-3">
                        <?php if (!empty($product['category_name'])): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 truncate max-w-full">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400 text-xs">—</span>
                        <?php endif; ?>
                    </td>

                    <!-- Stock -->
                    <td class="px-3 py-3 text-center">
                        <span class="text-sm font-medium text-gray-700"><?php echo $product['stock']; ?></span>
                        <?php if ($product['stock'] < 10): ?>
                            <i class="fas fa-exclamation-triangle text-amber-400 text-xs ml-0.5" title="Stock bajo"></i>
                        <?php endif; ?>
                    </td>

                    <!-- Status -->
                    <td class="px-3 py-3 text-center">
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full <?php echo $product['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                            <?php echo $product['status'] === 'active' ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="px-3 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="/manager/products/<?php echo $product['id']; ?>/edit"
                               class="text-cyan-600 hover:text-cyan-800 text-xs font-semibold flex items-center gap-1">
                                <i class="fas fa-edit"></i>Editar
                            </a>
                            <span class="text-gray-300">|</span>
                            <form action="/manager/products/<?php echo $product['id']; ?>/delete" method="POST"
                                  onsubmit="return confirm('¿Eliminar este producto?')">
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold flex items-center gap-1">
                                    <i class="fas fa-trash"></i>Borrar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($totalPages > 1):
            $queryBase = http_build_query(array_filter([
                'search'         => $filters['search'] ?? '',
                'status'         => $filters['status'] ?? '',
                'category_id'    => $filters['category_id'] ?? '',
                'subcategory_id' => $filters['subcategory_id'] ?? '',
            ]));
            $queryBase = $queryBase ? '&' . $queryBase : '';
        ?>
        <div class="mt-4 flex items-center justify-between">
            <p class="text-xs text-gray-500">
                <?php echo (($currentPage - 1) * $perPage) + 1; ?>–<?php echo min($currentPage * $perPage, $total); ?>
                de <?php echo $total; ?> resultados
            </p>
            <div class="flex items-center gap-1">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?php echo $currentPage - 1; ?><?php echo $queryBase; ?>"
                       class="px-3 py-1.5 border border-gray-300 rounded text-xs text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                $start = max(1, $currentPage - 2);
                $end   = min($totalPages, $currentPage + 2);
                if ($start > 1): ?>
                    <a href="?page=1<?php echo $queryBase; ?>" class="px-3 py-1.5 border border-gray-300 rounded text-xs text-gray-600 hover:bg-gray-50">1</a>
                    <?php if ($start > 2): ?><span class="px-1 text-gray-400 text-xs">…</span><?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i === $currentPage): ?>
                        <span class="px-3 py-1.5 bg-cyan-500 text-white rounded text-xs font-bold"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?><?php echo $queryBase; ?>" class="px-3 py-1.5 border border-gray-300 rounded text-xs text-gray-600 hover:bg-gray-50"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?><span class="px-1 text-gray-400 text-xs">…</span><?php endif; ?>
                    <a href="?page=<?php echo $totalPages; ?><?php echo $queryBase; ?>" class="px-3 py-1.5 border border-gray-300 rounded text-xs text-gray-600 hover:bg-gray-50"><?php echo $totalPages; ?></a>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?php echo $currentPage + 1; ?><?php echo $queryBase; ?>"
                       class="px-3 py-1.5 border border-gray-300 rounded text-xs text-gray-600 hover:bg-gray-50">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
