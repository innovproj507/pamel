<?php
$q                = htmlspecialchars($q ?? '');
$filterStatus     = $filterStatus     ?? '';
$filterCategory   = (int)($filterCategory   ?? 0);
$filterHasProduct = $filterHasProduct ?? '';
$total            = (int)($total   ?? 0);
$page             = (int)($page    ?? 1);
$lastPage         = (int)($lastPage ?? 1);

// Build base query string for pagination links (preserve all filters)
$pagerBase = http_build_query(array_filter([
    'q'           => $q,
    'status'      => $filterStatus,
    'category_id' => $filterCategory ?: null,
    'has_product' => $filterHasProduct,
]));
$pagerBase = $pagerBase ? "?{$pagerBase}&" : '?';
?>

<!-- Header -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black text-slate-800">Gestión de Cursos</h1>
        <p class="text-slate-500 font-medium">
            <?= $total ?> curso<?= $total !== 1 ? 's' : '' ?> encontrado<?= $total !== 1 ? 's' : '' ?>
        </p>
    </div>
    <div class="flex items-center gap-3">
        <form method="POST" action="/manager/products/sync-courses"
              onsubmit="return confirm('¿Crear cursos en borrador para todos los productos activos que aún no tienen curso?')">
            <input type="hidden" name="_csrf" value="<?= \Core\Security::generateCsrfToken() ?>">
            <button type="submit"
                    class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 text-slate-600 hover:text-blue-600 px-5 py-3 rounded-2xl font-bold transition text-sm">
                <i class="fas fa-sync-alt text-xs"></i> Sincronizar Productos
            </button>
        </form>
        <a href="/manager/lms/courses/create"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold transition shadow-lg shadow-blue-600/20">
            <i class="fas fa-plus"></i> Nuevo Curso
        </a>
    </div>
</div>

<!-- Filtros -->
<form method="GET" action="/manager/lms/courses"
      class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-5 mb-8">

    <!-- Fila 1: búsqueda -->
    <div class="flex gap-3 mb-4">
        <div class="flex-1 relative">
            <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 pointer-events-none"></i>
            <input type="text" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES) ?>"
                   placeholder="Buscar por título, slug o código…"
                   class="w-full pl-12 pr-5 py-3.5 rounded-xl bg-slate-50 text-sm font-semibold text-slate-700
                          focus:ring-4 focus:ring-blue-500/10 focus:bg-white border border-transparent
                          focus:border-blue-200 outline-none transition-all">
        </div>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white
                       font-bold text-sm px-7 py-3.5 rounded-xl transition-all shadow-md shadow-blue-600/20 whitespace-nowrap">
            <i class="fas fa-search text-xs"></i> Buscar
        </button>
        <?php if ($q || $filterStatus || $filterCategory || $filterHasProduct !== ''): ?>
        <a href="/manager/lms/courses"
           class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-600
                  font-bold text-sm px-5 py-3.5 rounded-xl transition-all whitespace-nowrap">
            <i class="fas fa-times text-xs"></i> Limpiar
        </a>
        <?php endif; ?>
    </div>

    <!-- Fila 2: filtros secundarios -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

        <!-- Estado -->
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Estado</label>
            <select name="status"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 text-sm font-bold text-slate-700
                           border border-slate-100 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-200 outline-none transition-all">
                <option value="">Todos los estados</option>
                <option value="published" <?= $filterStatus === 'published' ? 'selected' : '' ?>>Publicado</option>
                <option value="draft"     <?= $filterStatus === 'draft'     ? 'selected' : '' ?>>Borrador</option>
                <option value="archived"  <?= $filterStatus === 'archived'  ? 'selected' : '' ?>>Archivado</option>
            </select>
        </div>

        <!-- Categoría -->
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Categoría</label>
            <select name="category_id"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 text-sm font-bold text-slate-700
                           border border-slate-100 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-200 outline-none transition-all">
                <option value="">Todas las categorías</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $filterCategory === (int)$cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Producto vinculado -->
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 px-1">Producto vinculado</label>
            <select name="has_product"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 text-sm font-bold text-slate-700
                           border border-slate-100 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-200 outline-none transition-all">
                <option value="">Con y sin producto</option>
                <option value="1" <?= $filterHasProduct === '1' ? 'selected' : '' ?>>Con producto vinculado</option>
                <option value="0" <?= $filterHasProduct === '0' ? 'selected' : '' ?>>Sin producto vinculado</option>
            </select>
        </div>
    </div>
</form>

<!-- Tabla -->
<div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left table-fixed">
            <thead>
                <tr class="bg-white border-b border-slate-50">
                    <th class="w-1/3 px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.1em]">Curso</th>
                    <th class="w-36 px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] text-center">Estado</th>
                    <th class="w-52 px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] text-center">Contenido</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.1em] text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($courses)): ?>
                <tr>
                    <td colspan="4" class="px-8 py-16 text-center text-slate-400">
                        <i class="fas fa-search text-3xl mb-3 block opacity-30"></i>
                        <p class="font-semibold text-sm">No se encontraron cursos con esos filtros.</p>
                        <a href="/manager/lms/courses" class="text-blue-500 text-xs mt-1 inline-block hover:underline">Limpiar filtros</a>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($courses as $course): ?>
                <tr class="hover:bg-slate-50/30 transition-colors">

                    <!-- Curso -->
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-900 flex items-center justify-center
                                        text-white font-black text-xs shadow-sm flex-shrink-0">
                                <?= strtoupper(substr($course['title'], 0, 1)) ?>
                            </div>
                            <div class="min-w-0">
                                <a href="/manager/lms/courses/<?= $course['id'] ?>/show"
                                   class="font-bold text-slate-700 hover:text-blue-600 transition-colors block text-[13px] leading-tight truncate">
                                    <?= htmlspecialchars($course['title']) ?>
                                </a>
                                <?php if (!empty($course['course_code'])): ?>
                                <span class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 text-[9px] font-black uppercase tracking-widest">
                                    <i class="fas fa-tag" style="font-size:7px"></i>
                                    <?= htmlspecialchars($course['course_code']) ?>
                                </span>
                                <?php endif; ?>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5 truncate">
                                    <?= htmlspecialchars($course['category_name'] ?? '—') ?>
                                </p>
                                <?php if (!empty($course['product_name'])): ?>
                                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-[9px] font-bold
                                             <?= $course['product_status'] === 'active' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-500' ?>">
                                    <i class="fas fa-link" style="font-size:7px"></i>
                                    &bull; <?= $course['product_status'] === 'active' ? 'activo' : 'inactivo' ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    <!-- Estado -->
                    <td class="px-6 py-5 text-center">
                        <?php
                        $sc = ['published' => 'bg-emerald-50 text-emerald-600',
                               'draft'     => 'bg-amber-50 text-amber-600',
                               'archived'  => 'bg-slate-100 text-slate-500'];
                        $sl = ['published' => 'Publicado', 'draft' => 'Borrador', 'archived' => 'Archivado'];
                        $dot = ['published' => 'bg-emerald-500', 'draft' => 'bg-amber-500', 'archived' => 'bg-slate-400'];
                        ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest
                                     <?= $sc[$course['status']] ?? 'bg-slate-100 text-slate-500' ?>">
                            <span class="w-1 h-1 rounded-full <?= $dot[$course['status']] ?? 'bg-slate-400' ?>"></span>
                            <?= $sl[$course['status']] ?? $course['status'] ?>
                        </span>
                    </td>

                    <!-- Contenido -->
                    <td class="px-6 py-5">
                        <div class="flex items-center justify-center gap-4">
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-book-open text-[10px] text-blue-600"></i>
                                <span class="text-[10px] font-bold text-slate-700"><?= (int)($course['lesson_count'] ?? 0) ?> lec.</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-vial text-[10px] text-amber-500"></i>
                                <span class="text-[10px] font-bold text-slate-700"><?= (int)($course['quiz_count'] ?? 0) ?> quiz</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="fas fa-users text-[10px] text-emerald-600"></i>
                                <span class="text-[10px] font-bold text-slate-700"><?= (int)($course['student_count'] ?? 0) ?> est.</span>
                            </div>
                        </div>
                    </td>

                    <!-- Acciones -->
                    <td class="px-8 py-5 text-right">
                        <div class="flex items-center justify-end gap-2 flex-wrap">
                            <a href="/manager/lms/courses/<?= $course['id'] ?>/edit"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 text-slate-500 text-[9px] font-black uppercase tracking-widest hover:bg-slate-100 transition-all border border-slate-100">
                                <i class="fas fa-edit text-[10px]"></i> Editar
                            </a>
                            <a href="/manager/lms/courses/<?= $course['id'] ?>/students"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all text-[9px] font-black uppercase tracking-widest border border-emerald-100">
                                <i class="fas fa-user-graduate text-[10px]"></i> Estudiantes
                            </a>
                            <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/create"
                               class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all text-[9px] font-black uppercase tracking-widest border border-blue-100">
                                + Lección
                            </a>
                            <a href="/manager/lms/courses/<?= $course['id'] ?>/quizzes/create"
                               class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-all text-[9px] font-black uppercase tracking-widest border border-amber-100">
                                + Quiz
                            </a>
                            <form method="POST" action="/manager/lms/courses/<?= $course['id'] ?>/delete"
                                  onsubmit="return confirm('¿Eliminar el curso «<?= htmlspecialchars(addslashes($course['title'])) ?>»? Esta acción no se puede deshacer.')">
                                <?= \Core\Security::getCsrfField() ?>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-500 text-[9px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all border border-rose-100">
                                    <i class="fas fa-trash text-[10px]"></i> Eliminar
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

    <!-- Paginador -->
    <?php if ($lastPage > 1): ?>
    <div class="px-8 py-5 border-t border-slate-50 flex items-center justify-between gap-4">
        <p class="text-xs text-slate-400 font-medium">
            Página <strong class="text-slate-600"><?= $page ?></strong> de <strong class="text-slate-600"><?= $lastPage ?></strong>
            &mdash; <?= $total ?> cursos
        </p>
        <div class="flex items-center gap-1">
            <!-- Anterior -->
            <?php if ($page > 1): ?>
            <a href="/manager/lms/courses/<?= $pagerBase ?>page=<?= $page - 1 ?>"
               class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center
                      text-slate-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all text-xs font-bold">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </a>
            <?php endif; ?>

            <?php
            // Show up to 7 page buttons with ellipsis
            $start = max(1, $page - 3);
            $end   = min($lastPage, $page + 3);
            if ($start > 1): ?>
            <a href="/manager/lms/courses/<?= $pagerBase ?>page=1"
               class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 hover:bg-slate-100 transition-all">1</a>
            <?php if ($start > 2): ?><span class="text-slate-300 px-1">…</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($p = $start; $p <= $end; $p++): ?>
            <a href="/manager/lms/courses/<?= $pagerBase ?>page=<?= $p ?>"
               class="w-9 h-9 rounded-xl border flex items-center justify-center text-xs font-bold transition-all
                      <?= $p === $page
                          ? 'bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-600/25'
                          : 'bg-slate-50 border-slate-100 text-slate-500 hover:bg-slate-100' ?>">
                <?= $p ?>
            </a>
            <?php endfor; ?>

            <?php if ($end < $lastPage): ?>
            <?php if ($end < $lastPage - 1): ?><span class="text-slate-300 px-1">…</span><?php endif; ?>
            <a href="/manager/lms/courses/<?= $pagerBase ?>page=<?= $lastPage ?>"
               class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-xs font-bold text-slate-500 hover:bg-slate-100 transition-all"><?= $lastPage ?></a>
            <?php endif; ?>

            <!-- Siguiente -->
            <?php if ($page < $lastPage): ?>
            <a href="/manager/lms/courses/<?= $pagerBase ?>page=<?= $page + 1 ?>"
               class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center
                      text-slate-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all text-xs font-bold">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
