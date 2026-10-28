<?php
/**
 * Modalidades de la tienda.
 *
 * El icono viene de la base como clase de Font Awesome («fa-laptop»), así que
 * esas filas siguen usando FA en lugar de Lucide: cambiarlo pediría migrar
 * el dato, no sólo la plantilla.
 */
$modalities = $modalities ?? [];
$total      = count($modalities);
$cursos     = array_sum(array_column($modalities, 'product_count'));
$limitadas  = count(array_filter($modalities, static fn($m) => !empty($m['branch_name'])));
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Modalidades</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> modalidad<?= $total === 1 ? '' : 'es' ?> de impartición
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
        <a href="/manager/modalities/create" class="btn-primario">
            <i data-lucide="plus" class="ic ic-16"></i> Nueva Modalidad
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
            <div class="kpi-label">Modalidades</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Cursos</div>
            <div class="kpi-value"><?= number_format($cursos) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Limitadas a una rama</div>
            <div class="kpi-value"><?= number_format($limitadas) ?></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Modalidad</th>
                    <th>Disponible en</th>
                    <th class="centro">Cursos</th>
                    <th class="centro">Orden</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($modalities)): ?>
                <tr>
                    <td colspan="5" class="vacio">
                        <i data-lucide="layers" class="ic"></i>
                        <p>Todavía no hay modalidades.</p>
                        <a href="/manager/modalities/create" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Crear la primera
                        </a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($modalities as $modality):
                    $nCursos = (int) $modality['product_count'];
                ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar-ini av-violeta">
                                <i class="fas <?= htmlspecialchars($modality['icon']) ?> text-[15px]"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-[12.5px] text-slate-800"><?= htmlspecialchars($modality['name']) ?></div>
                                <div class="mt-1">
                                    <span class="cod">
                                        <i data-lucide="link" class="ic" style="width:10px;height:10px"></i>
                                        <?= htmlspecialchars($modality['slug']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($modality['branch_name'])): ?>
                        <span class="pastilla p-ambar">Sólo <?= htmlspecialchars($modality['branch_name']) ?></span>
                        <?php else: ?>
                        <span class="pastilla p-gris">Todas las ramas</span>
                        <?php endif; ?>
                    </td>
                    <td class="centro">
                        <span class="pastilla <?= $nCursos ? 'p-violeta' : 'p-gris' ?>"><?= $nCursos ?></span>
                    </td>
                    <td class="centro text-slate-400 font-bold"><?= (int) $modality['display_order'] ?></td>
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/modalities/<?= (int) $modality['id'] ?>/edit" class="acc acc-gris" title="Editar modalidad">
                                <i data-lucide="pencil" class="ic" style="width:12px;height:12px"></i> Editar
                            </a>
                            <form action="/manager/modalities/<?= (int) $modality['id'] ?>/delete" method="POST"
                                  onsubmit="return confirm('¿Eliminar la modalidad «<?= htmlspecialchars(addslashes($modality['name']), ENT_QUOTES) ?>»?<?= $nCursos ? ' Sus ' . $nCursos . ' curso(s) quedarán sin modalidad.' : '' ?>')">
                                <?= \Core\Security::getCsrfField() ?>
                                <button type="submit" class="acc acc-rojo" title="Eliminar modalidad">
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
