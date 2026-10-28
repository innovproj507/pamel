<?php
/**
 * Pie de los listados: rango mostrado, selector de filas y paginador.
 *
 * Se incluye desde la vista, que debe dejar preparadas estas variables:
 *   $ruta      string  ruta del listado, sin query (ej. '/manager/lms/courses')
 *   $total     int     filas que cumplen los filtros
 *   $page      int     página actual
 *   $perPage   int     filas por página
 *   $lastPage  int     última página
 *   $filtros   array   filtros activos (nombre => valor) que deben viajar
 *                      con el paginador y con el selector de filas
 */
$total    = (int)($total    ?? 0);
$page     = (int)($page     ?? 1);
$perPage  = (int)($perPage  ?? 20);
$lastPage = (int)($lastPage ?? 1);
$filtros  = $filtros ?? [];
$ruta     = $ruta ?? '';

$desde = $total ? (($page - 1) * $perPage) + 1 : 0;
$hasta = min($page * $perPage, $total);

// Base de los enlaces: conserva filtros y filas por página.
$consulta = http_build_query(array_filter(
    $filtros + ['per_page' => $perPage !== 20 ? $perPage : null],
    static fn($v) => $v !== '' && $v !== null && $v !== 0
));
$pagerBase = $consulta ? "?{$consulta}&" : '?';
?>
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
        <form method="GET" action="<?= htmlspecialchars($ruta, ENT_QUOTES) ?>" class="flex items-center gap-2">
            <?php foreach ($filtros as $nombre => $valor): ?>
            <input type="hidden" name="<?= htmlspecialchars((string)$nombre, ENT_QUOTES) ?>"
                   value="<?= htmlspecialchars((string)$valor, ENT_QUOTES) ?>">
            <?php endforeach; ?>
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
        <a href="<?= $ruta . $pagerBase ?>page=<?= $page - 1 ?>" class="pag" title="Anterior">
            <i data-lucide="chevron-left" class="ic ic-14"></i>
        </a>
        <?php else: ?>
        <span class="pag inerte"><i data-lucide="chevron-left" class="ic ic-14"></i></span>
        <?php endif; ?>

        <?php
        $inicio = max(1, $page - 2);
        $fin    = min($lastPage, $page + 2);
        if ($inicio > 1): ?>
        <a href="<?= $ruta . $pagerBase ?>page=1" class="pag">1</a>
        <?php if ($inicio > 2): ?><span class="pag inerte">…</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($p = $inicio; $p <= $fin; $p++): ?>
        <a href="<?= $ruta . $pagerBase ?>page=<?= $p ?>"
           class="pag <?= $p === $page ? 'activa' : '' ?>"><?= $p ?></a>
        <?php endfor; ?>

        <?php if ($fin < $lastPage): ?>
        <?php if ($fin < $lastPage - 1): ?><span class="pag inerte">…</span><?php endif; ?>
        <a href="<?= $ruta . $pagerBase ?>page=<?= $lastPage ?>" class="pag"><?= $lastPage ?></a>
        <?php endif; ?>

        <?php if ($page < $lastPage): ?>
        <a href="<?= $ruta . $pagerBase ?>page=<?= $page + 1 ?>" class="pag" title="Siguiente">
            <i data-lucide="chevron-right" class="ic ic-14"></i>
        </a>
        <?php else: ?>
        <span class="pag inerte"><i data-lucide="chevron-right" class="ic ic-14"></i></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
