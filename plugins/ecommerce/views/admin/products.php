<?php
/** Listado de productos (los cursos de la tienda). */
$products    = $products   ?? [];
$categories  = $categories ?? [];
$branches    = $branches   ?? [];
$filters     = $filters    ?? [];
$total       = (int) ($total ?? 0);
$currentPage = (int) ($currentPage ?? 1);
$totalPages  = (int) ($totalPages ?? 1);
$perPage     = (int) ($perPage ?? 20);

$busqueda  = (string) ($filters['search'] ?? '');
$selEstado = (string) ($filters['status'] ?? '');
$selCat    = (string) ($filters['category_id'] ?? '');
$selBranch = (string) ($filters['branch_id'] ?? '');
$hayFiltros = !empty($filters);

// Recuentos de la página visible; el total sí es el del filtro completo.
$vinculados = count(array_filter($products, static fn($p) => !empty($p['lms_course_id'])));
$activos    = count(array_filter($products, static fn($p) => $p['status'] === 'active'));
$stockBajo  = count(array_filter($products, static fn($p) => (int) $p['stock'] < 10));
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Productos</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> producto<?= $total === 1 ? '' : 's' ?> encontrado<?= $total === 1 ? '' : 's' ?>
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
        <a href="/manager/products/create" class="btn-primario">
            <i data-lucide="plus" class="ic ic-16"></i> Nuevo Producto
        </a>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Barra de filtros -->
    <form method="GET" action="/manager/products"
          class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <div>
            <label class="filtro-rot" for="f-q">Buscar</label>
            <div class="relative">
                <i data-lucide="search" class="ic ic-14 absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" id="f-q" name="search" value="<?= htmlspecialchars($busqueda, ENT_QUOTES) ?>"
                       placeholder="Nombre o código…"
                       class="campo" style="padding-left:32px; width:230px; border-radius:999px">
            </div>
        </div>

        <div>
            <label class="filtro-rot" for="f-estado">Estado</label>
            <select id="f-estado" name="status" class="campo" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="active"   <?= $selEstado === 'active'   ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= $selEstado === 'inactive' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>

        <div>
            <label class="filtro-rot" for="f-categoria">Categoría</label>
            <select id="f-categoria" name="category_id" class="campo" onchange="this.form.submit()" style="max-width:220px">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>" <?= $selCat === (string) $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="filtro-rot" for="f-rama">Rama</label>
            <select id="f-rama" name="branch_id" class="campo" onchange="this.form.submit()" style="max-width:200px">
                <option value="">Todas las ramas</option>
                <?php foreach ($branches as $branch): ?>
                <option value="<?= (int) $branch['id'] ?>" <?= $selBranch === (string) $branch['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($branch['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-sec">
            <i data-lucide="search" class="ic ic-14"></i> Filtrar
        </button>

        <?php if ($hayFiltros): ?>
        <a href="/manager/products" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Limpiar
        </a>
        <?php endif; ?>

        <input type="hidden" name="per_page" value="<?= $perPage ?>">
    </form>

    <!-- Resumen -->
    <div class="px-4 py-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="kpi">
            <div class="kpi-label">Productos</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Activos (esta página)</div>
            <div class="kpi-value"><?= number_format($activos) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Con curso LMS</div>
            <div class="kpi-value"><?= number_format($vinculados) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Stock bajo</div>
            <div class="kpi-value"><?= number_format($stockBajo) ?></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="centro">Curso LMS</th>
                    <th class="num">Stock</th>
                    <th>Estado</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="6" class="vacio">
                        <i data-lucide="package-open" class="ic"></i>
                        <p>No se encontraron productos.</p>
                        <a href="<?= $hayFiltros ? '/manager/products' : '/manager/products/create' ?>"
                           class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            <?= $hayFiltros ? 'Limpiar filtros' : 'Crear el primero' ?>
                        </a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product):
                    $stock  = (int) $product['stock'];
                    $activo = $product['status'] === 'active';
                ?>
                <tr>

                    <!-- Producto: miniatura, nombre y código -->
                    <td>
                        <div class="flex items-center gap-3">
                            <?php if (!empty($product['image'])): ?>
                            <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>" alt="" class="miniatura">
                            <?php else: ?>
                            <div class="miniatura vacia">
                                <i data-lucide="image" class="ic ic-14"></i>
                            </div>
                            <?php endif; ?>
                            <div class="min-w-0">
                                <div class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:320px"
                                     title="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </div>
                                <?php if (!empty($product['course_code'])): ?>
                                <div class="mt-1">
                                    <span class="cod">
                                        <i data-lucide="tag" class="ic" style="width:10px;height:10px"></i>
                                        <?= htmlspecialchars($product['course_code']) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    <!-- Categoría -->
                    <td>
                        <?php if (!empty($product['category_name'])): ?>
                        <span class="pastilla p-violeta" title="<?= htmlspecialchars($product['category_name'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars(mb_strimwidth($product['category_name'], 0, 26, '…')) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-slate-300">—</span>
                        <?php endif; ?>
                    </td>

                    <!-- Curso del LMS vinculado -->
                    <td class="centro">
                        <?php if (!empty($product['lms_course_id'])): ?>
                        <a href="/manager/lms/courses/<?= (int) $product['lms_course_id'] ?>/edit" class="pastilla p-verde"
                           title="Abrir el curso vinculado">
                            <i data-lucide="check" class="ic" style="width:10px;height:10px"></i> Vinculado
                        </a>
                        <?php else: ?>
                        <a href="/manager/lms/courses/create?product_id=<?= (int) $product['id'] ?>" class="pastilla p-gris"
                           title="Crear el curso para este producto">
                            <i data-lucide="plus" class="ic" style="width:10px;height:10px"></i> Sin curso
                        </a>
                        <?php endif; ?>
                    </td>

                    <!-- Stock -->
                    <td class="num whitespace-nowrap">
                        <span class="font-bold <?= $stock < 10 ? 'text-amber-600' : 'text-slate-700' ?>"><?= $stock ?></span>
                        <?php if ($stock < 10): ?>
                        <i data-lucide="triangle-alert" class="ic inline-block text-amber-400 ml-0.5"
                           style="width:11px;height:11px" title="Stock bajo"></i>
                        <?php endif; ?>
                    </td>

                    <!-- Estado -->
                    <td>
                        <span class="pastilla <?= $activo ? 'p-verde' : 'p-rojo' ?>">
                            <span class="punto"></span><?= $activo ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>

                    <!-- Acciones -->
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/products/<?= (int) $product['id'] ?>/edit" class="acc acc-gris" title="Editar producto">
                                <i data-lucide="pencil" class="ic" style="width:12px;height:12px"></i> Editar
                            </a>
                            <form action="/manager/products/<?= (int) $product['id'] ?>/delete" method="POST"
                                  onsubmit="return confirm('¿Eliminar el producto «<?= htmlspecialchars(addslashes($product['name']), ENT_QUOTES) ?>»? Esta acción no se puede deshacer.')">
                                <?= \Core\Security::getCsrfField() ?>
                                <button type="submit" class="acc acc-rojo" title="Eliminar producto">
                                    <i data-lucide="trash-2" class="ic" style="width:12px;height:12px"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    $ruta     = '/manager/products';
    $page     = $currentPage;
    $lastPage = $totalPages;
    $filtros  = [
        'search'      => $busqueda,
        'status'      => $selEstado,
        'category_id' => $selCat,
        'branch_id'   => $selBranch,
    ];
    include \Core\Config::get('paths.root') . '/admin/views/partials/paginador.php';
    ?>
</div>
