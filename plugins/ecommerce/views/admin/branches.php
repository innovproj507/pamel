<?php
/**
 * Ramas de la tienda.
 *
 * El icono viene de la base como clase de Font Awesome («fa-ship»), así que
 * esas filas siguen usando FA en lugar de Lucide: cambiarlo pediría migrar
 * el dato, no sólo la plantilla.
 */
$branches = $branches ?? [];
$total    = count($branches);
$cursos   = array_sum(array_column($branches, 'product_count'));
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Ramas</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> rama<?= $total === 1 ? '' : 's' ?> de formación
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
        <a href="/manager/branches/create" class="btn-primario">
            <i data-lucide="plus" class="ic ic-16"></i> Nueva Rama
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
    <div class="px-4 py-4 grid grid-cols-2 gap-3 border-b" style="border-color: var(--card-border)">
        <div class="kpi">
            <div class="kpi-label">Ramas</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
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
                    <th>Rama</th>
                    <th class="centro">Cursos</th>
                    <th class="centro">Orden</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($branches)): ?>
                <tr>
                    <td colspan="4" class="vacio">
                        <i data-lucide="git-branch" class="ic"></i>
                        <p>Todavía no hay ramas.</p>
                        <a href="/manager/branches/create" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Crear la primera
                        </a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($branches as $branch):
                    $nCursos = (int) $branch['product_count'];
                ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar-ini av-cian">
                                <i class="fas <?= htmlspecialchars($branch['icon']) ?> text-[15px]"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-[12.5px] text-slate-800"><?= htmlspecialchars($branch['name']) ?></div>
                                <?php if (!empty($branch['description'])): ?>
                                <div class="text-[11px] font-semibold text-slate-400 truncate" style="max-width:340px">
                                    <?= htmlspecialchars($branch['description']) ?>
                                </div>
                                <?php endif; ?>
                                <div class="mt-1">
                                    <span class="cod">
                                        <i data-lucide="link" class="ic" style="width:10px;height:10px"></i>
                                        <?= htmlspecialchars($branch['slug']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="centro">
                        <span class="pastilla <?= $nCursos ? 'p-violeta' : 'p-gris' ?>"><?= $nCursos ?></span>
                    </td>
                    <td class="centro text-slate-400 font-bold"><?= (int) $branch['display_order'] ?></td>
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/branches/<?= (int) $branch['id'] ?>/edit" class="acc acc-gris" title="Editar rama">
                                <i data-lucide="pencil" class="ic" style="width:12px;height:12px"></i> Editar
                            </a>
                            <form action="/manager/branches/<?= (int) $branch['id'] ?>/delete" method="POST"
                                  onsubmit="return confirm('¿Eliminar la rama «<?= htmlspecialchars(addslashes($branch['name']), ENT_QUOTES) ?>»?<?= $nCursos ? ' Sus ' . $nCursos . ' curso(s) quedarán sin rama.' : '' ?>')">
                                <?= \Core\Security::getCsrfField() ?>
                                <button type="submit" class="acc acc-rojo" title="Eliminar rama">
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
