<?php
/**
 * Listado de solicitudes de admisión.
 *
 * Valores por defecto arriba: la vista se dibuja aunque el controlador no
 * mande las filas por pantalla, y así $perPage ya existe cuando lo usan
 * los enlaces de las tarjetas de estado.
 */
$perPage        = $perPage        ?? 15;
$perPageOptions = $perPageOptions ?? [5, 10, 15, 25, 50];
?>
<div class="container mx-auto px-4 py-5">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Admission Requests</h1>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Total: <?php echo $totalCount; ?></span>
        </div>
    </div>

    <!-- Status Filters -->
    <div class="bg-white rounded-lg shadow-md p-3 mb-4">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
            <a href="/manager/admission-requests?per_page=<?php echo $perPage; ?>"
               class="status-card <?php echo empty($currentStatus) ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">All Requests</p>
                        <p class="text-xl font-black"><?php echo array_sum($statusCounts); ?></p>
                    </div>
                    <i class="fas fa-list text-gray-300 text-lg"></i>
                </div>
            </a>

            <a href="/manager/admission-requests?status=pending&per_page=<?php echo $perPage; ?>" 
               class="status-card <?php echo $currentStatus === 'pending' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Pending</p>
                        <p class="text-xl font-black text-yellow-600"><?php echo $statusCounts['pending']; ?></p>
                    </div>
                    <i class="fas fa-clock text-yellow-400 text-lg"></i>
                </div>
            </a>

            <a href="/manager/admission-requests?status=reviewed&per_page=<?php echo $perPage; ?>" 
               class="status-card <?php echo $currentStatus === 'reviewed' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Reviewed</p>
                        <p class="text-xl font-black text-blue-600"><?php echo $statusCounts['reviewed']; ?></p>
                    </div>
                    <i class="fas fa-eye text-blue-400 text-lg"></i>
                </div>
            </a>

            <a href="/manager/admission-requests?status=approved&per_page=<?php echo $perPage; ?>" 
               class="status-card <?php echo $currentStatus === 'approved' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Approved</p>
                        <p class="text-xl font-black text-green-600"><?php echo $statusCounts['approved']; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-400 text-lg"></i>
                </div>
            </a>

            <a href="/manager/admission-requests?status=rejected&per_page=<?php echo $perPage; ?>" 
               class="status-card <?php echo $currentStatus === 'rejected' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Rejected</p>
                        <p class="text-xl font-black text-red-600"><?php echo $statusCounts['rejected']; ?></p>
                    </div>
                    <i class="fas fa-times-circle text-red-400 text-lg"></i>
                </div>
            </a>
        </div>
    </div>

    <!--
        Barra de filtros en una sola línea: buscar, estado y cuántas filas
        mostrar. Los desplegables se envían solos al cambiar, así que el
        único botón que queda es el de buscar.
    -->
    <?php
    $etiquetasEstado = [
        ''         => 'All statuses',
        'pending'  => 'Pending',
        'reviewed' => 'Reviewed',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];
    ?>
    <form method="GET" action="/manager/admission-requests"
          class="bg-white rounded-lg shadow-md p-2.5 mb-4 flex flex-wrap items-center gap-2">

        <!-- Buscar -->
        <div class="relative flex-1 min-w-[220px]">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-xs"></i>
            <input type="search" name="search" value="<?php echo htmlspecialchars($currentSearch); ?>"
                   placeholder="Search by name or email…"
                   class="w-full pl-9 pr-3 py-2 text-[13px] border border-gray-200 rounded-lg focus:border-cyan-500 focus:outline-none transition">
        </div>

        <!-- Estado -->
        <select name="status" onchange="this.form.submit()"
                class="py-2 pl-3 pr-8 text-[13px] border border-gray-200 rounded-lg bg-white focus:border-cyan-500 focus:outline-none transition cursor-pointer">
            <?php foreach ($etiquetasEstado as $valor => $texto): ?>
            <option value="<?php echo $valor; ?>" <?php echo $currentStatus === $valor ? 'selected' : ''; ?>>
                <?php echo $texto; ?>
            </option>
            <?php endforeach; ?>
        </select>

        <!-- Filas por pantalla -->
        <label class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-400 uppercase tracking-wide whitespace-nowrap">
            Show
            <select name="per_page" onchange="this.form.submit()"
                    class="py-2 pl-2.5 pr-7 text-[13px] font-normal normal-case tracking-normal text-gray-700 border border-gray-200 rounded-lg bg-white focus:border-cyan-500 focus:outline-none transition cursor-pointer">
                <?php foreach ($perPageOptions as $n): ?>
                <option value="<?php echo $n; ?>" <?php echo (int) $perPage === $n ? 'selected' : ''; ?>><?php echo $n; ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit"
                class="px-4 py-2 text-[13px] font-semibold bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition">
            Search
        </button>

        <?php if ($currentSearch || $currentStatus): ?>
        <a href="/manager/admission-requests"
           class="w-9 h-9 inline-flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:border-rose-200 hover:text-rose-500 transition"
           title="Clear filters">
            <i class="fas fa-times text-xs"></i>
        </a>
        <?php endif; ?>
    </form>

    <!--
        Tabla compacta: el contenido se cortaba porque cada celda llevaba
        px-6 py-4 y el nombre del curso estaba limitado a max-w-xs. Ahora el
        relleno es menor, el texto más pequeño y el curso se lleva el ancho
        sobrante; lo que no cabe se corta con puntos suspensivos y el texto
        completo queda en el title.
    -->
    <?php
    // Estado -> icono, color y etiqueta. El icono sustituye a la palabra.
    $estados = [
        'pending'  => ['fa-clock',        'amber',   'Pending'],
        'reviewed' => ['fa-eye',          'blue',    'Reviewed'],
        'approved' => ['fa-circle-check', 'emerald', 'Approved'],
        'rejected' => ['fa-circle-xmark', 'rose',    'Rejected'],
    ];
    ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <!--
                w-full, no min-w-full: con min-width la tabla crece hasta
                caber el texto más largo, el recorte nunca se aplica y las
                últimas columnas quedan fuera de la pantalla. Con w-full el
                ancho es el del contenedor, cada columna respeta el suyo y
                el nombre del curso se corta con puntos suspensivos.
            -->
            <table class="w-full min-w-[900px] table-fixed divide-y divide-gray-200 text-[13px]">
                <thead class="bg-gray-50">
                    <!--
                        Anchos en porcentaje y sumando 100 exactos. Con anchos
                        en píxeles, el sobrante iba entero a la columna del
                        curso: se estiraba sin límite y empujaba las últimas
                        columnas fuera de la pantalla.
                    -->
                    <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="px-3 py-2 w-[5%]">ID</th>
                        <th class="px-3 py-2 w-[16%]">Name</th>
                        <th class="px-3 py-2 w-[19%]">Email</th>
                        <th class="px-3 py-2 w-[34%]">Course</th>
                        <th class="px-2 py-2 w-[5%] text-center">St.</th>
                        <th class="px-3 py-2 w-[11%]">Date</th>
                        <th class="px-3 py-2 w-[10%] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="7" class="px-3 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>No admission requests found.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $request):
                            $st = $request['status'] ?? 'pending';
                            [$icono, $color, $etiqueta] = $estados[$st] ?? $estados['pending'];
                            $curso = (string) $request['course'];
                        ?>
                            <tr class="hover:bg-gray-50 align-middle">
                                <td class="px-3 py-2 whitespace-nowrap text-gray-400 font-semibold">
                                    #<?php echo $request['id']; ?>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="font-semibold text-gray-900 truncate"
                                         title="<?php echo htmlspecialchars($request['given_name'] . ' ' . $request['surname']); ?>">
                                        <?php echo htmlspecialchars($request['given_name'] . ' ' . $request['surname']); ?>
                                    </div>
                                    <div class="text-[11px] text-gray-400 truncate">
                                        <?php echo htmlspecialchars((string) $request['nationality']); ?>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <a href="mailto:<?php echo htmlspecialchars($request['email']); ?>"
                                       class="block truncate text-gray-600 hover:text-cyan-600"
                                       title="<?php echo htmlspecialchars($request['email']); ?>">
                                        <?php echo htmlspecialchars($request['email']); ?>
                                    </a>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="truncate text-gray-800" title="<?php echo htmlspecialchars($curso); ?>">
                                        <?php echo htmlspecialchars($curso); ?>
                                    </div>
                                </td>
                                <td class="px-2 py-2 text-center">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-<?php echo $color; ?>-50 text-<?php echo $color; ?>-600 border border-<?php echo $color; ?>-200"
                                          title="<?php echo $etiqueta; ?>">
                                        <i class="fas <?php echo $icono; ?> text-xs"></i>
                                        <span class="sr-only"><?php echo $etiqueta; ?></span>
                                    </span>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-gray-500">
                                    <?php $t = strtotime($request['created_at']); ?>
                                    <div><?php echo date('d/m/Y', $t); ?></div>
                                    <div class="text-[11px] text-gray-400"><?php echo date('H:i', $t); ?></div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="/manager/admission-requests/<?php echo $request['id']; ?>"
                                           class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-gray-400 hover:bg-cyan-50 hover:text-cyan-600 transition"
                                           title="View request">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="/manager/admission-requests/<?php echo $request['id']; ?>/download-word"
                                           class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-gray-400 hover:bg-emerald-50 hover:text-emerald-600 transition"
                                           title="Download Word">
                                            <i class="fas fa-file-word text-xs"></i>
                                        </a>
                                        <button onclick="toggleDropdown(event, 'dd-<?php echo $request['id']; ?>')"
                                                class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition"
                                                title="Change status">
                                            <i class="fas fa-pen-to-square text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!--
            Paginación. Antes armaba la URL a mano tres veces y sólo pintaba
            las páginas 1 a 5: con pocas filas por pantalla no había forma de
            llegar a la sexta. Ahora la ventana sigue a la página actual.
        -->
        <?php
        /** URL de una página conservando filtros y filas por pantalla. */
        $enlace = static function (int $p) use ($currentStatus, $currentSearch, $perPage): string {
            return '?' . http_build_query(array_filter([
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
        <div class="px-4 py-2.5 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 text-[12px]">
            <p class="text-gray-400">
                Showing <strong class="text-gray-600"><?php echo $primero; ?>–<?php echo $ultimo; ?></strong>
                of <strong class="text-gray-600"><?php echo $totalCount; ?></strong>
            </p>

            <?php if ($totalPages > 1): ?>
            <nav class="inline-flex items-center gap-1">
                <a href="<?php echo $enlace(max(1, $currentPage - 1)); ?>"
                   class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-gray-200 <?php
                       echo $currentPage <= 1 ? 'text-gray-200 pointer-events-none' : 'text-gray-400 hover:bg-gray-50'; ?>"
                   title="Previous">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </a>

                <?php for ($i = $desde; $i <= $hasta; $i++): ?>
                <a href="<?php echo $enlace($i); ?>"
                   class="min-w-[1.75rem] h-7 px-2 inline-flex items-center justify-center rounded-lg border font-semibold <?php
                       echo $i === $currentPage
                           ? 'bg-cyan-500 border-cyan-500 text-white'
                           : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50'; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>

                <a href="<?php echo $enlace(min($totalPages, $currentPage + 1)); ?>"
                   class="w-7 h-7 inline-flex items-center justify-center rounded-lg border border-gray-200 <?php
                       echo $currentPage >= $totalPages ? 'text-gray-200 pointer-events-none' : 'text-gray-400 hover:bg-gray-50'; ?>"
                   title="Next">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </a>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- View Detail Modal -->
<div id="viewModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-t-2xl">
            <h3 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-user-graduate mr-3"></i>
                Admission Request Detail
            </h3>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div id="modalBody" class="p-6 space-y-4">
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-cyan-500 text-3xl"></i>
                <p class="text-gray-500 mt-2">Loading...</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div id="modalFooter" class="hidden flex justify-between items-center px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
            <div id="modalStatusBadge"></div>
            <button onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-lg font-semibold transition">
                Close
            </button>
        </div>
    </div>
</div>

<style>
/*
    CSS normal, no @apply: Tailwind se carga por CDN y en ese modo @apply
    no se procesa, así que estas reglas no pintaban absolutamente nada.
    Por eso las tarjetas de filtro y las pastillas de estado salían sin
    estilo.
*/
.status-card {
    display: block;
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: .5rem;
    padding: 1rem;
    cursor: pointer;
    transition: border-color .15s, background-color .15s;
}
.status-card:hover  { border-color: #06b6d4; }
.status-card.active { border-color: #06b6d4; background: #ecfeff; }

.detail-row {
    display: flex;
    padding: .75rem 0;
    border-bottom: 1px solid #f3f4f6;
}
.detail-row:last-child { border-bottom: 0; }
.detail-label {
    width: 11rem;
    flex-shrink: 0;
    font-size: .75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .05em;
}
.detail-value { flex: 1; font-size: .875rem; color: #111827; }

/* Oculto a la vista, legible por lectores de pantalla */
.sr-only {
    position: absolute;
    width: 1px; height: 1px;
    padding: 0; margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap; border: 0;
}
</style>

<script>
// Shared fixed dropdown element
const _dd = document.createElement('div');
_dd.id = '_statusDrop';
_dd.className = 'hidden fixed z-[9999] w-44 bg-white border border-gray-200 rounded-xl shadow-2xl py-1';
_dd.innerHTML = `
    <button data-s="pending"  class="w-full text-left px-4 py-2 text-sm hover:bg-yellow-50 flex items-center gap-2"><i class="fas fa-clock text-yellow-500"></i> Pending</button>
    <button data-s="reviewed" class="w-full text-left px-4 py-2 text-sm hover:bg-blue-50   flex items-center gap-2"><i class="fas fa-eye text-blue-500"></i> Reviewed</button>
    <button data-s="approved" class="w-full text-left px-4 py-2 text-sm hover:bg-green-50  flex items-center gap-2"><i class="fas fa-check-circle text-green-500"></i> Approved</button>
    <button data-s="rejected" class="w-full text-left px-4 py-2 text-sm hover:bg-red-50    flex items-center gap-2"><i class="fas fa-times-circle text-red-500"></i> Rejected</button>
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
    // Position below the button
    _dd.style.top  = (rect.bottom + window.scrollY + 4) + 'px';
    _dd.style.left = (rect.left   + window.scrollX - 60) + 'px';
    _dd.classList.remove('hidden');

    // Wire click handlers
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
        .catch(() => alert('Connection error. Please try again.'));
}

function viewRequest(id) {
    document.getElementById('viewModal').classList.remove('hidden');
    document.getElementById('modalFooter').classList.add('hidden');
    document.getElementById('modalBody').innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-cyan-500 text-3xl"></i>
            <p class="text-gray-500 mt-2">Loading...</p>
        </div>`;

    fetch(`/manager/admission-requests/${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.success) throw new Error(res.message);
            const d = res.data;

            const statusColors = {
                pending:  'bg-yellow-100 text-yellow-800',
                reviewed: 'bg-blue-100 text-blue-800',
                approved: 'bg-green-100 text-green-800',
                rejected: 'bg-red-100 text-red-800'
            };

            document.getElementById('modalBody').innerHTML = `
                <div class="grid grid-cols-1 gap-0 divide-y divide-gray-100 bg-white rounded-xl border border-gray-100">
                    <div class="detail-row px-4"><span class="detail-label">Request #</span><span class="detail-value font-bold text-cyan-600">#${d.id}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Given Name</span><span class="detail-value">${esc(d.given_name)}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Surname</span><span class="detail-value">${esc(d.surname)}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Passport / ID</span><span class="detail-value">${esc(d.passport_id)}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Nationality</span><span class="detail-value">${esc(d.nationality)}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Date of Birth</span><span class="detail-value">${esc(d.date_of_birth)}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Country of Birth</span><span class="detail-value">${esc(d.country_of_birth)}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Email</span><span class="detail-value"><a href="mailto:${esc(d.email)}" class="text-cyan-600 hover:underline">${esc(d.email)}</a></span></div>
                    <div class="detail-row px-4"><span class="detail-label">Phone</span><span class="detail-value">${esc(d.phone)}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Address</span><span class="detail-value">${esc(d.address)}</span></div>
                    ${d.capacity ? `<div class="detail-row px-4"><span class="detail-label">Capacity</span><span class="detail-value">${esc(d.capacity)}</span></div>` : ''}
                    <div class="detail-row px-4"><span class="detail-label">Course</span><span class="detail-value font-semibold">${esc(d.course)}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Terms Consent</span><span class="detail-value text-${parseInt(d.consent_accepted) === 1 ? 'green' : 'red'}-600 font-bold"><i class="fas fa-${parseInt(d.consent_accepted) === 1 ? 'check-circle' : 'times-circle'}"></i> ${parseInt(d.consent_accepted) === 1 ? 'Accepted' : 'Not Accepted'}</span></div>
                    <div class="detail-row px-4"><span class="detail-label">Status</span><span class="detail-value"><span class="px-3 py-1 rounded-full text-xs font-bold ${statusColors[d.status] || ''}">${d.status.charAt(0).toUpperCase() + d.status.slice(1)}</span></span></div>
                    ${d.notes ? `<div class="detail-row px-4"><span class="detail-label">Notes</span><span class="detail-value text-gray-600 italic">${esc(d.notes)}</span></div>` : ''}
                    <div class="detail-row px-4"><span class="detail-label">Submitted</span><span class="detail-value text-gray-500">${new Date(d.created_at).toLocaleString()}</span></div>
                </div>`;

            document.getElementById('modalStatusBadge').innerHTML =
                `<span class="px-3 py-1 rounded-full text-xs font-bold ${statusColors[d.status] || ''}">${d.status}</span>`;
            document.getElementById('modalFooter').classList.remove('hidden');
        })
        .catch(err => {
            document.getElementById('modalBody').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                    <p>${err.message}</p>
                </div>`;
        });
}

function closeModal() {
    document.getElementById('viewModal').classList.add('hidden');
}

// Close when clicking backdrop
document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function esc(str) {
    if (!str) return '—';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function updateStatus(id, currentStatus) {
    const status = prompt('Enter new status (pending, reviewed, approved, rejected):', currentStatus);
    if (status && ['pending', 'reviewed', 'approved', 'rejected'].includes(status)) {
        const notes = prompt('Add notes (optional):');
        
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);
        if (notes) formData.append('notes', notes);

        fetch('/manager/admission-requests/update-status', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Status updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}
</script>
