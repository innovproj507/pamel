<?php
/**
 * Listado de instructores del panel.
 *
 * Los recuadros de resumen reflejan los filtros activos, no el claustro
 * entero: la cifra grande de «Instructores» es la misma que la del paginador.
 */
$q            = $q ?? '';
$filterStatus = $filterStatus ?? '';
$stats        = $stats   ?? [];
$total        = (int)($total    ?? 0);
$page         = (int)($page     ?? 1);
$perPage      = (int)($perPage  ?? 20);
$lastPage     = (int)($lastPage ?? 1);

$hayFiltros = $q !== '' || $filterStatus !== '';

$recuadros = [
    ['Instructores', $total],
    ['Activos',      (int)($stats['activos']    ?? 0)],
    ['Bloqueados',   (int)($stats['bloqueados'] ?? 0)],
    ['Cursos asignados', (int)($stats['cursos'] ?? 0)],
];

// Tinte del redondel: estable por persona, para reconocerla entre páginas.
$tintes = ['', 'av-verde', 'av-ambar', 'av-rosa', 'av-violeta', 'av-cian'];
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Instructores</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> instructor<?= $total === 1 ? '' : 'es' ?> encontrado<?= $total === 1 ? '' : 's' ?>
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
        <a href="/manager/lms/students" class="btn-sec">
            <i data-lucide="users" class="ic ic-14"></i> Ver Estudiantes
        </a>
        <a href="/manager/users/create?role=teacher" class="btn-primario">
            <i data-lucide="plus" class="ic ic-16"></i> Nuevo Instructor
        </a>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Barra de filtros -->
    <form method="GET" action="/manager/lms/teachers"
          class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <div>
            <label class="filtro-rot" for="f-q">Buscar</label>
            <div class="relative">
                <i data-lucide="search" class="ic ic-14 absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" id="f-q" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES) ?>"
                       placeholder="Buscar por nombre o correo…"
                       class="campo" style="padding-left:32px; width:250px; border-radius:999px">
            </div>
        </div>

        <div>
            <label class="filtro-rot" for="f-estado">Estado</label>
            <select id="f-estado" name="status" class="campo" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="active"   <?= $filterStatus === 'active'   ? 'selected' : '' ?>>Activo</option>
                <option value="inactive" <?= $filterStatus === 'inactive' ? 'selected' : '' ?>>Bloqueado</option>
            </select>
        </div>

        <button type="submit" class="btn-sec">
            <i data-lucide="search" class="ic ic-14"></i> Buscar
        </button>

        <?php if ($hayFiltros): ?>
        <a href="/manager/lms/teachers" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Limpiar
        </a>
        <?php endif; ?>

        <input type="hidden" name="per_page" value="<?= $perPage ?>">
    </form>

    <!-- Resumen del conjunto filtrado -->
    <div class="px-4 py-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
        <?php foreach ($recuadros as [$etiqueta, $valor]): ?>
        <div class="kpi">
            <div class="kpi-label"><?= $etiqueta ?></div>
            <div class="kpi-value"><?= number_format($valor) ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Instructor</th>
                    <th>Estado</th>
                    <th class="centro">Cursos</th>
                    <th>Alta</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($teachers)): ?>
                <tr>
                    <td colspan="5" class="vacio">
                        <i data-lucide="search-x" class="ic"></i>
                        <p><?= $hayFiltros ? 'No se encontraron instructores con esos filtros.' : 'Todavía no hay instructores registrados.' ?></p>
                        <a href="<?= $hayFiltros ? '/manager/lms/teachers' : '/manager/users/create?role=teacher' ?>"
                           class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            <?= $hayFiltros ? 'Limpiar filtros' : 'Crear el primero' ?>
                        </a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($teachers as $t):
                    $nombre  = trim($t['name']);
                    $inicial = mb_strtoupper(mb_substr($nombre, 0, 1, 'UTF-8'), 'UTF-8');
                    $tinte   = $tintes[(int)$t['id'] % count($tintes)];
                    $cursos  = (int)($t['course_count'] ?? 0);
                    $activo  = $t['status'] === 'active';
                ?>
                <tr>

                    <!-- Instructor: inicial, nombre y correo -->
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar-ini <?= $tinte ?>"><?= htmlspecialchars($inicial) ?></div>
                            <div class="min-w-0">
                                <div class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:280px">
                                    <?= htmlspecialchars($nombre) ?>
                                </div>
                                <div class="text-[11px] font-semibold text-slate-400 truncate" style="max-width:280px">
                                    <?= htmlspecialchars($t['email']) ?>
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Estado -->
                    <td>
                        <span class="pastilla <?= $activo ? 'p-verde' : 'p-rojo' ?>">
                            <span class="punto"></span><?= $activo ? 'Activo' : 'Bloqueado' ?>
                        </span>
                    </td>

                    <!-- Cursos asignados -->
                    <td class="centro">
                        <a href="/manager/lms/courses?teacher_id=<?= $t['id'] ?>" class="pastilla p-azul"
                           title="Ver sus cursos"><?= $cursos ?></a>
                    </td>

                    <!-- Alta -->
                    <td class="text-slate-400 text-[11.5px] whitespace-nowrap">
                        <?= !empty($t['created_at']) ? date('d/m/Y', strtotime($t['created_at'])) : '—' ?>
                    </td>

                    <!-- Acciones -->
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/lms/courses?teacher_id=<?= $t['id'] ?>" class="acc acc-azul" title="Ver sus cursos">
                                <i data-lucide="graduation-cap" class="ic" style="width:12px;height:12px"></i> Ver Cursos
                            </a>
                            <a href="/manager/users/<?= $t['id'] ?>/edit" class="acc acc-gris" title="Editar usuario">
                                <i data-lucide="pencil" class="ic" style="width:12px;height:12px"></i> Editar
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    $ruta    = '/manager/lms/teachers';
    $filtros = ['q' => $q, 'status' => $filterStatus];
    include \Core\Config::get('paths.root') . '/admin/views/partials/paginador.php';
    ?>
</div>
