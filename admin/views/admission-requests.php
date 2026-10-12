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
            <a href="/manager/admission-requests" 
               class="status-card <?php echo empty($currentStatus) ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">All Requests</p>
                        <p class="text-xl font-black"><?php echo array_sum($statusCounts); ?></p>
                    </div>
                    <i class="fas fa-list text-gray-300 text-lg"></i>
                </div>
            </a>

            <a href="/manager/admission-requests?status=pending" 
               class="status-card <?php echo $currentStatus === 'pending' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Pending</p>
                        <p class="text-xl font-black text-yellow-600"><?php echo $statusCounts['pending']; ?></p>
                    </div>
                    <i class="fas fa-clock text-yellow-400 text-lg"></i>
                </div>
            </a>

            <a href="/manager/admission-requests?status=reviewed" 
               class="status-card <?php echo $currentStatus === 'reviewed' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Reviewed</p>
                        <p class="text-xl font-black text-blue-600"><?php echo $statusCounts['reviewed']; ?></p>
                    </div>
                    <i class="fas fa-eye text-blue-400 text-lg"></i>
                </div>
            </a>

            <a href="/manager/admission-requests?status=approved" 
               class="status-card <?php echo $currentStatus === 'approved' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Approved</p>
                        <p class="text-xl font-black text-green-600"><?php echo $statusCounts['approved']; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-400 text-lg"></i>
                </div>
            </a>

            <a href="/manager/admission-requests?status=rejected" 
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

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-md p-3 mb-4">
        <form method="GET" action="/manager/admission-requests" class="flex gap-4">
            <?php if ($currentStatus): ?>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($currentStatus); ?>">
            <?php endif; ?>
            <input type="text" 
                   name="search" 
                   value="<?php echo htmlspecialchars($currentSearch); ?>"
                   placeholder="Search by name or email..." 
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:outline-none">
            <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-search mr-2"></i>Search
            </button>
            <?php if ($currentSearch || $currentStatus): ?>
                <a href="/manager/admission-requests" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

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
            <table class="min-w-full table-fixed divide-y divide-gray-200 text-[13px]">
                <thead class="bg-gray-50">
                    <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        <th class="px-3 py-2 w-16">ID</th>
                        <th class="px-3 py-2 w-48">Name</th>
                        <th class="px-3 py-2 w-56">Email</th>
                        <th class="px-3 py-2">Course</th>
                        <th class="px-2 py-2 w-14 text-center">St.</th>
                        <th class="px-3 py-2 w-28">Date</th>
                        <th class="px-3 py-2 w-24 text-right">Actions</th>
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

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?><?php echo $currentStatus ? '&status=' . $currentStatus : ''; ?><?php echo $currentSearch ? '&search=' . urlencode($currentSearch) : ''; ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    <?php endif; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?php echo $currentPage + 1; ?><?php echo $currentStatus ? '&status=' . $currentStatus : ''; ?><?php echo $currentSearch ? '&search=' . urlencode($currentSearch) : ''; ?>" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing page <span class="font-medium"><?php echo $currentPage; ?></span> of <span class="font-medium"><?php echo $totalPages; ?></span>
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php for ($i = 1; $i <= min($totalPages, 5); $i++): ?>
                                <a href="?page=<?php echo $i; ?><?php echo $currentStatus ? '&status=' . $currentStatus : ''; ?><?php echo $currentSearch ? '&search=' . urlencode($currentSearch) : ''; ?>" 
                                   class="<?php echo $i === $currentPage ? 'bg-cyan-50 border-cyan-500 text-cyan-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
