<?php
/** Foros del elearning: alta rápida a la izquierda, listado a la derecha. */
$forums   = $forums ?? [];
$total    = count($forums);
$activos  = count(array_filter($forums, static fn($f) => !empty($f['is_active'])));
$temas    = array_sum(array_column($forums, 'topics_count'));
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Foros</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> foro<?= $total === 1 ? '' : 's' ?> de discusión del elearning
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    <!-- Alta de foro -->
    <div class="card card-pad">
        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[.09em] mb-4">Nuevo foro</h3>
        <form action="/manager/lms/forums/store" method="POST" class="space-y-3.5">
            <?= \Core\Security::getCsrfField() ?>
            <div>
                <label class="filtro-rot" for="f-titulo">Título <span class="text-rose-500">*</span></label>
                <input type="text" id="f-titulo" name="title" required class="campo w-full"
                       placeholder="Ej: Seguridad Marítima">
            </div>
            <div>
                <label class="filtro-rot" for="f-desc">Descripción</label>
                <textarea id="f-desc" name="description" rows="3" class="campo w-full"
                          style="height:auto; padding:9px 11px; line-height:1.5"
                          placeholder="Breve descripción del foro…"></textarea>
            </div>
            <div>
                <label class="filtro-rot" for="f-orden">Orden</label>
                <input type="number" id="f-orden" name="order_num" value="0" min="0" class="campo w-full">
            </div>
            <button type="submit" class="btn-primario w-full justify-center">
                <i data-lucide="plus" class="ic ic-16"></i> Crear Foro
            </button>
        </form>
    </div>

    <!-- Listado -->
    <div class="card overflow-hidden lg:col-span-2">

        <div class="px-4 py-4 grid grid-cols-3 gap-3 border-b" style="border-color: var(--card-border)">
            <div class="kpi">
                <div class="kpi-label">Foros</div>
                <div class="kpi-value"><?= number_format($total) ?></div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Activos</div>
                <div class="kpi-value"><?= number_format($activos) ?></div>
            </div>
            <div class="kpi">
                <div class="kpi-label">Temas</div>
                <div class="kpi-value"><?= number_format($temas) ?></div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Foro</th>
                        <th class="centro">Temas</th>
                        <th>Estado</th>
                        <th class="num">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($forums)): ?>
                    <tr>
                        <td colspan="4" class="vacio">
                            <i data-lucide="message-square" class="ic"></i>
                            <p>No hay foros creados aún. Crea el primero con el formulario.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($forums as $forum):
                        $activo = !empty($forum['is_active']);
                        $nTemas = (int)$forum['topics_count'];
                        $aviso  = $nTemas
                            ? sprintf('¿Eliminar el foro «%s»? Se perderán también sus %d tema%s. Esta acción no se puede deshacer.',
                                      addslashes($forum['title']), $nTemas, $nTemas === 1 ? '' : 's')
                            : sprintf('¿Eliminar el foro «%s»? Esta acción no se puede deshacer.', addslashes($forum['title']));
                    ?>
                    <tr>
                        <td>
                            <div class="font-bold text-[12.5px] text-slate-800"><?= htmlspecialchars($forum['title']) ?></div>
                            <?php if (!empty($forum['description'])): ?>
                            <div class="text-[11px] font-semibold text-slate-400 truncate mt-0.5" style="max-width:380px">
                                <?= htmlspecialchars($forum['description']) ?>
                            </div>
                            <?php endif; ?>
                            <div class="mt-1">
                                <span class="cod">
                                    <i data-lucide="link" class="ic" style="width:10px;height:10px"></i>
                                    <?= htmlspecialchars($forum['slug']) ?>
                                </span>
                            </div>
                        </td>
                        <td class="centro">
                            <span class="pastilla <?= $nTemas ? 'p-azul' : 'p-gris' ?>"><?= $nTemas ?></span>
                        </td>
                        <td>
                            <span class="pastilla <?= $activo ? 'p-verde' : 'p-gris' ?>">
                                <span class="punto"></span><?= $activo ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="num">
                            <div class="flex items-center justify-end gap-1.5">
                                <form method="POST" action="/manager/lms/forums/<?= $forum['id'] ?>/toggle">
                                    <?= \Core\Security::getCsrfField() ?>
                                    <button type="submit" class="acc <?= $activo ? 'acc-ambar' : 'acc-verde' ?>"
                                            title="<?= $activo ? 'Ocultar el foro' : 'Publicar el foro' ?>">
                                        <i data-lucide="<?= $activo ? 'eye-off' : 'eye' ?>" class="ic" style="width:12px;height:12px"></i>
                                        <?= $activo ? 'Ocultar' : 'Activar' ?>
                                    </button>
                                </form>
                                <form method="POST" action="/manager/lms/forums/<?= $forum['id'] ?>/delete"
                                      onsubmit="return confirm('<?= htmlspecialchars($aviso, ENT_QUOTES) ?>')">
                                    <?= \Core\Security::getCsrfField() ?>
                                    <button type="submit" class="acc acc-rojo" title="Eliminar foro">
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
