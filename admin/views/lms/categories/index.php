<?php
/** Categorías del LMS: alta rápida a la izquierda, listado a la derecha. */
$categories = $categories ?? [];
$total      = count($categories);
$cursos     = array_sum(array_column($categories, 'course_count'));
$sinCursos  = count(array_filter($categories, static fn($c) => (int)$c['course_count'] === 0));
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Categorías LMS</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> categoría<?= $total === 1 ? '' : 's' ?> · organiza los cursos por área
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
        <a href="/manager/lms/courses" class="btn-sec">
            <i data-lucide="graduation-cap" class="ic ic-14"></i> Ver Cursos
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    <!-- Alta de categoría -->
    <div class="card card-pad">
        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[.09em] mb-4">Añadir nueva categoría</h3>
        <form action="/manager/lms/categories/store" method="POST" class="space-y-3.5">
            <?= \Core\Security::getCsrfField() ?>
            <div>
                <label class="filtro-rot" for="c-nombre">Nombre</label>
                <input type="text" id="c-nombre" name="name" required class="campo w-full"
                       placeholder="Ej: Seguridad Marítima">
            </div>
            <div>
                <label class="filtro-rot" for="c-desc">Descripción</label>
                <textarea id="c-desc" name="description" rows="3" class="campo w-full"
                          style="height:auto; padding:9px 11px; line-height:1.5"
                          placeholder="Breve descripción…"></textarea>
            </div>
            <button type="submit" class="btn-primario w-full justify-center">
                <i data-lucide="plus" class="ic ic-16"></i> Guardar Categoría
            </button>
        </form>
    </div>

    <!-- Listado -->
    <div class="card overflow-hidden lg:col-span-2">

        <div class="px-4 py-4 grid grid-cols-3 gap-3 border-b" style="border-color: var(--card-border)">
            <div class="kpi">
                <div class="kpi-label">Categorías</div>
                <div class="kpi-value"><?= number_format($total) ?></div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Cursos clasificados</div>
                <div class="kpi-value"><?= number_format($cursos) ?></div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Sin cursos</div>
                <div class="kpi-value"><?= number_format($sinCursos) ?></div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th class="centro">Cursos</th>
                        <th class="num">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="3" class="vacio">
                            <i data-lucide="tags" class="ic"></i>
                            <p>Todavía no hay categorías. Crea la primera con el formulario.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($categories as $cat):
                        $nCursos = (int)$cat['course_count'];
                        // El aviso dice qué pasa con los cursos que cuelgan de ella.
                        $aviso = $nCursos
                            ? sprintf('¿Eliminar la categoría «%s»? Sus %d curso%s quedarán sin categoría.',
                                      addslashes($cat['name']), $nCursos, $nCursos === 1 ? '' : 's')
                            : sprintf('¿Eliminar la categoría «%s»?', addslashes($cat['name']));
                    ?>
                    <tr>
                        <td>
                            <div class="font-bold text-[12.5px] text-slate-800"><?= htmlspecialchars($cat['name']) ?></div>
                            <div class="mt-1">
                                <span class="cod">
                                    <i data-lucide="link" class="ic" style="width:10px;height:10px"></i>
                                    <?= htmlspecialchars($cat['slug']) ?>
                                </span>
                            </div>
                        </td>
                        <td class="centro">
                            <a href="/manager/lms/courses?category_id=<?= $cat['id'] ?>"
                               class="pastilla <?= $nCursos ? 'p-azul' : 'p-gris' ?>"
                               title="Ver los cursos de esta categoría"><?= $nCursos ?></a>
                        </td>
                        <td class="num">
                            <div class="flex items-center justify-end gap-1.5">
                                <form method="POST" action="/manager/lms/categories/<?= $cat['id'] ?>/delete"
                                      onsubmit="return confirm('<?= htmlspecialchars($aviso, ENT_QUOTES) ?>')">
                                    <?= \Core\Security::getCsrfField() ?>
                                    <button type="submit" class="acc acc-rojo" title="Eliminar categoría">
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
</div>
