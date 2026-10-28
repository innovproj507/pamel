<?php
/**
 * Categorías de la tienda, con sus subcategorías sangradas bajo la madre.
 *
 * El icono viene de la base como clase de Font Awesome («fa-anchor»), así que
 * esas filas siguen usando FA en lugar de Lucide: cambiarlo pediría migrar
 * el dato, no sólo la plantilla.
 */
$categories = $categories ?? [];
$total      = count($categories);
$principales = count(array_filter($categories, static fn($c) => !isset($c['is_subcategory'])));
$cursos      = array_sum(array_column($categories, 'product_count'));
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Categorías</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($principales) ?> principal<?= $principales === 1 ? '' : 'es' ?>
            y <?= number_format($total - $principales) ?> subcategoría<?= ($total - $principales) === 1 ? '' : 's' ?>
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
        <a href="/manager/categories/create" class="btn-primario">
            <i data-lucide="plus" class="ic ic-16"></i> Nueva Categoría
        </a>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div class="card card-pad mb-4 flex items-center gap-3" style="border-color:#bbf7d0; background:#f0fdf4">
    <i data-lucide="circle-check" class="ic ic-17 text-emerald-600 flex-shrink-0"></i>
    <p class="text-[12.5px] font-bold text-emerald-800"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="card card-pad mb-4 flex items-center gap-3" style="border-color:#fecaca; background:#fef2f2">
    <i data-lucide="circle-alert" class="ic ic-17 text-rose-600 flex-shrink-0"></i>
    <p class="text-[12.5px] font-bold text-rose-800"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
</div>
<?php endif; ?>

<div class="card overflow-hidden">

    <!-- Resumen -->
    <div class="px-4 py-4 grid grid-cols-3 gap-3 border-b" style="border-color: var(--card-border)">
        <div class="kpi">
            <div class="kpi-label">Categorías</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Principales</div>
            <div class="kpi-value"><?= number_format($principales) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Cursos clasificados</div>
            <div class="kpi-value"><?= number_format($cursos) ?></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th class="centro">Cursos</th>
                    <th class="centro">Orden</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="5" class="vacio">
                        <i data-lucide="tags" class="ic"></i>
                        <p>Todavía no hay categorías.</p>
                        <a href="/manager/categories/create" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Crear la primera
                        </a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($categories as $category):
                    $esSub   = isset($category['is_subcategory']);
                    $nCursos = (int) $category['product_count'];
                    $conHijas = !empty($category['has_subcategories']);
                    // Con subcategorías colgando, el borrado no procede: se avisa y se corta.
                    $aviso = $conHijas
                        ? 'Esta categoría tiene subcategorías. Elimínalas o reasígnalas antes de borrarla.'
                        : sprintf('¿Eliminar la categoría «%s»?%s', addslashes($category['name']),
                                  $nCursos ? " Sus {$nCursos} curso(s) quedarán sin categoría." : '');
                ?>
                <tr<?= $esSub ? ' style="background:#f8fafc"' : '' ?>>
                    <td>
                        <div class="flex items-center gap-3" <?= $esSub ? 'style="padding-left:26px"' : '' ?>>
                            <?php if ($esSub): ?>
                            <span class="text-slate-300 text-[13px] flex-shrink-0 -ml-5">└</span>
                            <?php endif; ?>
                            <div class="avatar-ini <?= $esSub ? 'av-cian' : '' ?>" <?= $esSub ? 'style="width:30px;height:30px"' : '' ?>>
                                <i class="fas <?= htmlspecialchars($category['icon']) ?> text-[<?= $esSub ? '12' : '15' ?>px]"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-[12.5px] text-slate-800 flex items-center gap-2 flex-wrap">
                                    <?= htmlspecialchars($category['name']) ?>
                                    <?php if ($conHijas): ?>
                                    <span class="pastilla p-cian">Con subcategorías</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($category['description'])): ?>
                                <div class="text-[11px] font-semibold text-slate-400 truncate" style="max-width:320px">
                                    <?= htmlspecialchars($category['description']) ?>
                                </div>
                                <?php endif; ?>
                                <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                    <span class="cod">
                                        <i data-lucide="link" class="ic" style="width:10px;height:10px"></i>
                                        <?= htmlspecialchars($category['slug']) ?>
                                    </span>
                                    <?php if ($esSub && isset($category['parent_name'])): ?>
                                    <span class="text-[10.5px] font-semibold text-slate-400">
                                        · dentro de <?= htmlspecialchars($category['parent_name']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="pastilla <?= $esSub ? 'p-azul' : 'p-verde' ?>">
                            <?= $esSub ? 'Subcategoría' : 'Principal' ?>
                        </span>
                    </td>
                    <td class="centro">
                        <span class="pastilla <?= $nCursos ? 'p-violeta' : 'p-gris' ?>"><?= $nCursos ?></span>
                    </td>
                    <td class="centro text-slate-400 font-bold"><?= (int) $category['display_order'] ?></td>
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/categories/<?= (int) $category['id'] ?>/edit" class="acc acc-gris" title="Editar categoría">
                                <i data-lucide="pencil" class="ic" style="width:12px;height:12px"></i> Editar
                            </a>
                            <form action="/manager/categories/<?= (int) $category['id'] ?>/delete" method="POST"
                                  onsubmit="<?= $conHijas
                                      ? 'alert(\'' . htmlspecialchars($aviso, ENT_QUOTES) . '\'); return false;'
                                      : 'return confirm(\'' . htmlspecialchars($aviso, ENT_QUOTES) . '\')' ?>">
                                <?= \Core\Security::getCsrfField() ?>
                                <button type="submit" class="acc acc-rojo <?= $conHijas ? 'opacity-50' : '' ?>"
                                        title="<?= $conHijas ? 'Primero hay que vaciar sus subcategorías' : 'Eliminar categoría' ?>">
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
</div>
