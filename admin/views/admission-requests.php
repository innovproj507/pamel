<?php
/**
 * Listado de solicitudes de admisión.
 *
 * Valores por defecto arriba: la vista se dibuja aunque el controlador no
 * mande las filas por pantalla, y así $perPage ya existe cuando lo usan
 * los enlaces de los recuadros de estado.
 */
$requests       = $requests       ?? [];
$statusCounts   = $statusCounts   ?? ['pending' => 0, 'reviewed' => 0, 'approved' => 0, 'rejected' => 0];
$currentStatus  = $currentStatus  ?? '';
$currentSearch  = $currentSearch  ?? '';
$totalCount     = (int) ($totalCount  ?? 0);
$currentPage    = (int) ($currentPage ?? 1);
$totalPages     = (int) ($totalPages  ?? 1);
$perPage        = (int) ($perPage ?? 15);
$perPageOptions = $perPageOptions ?? [5, 10, 15, 25, 50];

$hayFiltros = $currentSearch !== '' || $currentStatus !== '';

// Estado -> etiqueta en español, pastilla e icono.
$estados = [
    'pending'  => ['Pendiente', 'p-ambar', 'clock'],
    'reviewed' => ['Revisada',  'p-azul',  'eye'],
    'approved' => ['Aprobada',  'p-verde', 'circle-check'],
    'rejected' => ['Rechazada', 'p-rojo',  'circle-x'],
];

// Recuadros: además de contar, filtran por estado.
$recuadros = [
    ['Todas',      array_sum($statusCounts),   ''],
    ['Pendientes', $statusCounts['pending'],   'pending'],
    ['Revisadas',  $statusCounts['reviewed'],  'reviewed'],
    ['Aprobadas',  $statusCounts['approved'],  'approved'],
    ['Rechazadas', $statusCounts['rejected'],  'rejected'],
];

/** URL de una página conservando filtros y filas por pantalla. */
$enlace = static function (int $p) use ($currentStatus, $currentSearch, $perPage): string {
    return '/manager/admission-requests?' . http_build_query(array_filter([
        'page'     => $p,
        'status'   => $currentStatus,
        'search'   => $currentSearch,
        'per_page' => $perPage,
    ], static fn($v) => $v !== '' && $v !== null));
};

$desde = max(1, min($currentPage - 2, $totalPages - 4));
$hasta = min($totalPages, max($currentPage + 2, 5));

$primero = $totalCount === 0 ? 0 : (($currentPage - 1) * $perPage) + 1;
$ultimo  = min($currentPage * $perPage, $totalCount);
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Solicitudes de Admisión</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($totalCount) ?> solicitud<?= $totalCount === 1 ? '' : 'es' ?> encontrada<?= $totalCount === 1 ? '' : 's' ?>
        </p>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Barra de filtros -->
    <form method="GET" action="/manager/admission-requests"
          class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <div>
            <label class="filtro-rot" for="f-q">Buscar</label>
            <div class="relative">
                <i data-lucide="search" class="ic ic-14 absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="search" id="f-q" name="search" value="<?= htmlspecialchars($currentSearch, ENT_QUOTES) ?>"
                       placeholder="Buscar por nombre o correo…"
                       class="campo" style="padding-left:32px; width:260px; border-radius:999px">
            </div>
        </div>

        <div>
            <label class="filtro-rot" for="f-estado">Estado</label>
            <select id="f-estado" name="status" class="campo" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                <?php foreach ($estados as $clave => [$texto, $_c, $_i]): ?>
                <option value="<?= $clave ?>" <?= $currentStatus === $clave ? 'selected' : '' ?>><?= $texto ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-sec">
            <i data-lucide="search" class="ic ic-14"></i> Buscar
        </button>

        <?php if ($hayFiltros): ?>
        <a href="/manager/admission-requests" class="btn-sec">
            <i data-lucide="x" class="ic ic-14"></i> Limpiar
        </a>
        <?php endif; ?>

        <input type="hidden" name="per_page" value="<?= $perPage ?>">
    </form>

    <!-- Recuadros: además de contar, filtran por estado -->
    <div class="px-4 py-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
        <?php foreach ($recuadros as [$etiqueta, $valor, $clave]):
            $activo = $currentStatus === $clave;
            $url = '/manager/admission-requests?' . http_build_query(array_filter([
                'status'   => $clave,
                'per_page' => $perPage,
            ], static fn($v) => $v !== '' && $v !== null));
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
                    <th>Solicitante</th>
                    <th>Curso</th>
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
                        <p>No hay solicitudes de admisión.</p>
                        <?php if ($hayFiltros): ?>
                        <a href="/manager/admission-requests" class="text-blue-600 text-xs font-semibold mt-1 inline-block hover:underline">
                            Limpiar filtros
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($requests as $request):
                    $st = $request['status'] ?? 'pending';
                    [$etiqueta, $clasePastilla, $icono] = $estados[$st] ?? $estados['pending'];
                    $curso   = (string) $request['course'];
                    $nombre  = trim($request['given_name'] . ' ' . $request['surname']);
                    $inicial = mb_strtoupper(mb_substr($nombre, 0, 1, 'UTF-8'), 'UTF-8');
                    $t = strtotime($request['created_at']);

                    // Eliminar no tiene vuelta atrás y se lleva los adjuntos por
                    // delante, así que la confirmación lo dice con todas las letras.
                    $_aviso = "¿Eliminar la solicitud #{$request['id']} de {$nombre}?\n\n"
                            . "Se borrarán también los archivos que haya subido.\n"
                            . "Esta acción no se puede deshacer.";
                ?>
                <tr>
                    <td><span class="cod">#<?= (int) $request['id'] ?></span></td>

                    <!-- Solicitante: inicial, nombre, correo y nacionalidad -->
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar-ini"><?= htmlspecialchars($inicial) ?></div>
                            <div class="min-w-0">
                                <div class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:220px"
                                     title="<?= htmlspecialchars($nombre, ENT_QUOTES) ?>">
                                    <?= htmlspecialchars($nombre) ?>
                                </div>
                                <a href="mailto:<?= htmlspecialchars($request['email'], ENT_QUOTES) ?>"
                                   class="block text-[11px] font-semibold text-slate-400 truncate hover:text-blue-600"
                                   style="max-width:220px" title="<?= htmlspecialchars($request['email'], ENT_QUOTES) ?>">
                                    <?= htmlspecialchars($request['email']) ?>
                                </a>
                                <?php if (!empty($request['nationality'])): ?>
                                <div class="sub-cat truncate mt-0.5" style="max-width:220px">
                                    <?= htmlspecialchars((string) $request['nationality']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>

                    <td>
                        <div class="truncate" style="max-width:300px" title="<?= htmlspecialchars($curso, ENT_QUOTES) ?>">
                            <?= htmlspecialchars($curso) ?>
                        </div>
                    </td>

                    <td>
                        <span class="pastilla <?= $clasePastilla ?>">
                            <i data-lucide="<?= $icono ?>" class="ic" style="width:11px;height:11px"></i><?= $etiqueta ?>
                        </span>
                    </td>

                    <td class="text-slate-400 text-[11.5px] whitespace-nowrap">
                        <div><?= date('d/m/Y', $t) ?></div>
                        <div class="text-slate-300"><?= date('H:i', $t) ?></div>
                    </td>

                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/admission-requests/<?= (int) $request['id'] ?>" class="ib ib-azul" title="Ver la solicitud">
                                <i data-lucide="eye" class="ic ic-14"></i>
                            </a>
                            <a href="/manager/admission-requests/<?= (int) $request['id'] ?>/download-word"
                               class="ib ib-verde" title="Descargar Word">
                                <i data-lucide="file-text" class="ic ic-14"></i>
                            </a>
                            <?php /* El id va como número: se manda tal cual al servidor */ ?>
                            <button onclick="toggleDropdown(event, <?= (int) $request['id'] ?>)" class="ib ib-ambar"
                                    title="Cambiar el estado">
                                <i data-lucide="square-pen" class="ic ic-14"></i>
                            </button>
                            <form method="POST" action="/manager/admission-requests/delete"
                                  onsubmit="return confirm(<?= htmlspecialchars(json_encode($_aviso), ENT_QUOTES, 'UTF-8') ?>);">
                                <?= \Core\Security::getCsrfField() ?>
                                <input type="hidden" name="id" value="<?= (int) $request['id'] ?>">
                                <input type="hidden" name="volver_a" value="<?= htmlspecialchars('/manager/admission-requests?' . ($_SERVER['QUERY_STRING'] ?? ''), ENT_QUOTES) ?>">
                                <button type="submit" class="ib ib-rojo" title="Eliminar la solicitud">
                                    <i data-lucide="trash-2" class="ic ic-14"></i>
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

    <!--
        Pie con el recuento, las filas por pantalla y el paginador. La ventana
        de páginas sigue a la actual en vez de pintar siempre las cinco
        primeras.
    -->
    <div class="px-4 py-3.5 border-t flex flex-wrap items-center justify-between gap-3"
         style="border-color: var(--card-border)">

        <div class="flex items-center gap-3 flex-wrap">
            <p class="text-[12px] font-semibold text-slate-400">
                <?php if ($totalCount): ?>
                Mostrando <?= number_format($primero) ?>-<?= number_format($ultimo) ?> de <?= number_format($totalCount) ?>
                <?php else: ?>
                Sin resultados
                <?php endif; ?>
            </p>

            <form method="GET" action="/manager/admission-requests" class="flex items-center gap-2">
                <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus, ENT_QUOTES) ?>">
                <input type="hidden" name="search" value="<?= htmlspecialchars($currentSearch, ENT_QUOTES) ?>">
                <span class="text-[11.5px] font-semibold text-slate-400">Mostrar</span>
                <select name="per_page" class="campo" onchange="this.form.submit()" aria-label="Filas por página">
                    <?php foreach ($perPageOptions as $n): ?>
                    <option value="<?= $n ?>" <?= $perPage === (int) $n ? 'selected' : '' ?>><?= $n ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="text-[11.5px] font-semibold text-slate-400">filas</span>
            </form>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="flex items-center gap-1.5">
            <a href="<?= $enlace(max(1, $currentPage - 1)) ?>"
               class="pag <?= $currentPage <= 1 ? 'inerte pointer-events-none' : '' ?>" title="Anterior">
                <i data-lucide="chevron-left" class="ic ic-14"></i>
            </a>

            <?php for ($i = $desde; $i <= $hasta; $i++): ?>
            <a href="<?= $enlace($i) ?>" class="pag <?= $i === $currentPage ? 'activa' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <a href="<?= $enlace(min($totalPages, $currentPage + 1)) ?>"
               class="pag <?= $currentPage >= $totalPages ? 'inerte pointer-events-none' : '' ?>" title="Siguiente">
                <i data-lucide="chevron-right" class="ic ic-14"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Ficha de la solicitud -->
<div id="viewModal" class="hidden fixed inset-0 bg-slate-900/60 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl">

        <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color: var(--card-border)">
            <h3 class="text-[15px] font-black text-slate-800 flex items-center gap-2.5">
                <i data-lucide="graduation-cap" class="ic ic-17 text-blue-600"></i>
                Detalle de la solicitud
            </h3>
            <button onclick="closeModal()" class="btn-atras" title="Cerrar">
                <i data-lucide="x" class="ic ic-17"></i>
            </button>
        </div>

        <div id="modalBody" class="p-5">
            <div class="vacio"><p>Cargando…</p></div>
        </div>

        <div id="modalFooter" class="hidden flex justify-between items-center px-5 py-3.5 border-t bg-slate-50 rounded-b-2xl"
             style="border-color: var(--card-border)">
            <div id="modalStatusBadge"></div>
            <button onclick="closeModal()" class="btn-sec">Cerrar</button>
        </div>
    </div>
</div>

<style>
/*
    CSS normal, no @apply: Tailwind se carga por CDN y en ese modo @apply
    no se procesa, así que estas reglas no pintaban absolutamente nada.
*/
.detail-row {
    display: flex;
    padding: .7rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.detail-row:last-child { border-bottom: 0; }
.detail-label {
    width: 11rem;
    flex-shrink: 0;
    font-size: 9.5px;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .09em;
    padding-top: 2px;
}
.detail-value { flex: 1; font-size: 12.5px; color: #334155; }
</style>

<script>
// Menú de estado, compartido y en position:fixed para que no lo recorte la tabla
const _dd = document.createElement('div');
_dd.id = '_statusDrop';
_dd.className = 'hidden fixed z-[9999] w-44 bg-white rounded-xl shadow-2xl py-1';
_dd.style.border = '1px solid #e8edf6';
_dd.innerHTML = `
    <button data-s="pending"  class="w-full text-left px-4 py-2 text-[12.5px] font-semibold text-slate-600 hover:bg-amber-50">Pendiente</button>
    <button data-s="reviewed" class="w-full text-left px-4 py-2 text-[12.5px] font-semibold text-slate-600 hover:bg-blue-50">Revisada</button>
    <button data-s="approved" class="w-full text-left px-4 py-2 text-[12.5px] font-semibold text-slate-600 hover:bg-emerald-50">Aprobada</button>
    <button data-s="rejected" class="w-full text-left px-4 py-2 text-[12.5px] font-semibold text-slate-600 hover:bg-rose-50">Rechazada</button>
`;
document.body.appendChild(_dd);
let _ddId = null;

function toggleDropdown(e, id) {
    e.stopPropagation();
    const btn   = e.currentTarget;
    const rect  = btn.getBoundingClientRect();
    const same  = _ddId === id && !_dd.classList.contains('hidden');

    _dd.classList.add('hidden');
    if (same) { _ddId = null; return; }

    _ddId = id;

    /*
        El menú es position:fixed, así que sus coordenadas son las del
        viewport y getBoundingClientRect() ya las da en ese sistema.
        Se muestra antes de medir porque un elemento oculto no tiene alto,
        y se ajusta para que no se salga por abajo ni por los lados.
    */
    _dd.classList.remove('hidden');

    const margen = 8;
    const ancho  = _dd.offsetWidth;
    const alto   = _dd.offsetHeight;

    let top = rect.bottom + 4;
    if (top + alto > window.innerHeight - margen) {
        top = rect.top - alto - 4;               // no cabe debajo: se abre hacia arriba
    }
    top = Math.max(margen, Math.min(top, window.innerHeight - alto - margen));

    let left = rect.right - ancho;               // alineado con el botón por la derecha
    left = Math.max(margen, Math.min(left, window.innerWidth - ancho - margen));

    _dd.style.top  = top + 'px';
    _dd.style.left = left + 'px';

    _dd.querySelectorAll('button[data-s]').forEach(b => {
        b.onclick = () => setStatusInline(id, b.dataset.s);
    });
}

document.addEventListener('click', () => { _dd.classList.add('hidden'); _ddId = null; });
_dd.addEventListener('click', e => e.stopPropagation());
window.addEventListener('scroll', () => { _dd.classList.add('hidden'); _ddId = null; });

function setStatusInline(id, status) {
    _dd.classList.add('hidden');
    _ddId = null;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', status);
    formData.append('csrf_token', '<?= \Core\Security::generateCsrfToken() ?>');

    fetch('/manager/admission-requests/update-status', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert('Error: ' + data.message); }
        })
        .catch(() => alert('Error de conexión. Inténtalo de nuevo.'));
}

function viewRequest(id) {
    document.getElementById('viewModal').classList.remove('hidden');
    document.getElementById('modalFooter').classList.add('hidden');
    document.getElementById('modalBody').innerHTML = '<div class="vacio"><p>Cargando…</p></div>';

    fetch(`/manager/admission-requests/${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) throw new Error(res.message);
            const d = res.data;

            const pastillas = {
                pending:  'p-ambar',
                reviewed: 'p-azul',
                approved: 'p-verde',
                rejected: 'p-rojo'
            };
            const nombres = {
                pending: 'Pendiente', reviewed: 'Revisada',
                approved: 'Aprobada', rejected: 'Rechazada'
            };
            const aceptado = parseInt(d.consent_accepted) === 1;

            document.getElementById('modalBody').innerHTML = `
                <div class="card" style="padding:4px 16px">
                    <div class="detail-row"><span class="detail-label">Solicitud</span><span class="detail-value font-bold text-blue-700">#${d.id}</span></div>
                    <div class="detail-row"><span class="detail-label">Nombre</span><span class="detail-value">${esc(d.given_name)}</span></div>
                    <div class="detail-row"><span class="detail-label">Apellidos</span><span class="detail-value">${esc(d.surname)}</span></div>
                    <div class="detail-row"><span class="detail-label">Pasaporte / Cédula</span><span class="detail-value">${esc(d.passport_id)}</span></div>
                    <div class="detail-row"><span class="detail-label">Nacionalidad</span><span class="detail-value">${esc(d.nationality)}</span></div>
                    <div class="detail-row"><span class="detail-label">Fecha de nacimiento</span><span class="detail-value">${esc(d.date_of_birth)}</span></div>
                    <div class="detail-row"><span class="detail-label">País de nacimiento</span><span class="detail-value">${esc(d.country_of_birth)}</span></div>
                    <div class="detail-row"><span class="detail-label">Correo</span><span class="detail-value"><a href="mailto:${esc(d.email)}" class="text-blue-600 hover:underline">${esc(d.email)}</a></span></div>
                    <div class="detail-row"><span class="detail-label">Teléfono</span><span class="detail-value">${esc(d.phone)}</span></div>
                    <div class="detail-row"><span class="detail-label">Dirección</span><span class="detail-value">${esc(d.address)}</span></div>
                    ${d.capacity ? `<div class="detail-row"><span class="detail-label">Capacidad</span><span class="detail-value">${esc(d.capacity)}</span></div>` : ''}
                    <div class="detail-row"><span class="detail-label">Curso</span><span class="detail-value font-bold">${esc(d.course)}</span></div>
                    <div class="detail-row"><span class="detail-label">Consentimiento</span><span class="detail-value"><span class="pastilla ${aceptado ? 'p-verde' : 'p-rojo'}">${aceptado ? 'Aceptado' : 'No aceptado'}</span></span></div>
                    <div class="detail-row"><span class="detail-label">Estado</span><span class="detail-value"><span class="pastilla ${pastillas[d.status] || 'p-gris'}">${nombres[d.status] || esc(d.status)}</span></span></div>
                    ${d.notes ? `<div class="detail-row"><span class="detail-label">Notas</span><span class="detail-value text-slate-500 italic">${esc(d.notes)}</span></div>` : ''}
                    <div class="detail-row"><span class="detail-label">Recibida</span><span class="detail-value text-slate-400">${new Date(d.created_at).toLocaleString()}</span></div>
                </div>`;

            document.getElementById('modalStatusBadge').innerHTML =
                `<span class="pastilla ${pastillas[d.status] || 'p-gris'}">${nombres[d.status] || esc(d.status)}</span>`;
            document.getElementById('modalFooter').classList.remove('hidden');
        })
        .catch(err => {
            document.getElementById('modalBody').innerHTML =
                `<div class="vacio"><p class="text-rose-500">${err.message}</p></div>`;
        });
}

function closeModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

// Cerrar al pulsar fuera de la ficha
document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function esc(str) {
    if (!str) return '—';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
