<?php
/** Lecciones de un curso concreto. */
$lessons = $lessons ?? [];
$total   = count($lessons);
$activas = count(array_filter($lessons, static fn($l) => !empty($l['is_active'])));
$minutos = array_sum(array_column($lessons, 'duration'));

// Icono y nombre de cada tipo de lección.
$tipos = [
    'video' => ['play-circle', 'Vídeo'],
    'file'  => ['paperclip',   'Archivo'],
    'text'  => ['file-text',   'Texto'],
];
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager/lms/courses/<?= $course['id'] ?>/edit" class="btn-atras" title="Volver al curso">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Lecciones del Curso</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5 truncate" style="max-width:520px">
            <?= htmlspecialchars($course['title']) ?>
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
<?php if (can('lms.lessons.manage')): ?>
        <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/create" class="btn-primario">
            <i data-lucide="plus" class="ic ic-16"></i> Añadir Lección
        </a>
<?php endif; ?>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Resumen -->
    <div class="px-4 py-4 grid grid-cols-3 gap-3 border-b" style="border-color: var(--card-border)">
        <div class="kpi">
            <div class="kpi-label">Lecciones</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Activas</div>
            <div class="kpi-value"><?= number_format($activas) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Duración total</div>
            <div class="kpi-value"><?= number_format($minutos) ?> <span class="text-[12px] font-bold text-slate-400">min</span></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th class="centro">#</th>
                    <th>Lección</th>
                    <th>Tipo</th>
                    <th class="num">Duración</th>
                    <th>Estado</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lessons)): ?>
                <tr>
                    <td colspan="6" class="vacio">
                        <i data-lucide="book-open" class="ic"></i>
                        <p>Este curso aún no tiene lecciones.</p>
<?php if (can('lms.lessons.manage')): ?>
                        <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/create"
                           class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Añadir la primera
                        </a>
<?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($lessons as $lesson):
                    $activa = !empty($lesson['is_active']);
                    [$icono, $tipoTexto] = $tipos[$lesson['type']] ?? ['file-text', ucfirst($lesson['type'])];
                ?>
                <tr>
                    <td class="centro">
                        <span class="cod"><?= (int)$lesson['order_num'] ?></span>
                    </td>
                    <td>
                        <div class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:420px"
                             title="<?= htmlspecialchars($lesson['title'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars($lesson['title']) ?>
                        </div>
                    </td>
                    <td>
                        <span class="pastilla p-gris">
                            <i data-lucide="<?= $icono ?>" class="ic" style="width:11px;height:11px"></i>
                            <?= htmlspecialchars($tipoTexto) ?>
                        </span>
                    </td>
                    <td class="num font-bold text-slate-700 whitespace-nowrap">
                        <?= (int)$lesson['duration'] ?> <span class="text-[10.5px] font-semibold text-slate-400">min</span>
                    </td>
                    <td>
                        <span class="pastilla <?= $activa ? 'p-verde' : 'p-gris' ?>">
                            <span class="punto"></span><?= $activa ? 'Activa' : 'Oculta' ?>
                        </span>
                    </td>
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
<?php if (can('lms.lessons.manage')): ?>
                            <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/<?= $lesson['id'] ?>/edit"
                               class="acc acc-gris" title="Editar lección">
                                <i data-lucide="pencil" class="ic" style="width:12px;height:12px"></i> Editar
                            </a>
                            <form method="POST" action="/manager/lms/courses/<?= $course['id'] ?>/lessons/<?= $lesson['id'] ?>/delete"
                                  onsubmit="return confirm('¿Eliminar la lección «<?= htmlspecialchars(addslashes($lesson['title']), ENT_QUOTES) ?>»? Esta acción no se puede deshacer.')">
                                <?= \Core\Security::getCsrfField() ?>
                                <button type="submit" class="acc acc-rojo" title="Eliminar lección">
                                    <i data-lucide="trash-2" class="ic" style="width:12px;height:12px"></i> Eliminar
                                </button>
                            </form>
<?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
