<?php
/**
 * Listado de cursos del panel.
 *
 * Los recuadros de resumen reflejan los filtros activos, no el catálogo
 * entero: la cifra grande de «Cursos» es la misma que la del paginador.
 */
$q                = $q ?? '';
$filterStatus     = $filterStatus     ?? '';
$filterCategory   = (int)($filterCategory   ?? 0);
$filterHasProduct = $filterHasProduct ?? '';
$filterTeacherId  = (int)($filterTeacherId ?? 0);
$teachers         = $teachers ?? [];
$isTeacher        = $isTeacher ?? false;
$stats            = $stats    ?? [];
$total            = (int)($total    ?? 0);
$page             = (int)($page     ?? 1);
$perPage          = (int)($perPage  ?? 20);
$lastPage         = (int)($lastPage ?? 1);

$hayFiltros = $q !== '' || $filterStatus !== '' || $filterCategory || $filterHasProduct !== '' || $filterTeacherId;

// Rango que se está mostrando, para el pie de la tabla.
$desde = $total ? (($page - 1) * $perPage) + 1 : 0;
$hasta = min($page * $perPage, $total);

// Base de los enlaces del paginador: conserva filtros y filas por página.
$pagerBase = http_build_query(array_filter([
    'q'           => $q,
    'status'      => $filterStatus,
    'category_id' => $filterCategory ?: null,
    'has_product' => $filterHasProduct,
    'teacher_id'  => $filterTeacherId ?: null,
    'per_page'    => $perPage !== 20 ? $perPage : null,
]));
$pagerBase = $pagerBase ? "?{$pagerBase}&" : '?';

$recuadros = [
    ['Cursos',      $total],
    ['Publicados',  (int)($stats['publicados']  ?? 0)],
    ['Borradores',  (int)($stats['borradores']  ?? 0)],
    ['Lecciones',   (int)($stats['lecciones']   ?? 0)],
    ['Estudiantes', (int)($stats['estudiantes'] ?? 0)],
];
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Gestión de Cursos</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> curso<?= $total === 1 ? '' : 's' ?> encontrado<?= $total === 1 ? '' : 's' ?>
        </p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap lg:ml-auto">
<?php if (can('lms.courses.manage')): ?>
        <form method="POST" action="/manager/products/sync-courses"
              onsubmit="return confirm('¿Crear cursos en borrador para todos los productos activos que aún no tienen curso?')">
            <input type="hidden" name="_csrf" value="<?= \Core\Security::generateCsrfToken() ?>">
            <button type="submit" class="btn-violeta">
                <i data-lucide="refresh-cw" class="ic ic-14"></i> Sincronizar Productos
            </button>
        </form>
        <a href="/manager/lms/courses/create" class="btn-primario">
            <i data-lucide="plus" class="ic ic-16"></i> Nuevo Curso
        </a>
<?php endif; ?>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Barra de filtros -->
    <form method="GET" action="/manager/lms/courses"
          class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <div>
            <label class="filtro-rot" for="f-q">Buscar</label>
            <div class="relative">
                <i data-lucide="search" class="ic ic-14 absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" id="f-q" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES) ?>"
                       placeholder="Buscar por título, slug o código…"
                       class="campo" style="padding-left:32px; width:250px; border-radius:999px">
            </div>
        </div>

        <div>
            <label class="filtro-rot" for="f-estado">Estado</label>
            <select id="f-estado" name="status" class="campo" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <option value="published" <?= $filterStatus === 'published' ? 'selected' : '' ?>>Publicado</option>
                <option value="draft"     <?= $filterStatus === 'draft'     ? 'selected' : '' ?>>Borrador</option>
                <option value="archived"  <?= $filterStatus === 'archived'  ? 'selected' : '' ?>>Archivado</option>
            </select>
        </div>

        <div>
            <label class="filtro-rot" for="f-categoria">Categoría</label>
            <select id="f-categoria" name="category_id" class="campo" onchange="this.form.submit()">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $filterCategory === (int)$cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="filtro-rot" for="f-producto">Producto vinculado</label>
            <select id="f-producto" name="has_product" class="campo" onchange="this.form.submit()">
                <option value="">Con y sin producto</option>
                <option value="1" <?= $filterHasProduct === '1' ? 'selected' : '' ?>>Con producto</option>
                <option value="0" <?= $filterHasProduct === '0' ? 'selected' : '' ?>>Sin producto</option>
            </select>
        </div>

        <?php if (!$isTeacher): ?>
        <div>
            <label class="filtro-rot" for="f-instructor">Instructor</label>
            <select id="f-instructor" name="teacher_id" class="campo" onchange="this.form.submit()">
                <option value="">Todos los instructores</option>
                <?php foreach ($teachers as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $filterTeacherId === (int)$t['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <button type="submit" class="btn-sec">
            <i data-lucide="search" class="ic ic-14"></i> Buscar
        </button>

        <?php if ($hayFiltros): ?>
        <a href="/manager/lms/courses" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Limpiar
        </a>
        <?php endif; ?>

        <!-- Las filas por página se eligen en el pie, pero viajan con los
             filtros para que al buscar no se pierda el tamaño elegido. -->
        <input type="hidden" name="per_page" value="<?= $perPage ?>">
    </form>

    <!-- Resumen del conjunto filtrado -->
    <div class="px-4 py-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
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
                    <th>Curso</th>
                    <th>Instructor</th>
                    <th>Estado</th>
                    <th>Contenido</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($courses)): ?>
                <tr>
                    <td colspan="5" class="text-center" style="padding:56px 12px">
                        <i data-lucide="search-x" class="ic text-slate-300 mx-auto mb-3" style="width:30px;height:30px"></i>
                        <p class="text-[13px] font-semibold text-slate-400">No se encontraron cursos con esos filtros.</p>
                        <a href="/manager/lms/courses" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Limpiar filtros
                        </a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($courses as $course):
                    $estados = [
                        'published' => ['Publicado', 'p-verde'],
                        'draft'     => ['Borrador',  'p-ambar'],
                        'archived'  => ['Archivado', 'p-gris'],
                    ];
                    [$estadoTexto, $estadoClase] = $estados[$course['status']] ?? [$course['status'], 'p-gris'];
                    $lecciones   = (int)($course['lesson_count']  ?? 0);
                    $estudiantes = (int)($course['student_count'] ?? 0);
                    // Inicial para el redondel; mb_* porque hay títulos con acento.
                    $inicial = mb_strtoupper(mb_substr(trim($course['title']), 0, 1, 'UTF-8'), 'UTF-8');
                ?>
                <tr>

                    <!-- Curso: inicial, título, categoría, código, producto y fecha -->
                    <td>
                        <div class="flex items-start gap-3">
                            <div class="avatar-ini"><?= htmlspecialchars($inicial) ?></div>
                            <div class="min-w-0">
                                <a href="/manager/lms/courses/<?= $course['id'] ?>/show"
                                   class="block font-bold text-[12.5px] text-slate-800 hover:text-blue-700 hover:underline truncate"
                                   style="max-width:360px" title="<?= htmlspecialchars($course['title'], ENT_QUOTES) ?>">
                                    <?= htmlspecialchars($course['title']) ?>
                                </a>

                                <?php if (!empty($course['category_name'])): ?>
                                <div class="sub-cat truncate mt-0.5" style="max-width:360px">
                                    <?= htmlspecialchars($course['category_name']) ?>
                                </div>
                                <?php endif; ?>

                                <div class="flex items-center gap-1.5 flex-wrap mt-1.5">
                                    <?php if (!empty($course['course_code'])): ?>
                                    <span class="cod">
                                        <i data-lucide="tag" class="ic" style="width:10px;height:10px"></i>
                                        <?= htmlspecialchars($course['course_code']) ?>
                                    </span>
                                    <?php endif; ?>

                                    <?php if (!empty($course['product_name'])): ?>
                                    <span class="pastilla <?= $course['product_status'] === 'active' ? 'p-azul' : 'p-rojo' ?>"
                                          title="<?= htmlspecialchars($course['product_name'], ENT_QUOTES) ?>">
                                        <i data-lucide="link" class="ic" style="width:10px;height:10px"></i>
                                        <?= $course['product_status'] === 'active' ? 'Vinculado' : 'Inactivo' ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-slate-300 text-[10.5px] font-semibold">Sin producto</span>
                                    <?php endif; ?>

                                    <?php if (!empty($course['created_at'])): ?>
                                    <span class="text-slate-300 text-[10.5px] font-semibold whitespace-nowrap">
                                        · <?= date('d/m/Y', strtotime($course['created_at'])) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Instructor -->
                    <td>
                        <?php if (!empty($course['teacher_name'])): ?>
                        <span class="block truncate font-semibold" style="max-width:130px">
                            <?= htmlspecialchars($course['teacher_name']) ?>
                        </span>
                        <?php else: ?>
                        <span class="sub-cat">Sin asignar</span>
                        <?php endif; ?>
                    </td>

                    <!-- Estado -->
                    <td><span class="pastilla <?= $estadoClase ?>"><?= $estadoTexto ?></span></td>

                    <!-- Contenido: lecciones, quizzes y estudiantes juntos -->
                    <td>
                        <div class="flex items-center gap-3.5">
                            <span class="meta-dato" title="<?= $lecciones ?> lección<?= $lecciones !== 1 ? 'es' : '' ?>">
                                <i data-lucide="book-open" class="ic ic-14 text-blue-400"></i>
                                <span>
                                    <span class="cifra block"><?= $lecciones ?></span>
                                    <span class="unidad">lec.</span>
                                </span>
                            </span>
                            <span class="meta-dato">
                                <i data-lucide="pencil-line" class="ic ic-14 text-amber-400"></i>
                                <span>
                                    <span class="cifra block"><?= (int)($course['quiz_count'] ?? 0) ?></span>
                                    <span class="unidad">quiz</span>
                                </span>
                            </span>
                            <span class="meta-dato" title="<?= $estudiantes ?> inscrito<?= $estudiantes !== 1 ? 's' : '' ?>">
                                <i data-lucide="users" class="ic ic-14 text-emerald-400"></i>
                                <span>
                                    <span class="cifra block"><?= $estudiantes ?></span>
                                    <span class="unidad">est.</span>
                                </span>
                            </span>
                        </div>
                    </td>

                    <!-- Acciones -->
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
<?php if (can('lms.courses.manage')): ?>
                            <a href="/manager/lms/courses/<?= $course['id'] ?>/edit" class="acc acc-gris" title="Editar curso">
                                <i data-lucide="pencil" class="ic" style="width:12px;height:12px"></i> Editar
                            </a>
<?php endif; ?>
                            <a href="/manager/lms/courses/<?= $course['id'] ?>/students" class="acc acc-verde"
                               title="<?= $estudiantes ?> estudiante<?= $estudiantes !== 1 ? 's' : '' ?> inscrito<?= $estudiantes !== 1 ? 's' : '' ?>">
                                <i data-lucide="users" class="ic" style="width:12px;height:12px"></i> Estudiantes
                            </a>
<?php if (can('lms.lessons.manage')): ?>
                            <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/create" class="acc acc-azul" title="Añadir lección">
                                <i data-lucide="plus" class="ic" style="width:12px;height:12px"></i> Lección
                            </a>
<?php endif; ?>
<?php if (can('lms.quizzes.manage')): ?>
                            <a href="/manager/lms/quizzes/create?course_id=<?= $course['id'] ?>" class="acc acc-ambar" title="Añadir quiz">
                                <i data-lucide="plus" class="ic" style="width:12px;height:12px"></i> Quiz
                            </a>
<?php endif; ?>
<?php if (can('lms.courses.manage')): ?>
                            <?php
                            // El aviso dice qué se lleva por delante el borrado: el
                            // curso arrastra sus lecciones y sus inscripciones.
                            $aviso = sprintf(
                                '¿Eliminar el curso «%s»? Se perderán también sus %d lección%s y sus %d inscripción%s. Esta acción no se puede deshacer.',
                                addslashes($course['title']),
                                $lecciones,   $lecciones   === 1 ? '' : 'es',
                                $estudiantes, $estudiantes === 1 ? '' : 'es'
                            );
                            ?>
                            <form method="POST" action="/manager/lms/courses/<?= $course['id'] ?>/delete"
                                  onsubmit="return confirm('<?= htmlspecialchars($aviso, ENT_QUOTES) ?>')">
                                <?= \Core\Security::getCsrfField() ?>
                                <button type="submit" class="acc acc-rojo" title="Eliminar curso">
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

    <!-- Pie: rango y paginador -->
    <div class="px-4 py-3.5 border-t flex flex-wrap items-center justify-between gap-3"
         style="border-color: var(--card-border)">

        <div class="flex items-center gap-3 flex-wrap">
            <p class="text-[12px] font-semibold text-slate-400">
                <?php if ($total): ?>
                Mostrando <?= number_format($desde) ?>-<?= number_format($hasta) ?> de <?= number_format($total) ?>
                <?php else: ?>
                Sin resultados
                <?php endif; ?>
            </p>

            <!-- Formulario aparte: arrastra los filtros activos para no perderlos. -->
            <form method="GET" action="/manager/lms/courses" class="flex items-center gap-2">
                <input type="hidden" name="q"           value="<?= htmlspecialchars($q, ENT_QUOTES) ?>">
                <input type="hidden" name="status"      value="<?= htmlspecialchars($filterStatus, ENT_QUOTES) ?>">
                <input type="hidden" name="category_id" value="<?= $filterCategory ?: '' ?>">
                <input type="hidden" name="has_product" value="<?= htmlspecialchars($filterHasProduct, ENT_QUOTES) ?>">
                <input type="hidden" name="teacher_id"  value="<?= $filterTeacherId ?: '' ?>">
                <span class="text-[11.5px] font-semibold text-slate-400">Mostrar</span>
                <select name="per_page" class="campo" onchange="this.form.submit()" aria-label="Filas por página">
                    <?php foreach ([10, 20, 50, 100] as $n): ?>
                    <option value="<?= $n ?>" <?= $perPage === $n ? 'selected' : '' ?>><?= $n ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="text-[11.5px] font-semibold text-slate-400">filas</span>
            </form>
        </div>

        <?php if ($lastPage > 1): ?>
        <div class="flex items-center gap-1.5">

            <?php if ($page > 1): ?>
            <a href="/manager/lms/courses<?= $pagerBase ?>page=<?= $page - 1 ?>" class="pag" title="Anterior">
                <i data-lucide="chevron-left" class="ic ic-14"></i>
            </a>
            <?php else: ?>
            <span class="pag inerte"><i data-lucide="chevron-left" class="ic ic-14"></i></span>
            <?php endif; ?>

            <?php
            $inicio = max(1, $page - 2);
            $fin    = min($lastPage, $page + 2);
            if ($inicio > 1): ?>
            <a href="/manager/lms/courses<?= $pagerBase ?>page=1" class="pag">1</a>
            <?php if ($inicio > 2): ?><span class="pag inerte">…</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($p = $inicio; $p <= $fin; $p++): ?>
            <a href="/manager/lms/courses<?= $pagerBase ?>page=<?= $p ?>"
               class="pag <?= $p === $page ? 'activa' : '' ?>"><?= $p ?></a>
            <?php endfor; ?>

            <?php if ($fin < $lastPage): ?>
            <?php if ($fin < $lastPage - 1): ?><span class="pag inerte">…</span><?php endif; ?>
            <a href="/manager/lms/courses<?= $pagerBase ?>page=<?= $lastPage ?>" class="pag"><?= $lastPage ?></a>
            <?php endif; ?>

            <?php if ($page < $lastPage): ?>
            <a href="/manager/lms/courses<?= $pagerBase ?>page=<?= $page + 1 ?>" class="pag" title="Siguiente">
                <i data-lucide="chevron-right" class="ic ic-14"></i>
            </a>
            <?php else: ?>
            <span class="pag inerte"><i data-lucide="chevron-right" class="ic ic-14"></i></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
