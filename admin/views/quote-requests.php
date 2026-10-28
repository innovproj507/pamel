<?php
/** Bandeja de solicitudes de cotización. */
$requests      = $requests      ?? [];
$statusCounts  = $statusCounts  ?? ['pending' => 0, 'quoted' => 0, 'completed' => 0, 'cancelled' => 0];
$currentStatus = $currentStatus ?? '';
$currentSearch = $currentSearch ?? '';
$totalCount    = (int) ($totalCount  ?? 0);
$currentPage   = (int) ($currentPage ?? 1);
$totalPages    = (int) ($totalPages  ?? 1);

$hayFiltros = $currentSearch !== '' || $currentStatus !== '';

// Nombre en español y tinte de cada estado.
$estados = [
    'pending'   => ['Pendiente',  'p-ambar'],
    'quoted'    => ['Cotizada',   'p-azul'],
    'completed' => ['Completada', 'p-verde'],
    'cancelled' => ['Cancelada',  'p-rojo'],
];

// Recuadros: el primero quita el filtro, los demás filtran por su estado.
$recuadros = [
    ['Todas',      array_sum($statusCounts),     ''],
    ['Pendientes', $statusCounts['pending'],     'pending'],
    ['Cotizadas',  $statusCounts['quoted'],      'quoted'],
    ['Completadas',$statusCounts['completed'],   'completed'],
    ['Canceladas', $statusCounts['cancelled'],   'cancelled'],
];

/** URL de paginación conservando filtros. */
$enlace = static function (int $p) use ($currentStatus, $currentSearch): string {
    return '/manager/quote-requests?' . http_build_query(array_filter([
        'page'   => $p,
        'status' => $currentStatus,
        'search' => $currentSearch,
    ], static fn($v) => $v !== '' && $v !== null));
};
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Cotizaciones</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($totalCount) ?> solicitud<?= $totalCount === 1 ? '' : 'es' ?> encontrada<?= $totalCount === 1 ? '' : 's' ?>
        </p>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Barra de filtros -->
    <form method="GET" action="/manager/quote-requests"
          class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <?php if ($currentStatus): ?>
        <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus, ENT_QUOTES) ?>">
        <?php endif; ?>

        <div>
            <label class="filtro-rot" for="f-q">Buscar</label>
            <div class="relative">
                <i data-lucide="search" class="ic ic-14 absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" id="f-q" name="search" value="<?= htmlspecialchars($currentSearch, ENT_QUOTES) ?>"
                       placeholder="Buscar por nombre o correo…"
                       class="campo" style="padding-left:32px; width:280px; border-radius:999px">
            </div>
        </div>

        <button type="submit" class="btn-sec">
            <i data-lucide="search" class="ic ic-14"></i> Buscar
        </button>

        <?php if ($hayFiltros): ?>
        <a href="/manager/quote-requests" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Limpiar
        </a>
        <?php endif; ?>
    </form>

    <!-- Recuadros: además de contar, filtran por estado -->
    <div class="px-4 py-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        <?php foreach ($recuadros as [$etiqueta, $valor, $clave]):
            $activo = $currentStatus === $clave;
            $url = '/manager/quote-requests' . ($clave ? '?status=' . $clave : '');
        ?>
        <a href="<?= $url ?>" class="kpi block transition"
           <?= $activo ? 'style="border-color:#2563eb; background:#f5f8ff"' : '' ?>
           title="<?= $activo ? 'Quitar este filtro' : 'Ver sólo ' . strtolower($etiqueta) ?>">
            <div class="kpi-label"><?= $etiqueta ?></div>
            <div class="kpi-value"><?= number_format($valor) ?></div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Cliente</th>
                    <th>Cursos solicitados</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                <tr>
                    <td colspan="6" class="vacio">
                        <i data-lucide="inbox" class="ic"></i>
                        <p>No hay solicitudes de cotización.</p>
                        <?php if ($hayFiltros): ?>
                        <a href="/manager/quote-requests" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Limpiar filtros
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($requests as $request):
                    [$estadoTexto, $estadoClase] = $estados[$request['status']] ?? [ucfirst($request['status']), 'p-gris'];
                    $cursos  = array_map(static fn($i) => $i['product_name'], $request['items']);
                    $nCursos = count($cursos);
                    $listado = implode(', ', $cursos);
                ?>
                <tr>
                    <td><span class="cod">#<?= (int) $request['id'] ?></span></td>
                    <td>
                        <div class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:200px">
                            <?= htmlspecialchars($request['customer_name']) ?>
                        </div>
                        <a href="mailto:<?= htmlspecialchars($request['email'], ENT_QUOTES) ?>"
                           class="block text-[11px] font-semibold text-slate-400 truncate hover:text-blue-600"
                           style="max-width:200px"><?= htmlspecialchars($request['email']) ?></a>
                    </td>
                    <td>
                        <div class="flex items-center gap-2">
                            <span class="pastilla p-azul"><?= $nCursos ?></span>
                            <span class="truncate text-slate-400" style="max-width:280px"
                                  title="<?= htmlspecialchars($listado, ENT_QUOTES) ?>"><?= htmlspecialchars($listado) ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="pastilla <?= $estadoClase ?>"><span class="punto"></span><?= $estadoTexto ?></span>
                    </td>
                    <td class="text-slate-400 text-[11.5px] whitespace-nowrap">
                        <?= date('d/m/Y H:i', strtotime($request['created_at'])) ?>
                    </td>
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/quote-requests/<?= (int) $request['id'] ?>" class="acc acc-azul" title="Ver la solicitud">
                                <i data-lucide="eye" class="ic" style="width:12px;height:12px"></i> Ver
                            </a>
                            <?php if ($request['status'] === 'pending'): ?>
                            <button onclick="sendQuote(<?= (int) $request['id'] ?>, this)" class="acc acc-verde"
                                    title="Enviar la cotización al cliente">
                                <i data-lucide="send" class="ic" style="width:12px;height:12px"></i> Cotizar
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pie -->
    <div class="px-4 py-3.5 border-t flex flex-wrap items-center justify-between gap-3"
         style="border-color: var(--card-border)">

        <p class="text-[12px] font-semibold text-slate-400">
            <?php if ($totalPages > 1): ?>
            Página <?= $currentPage ?> de <?= $totalPages ?> · <?= number_format($totalCount) ?> solicitudes
            <?php else: ?>
            <?= number_format($totalCount) ?> solicitud<?= $totalCount === 1 ? '' : 'es' ?>
            <?php endif; ?>
        </p>

        <?php if ($totalPages > 1): ?>
        <div class="flex items-center gap-1.5">
            <?php if ($currentPage > 1): ?>
            <a href="<?= $enlace($currentPage - 1) ?>" class="pag" title="Anterior">
                <i data-lucide="chevron-left" class="ic ic-14"></i>
            </a>
            <?php else: ?>
            <span class="pag inerte"><i data-lucide="chevron-left" class="ic ic-14"></i></span>
            <?php endif; ?>

            <?php
            $inicio = max(1, $currentPage - 2);
            $fin    = min($totalPages, $currentPage + 2);
            for ($i = $inicio; $i <= $fin; $i++): ?>
            <a href="<?= $enlace($i) ?>" class="pag <?= $i === $currentPage ? 'activa' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $enlace($currentPage + 1) ?>" class="pag" title="Siguiente">
                <i data-lucide="chevron-right" class="ic ic-14"></i>
            </a>
            <?php else: ?>
            <span class="pag inerte"><i data-lucide="chevron-right" class="ic ic-14"></i></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function sendQuote(id, btn) {
    if (!confirm('¿Enviar esta cotización al cliente con los precios actuales de los cursos?')) return;

    btn.disabled = true;
    const formData = new FormData();
    formData.append('csrf_token', '<?= \Core\Security::generateCsrfToken() ?>');

    fetch(`/manager/quote-requests/${id}/send-quote`, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert('Error: ' + data.message); btn.disabled = false; }
        })
        .catch(() => { alert('Error de conexión. Inténtalo de nuevo.'); btn.disabled = false; });
}
</script>
