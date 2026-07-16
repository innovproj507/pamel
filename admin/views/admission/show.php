<?php
$statusColors = [
    'pending'  => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'reviewed' => 'bg-blue-100 text-blue-800 border-blue-200',
    'approved' => 'bg-green-100 text-green-800 border-green-200',
    'rejected' => 'bg-red-100 text-red-800 border-red-200',
];
$statusIcons = [
    'pending'  => 'fa-clock',
    'reviewed' => 'fa-eye',
    'approved' => 'fa-check-circle',
    'rejected' => 'fa-times-circle',
];
$status      = $request['status'] ?? 'pending';
$colorClass  = $statusColors[$status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
$iconClass   = $statusIcons[$status]  ?? 'fa-circle';
?>

<div class="p-6 max-w-4xl mx-auto">

    <!-- Back link -->
    <div class="mb-6">
        <a href="/manager/admission-requests" class="text-cyan-600 hover:text-cyan-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Admission Requests
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 px-8 py-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-user-graduate mr-3"></i>
                        Admission Request #<?= $request['id'] ?>
                    </h1>
                    <p class="text-blue-100 mt-1">
                        Submitted on <?= date('F j, Y', strtotime($request['created_at'])) ?>
                        at <?= date('H:i', strtotime($request['created_at'])) ?>
                    </p>
                </div>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold border <?= $colorClass ?>">
                    <i class="fas <?= $iconClass ?> mr-2"></i>
                    <?= ucfirst($status) ?>
                </span>
            </div>
        </div>

        <div class="p-8">

            <!-- Applicant Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user text-cyan-600 mr-2"></i>Applicant Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 rounded-xl p-5">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Given Name</label>
                        <p class="text-gray-900 mt-1 font-medium"><?= htmlspecialchars($request['given_name']) ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Surname</label>
                        <p class="text-gray-900 mt-1 font-medium"><?= htmlspecialchars($request['surname']) ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Passport / ID</label>
                        <p class="text-gray-900 mt-1"><?= htmlspecialchars($request['passport_id']) ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nationality</label>
                        <p class="text-gray-900 mt-1"><?= htmlspecialchars($request['nationality']) ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Date of Birth</label>
                        <p class="text-gray-900 mt-1"><?= htmlspecialchars($request['date_of_birth']) ?></p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Country of Birth</label>
                        <p class="text-gray-900 mt-1"><?= htmlspecialchars($request['country_of_birth']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-address-card text-cyan-600 mr-2"></i>Contact Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 rounded-xl p-5">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</label>
                        <p class="mt-1">
                            <a href="mailto:<?= htmlspecialchars($request['email']) ?>" class="text-cyan-600 hover:underline font-medium">
                                <?= htmlspecialchars($request['email']) ?>
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Phone</label>
                        <p class="text-gray-900 mt-1"><?= htmlspecialchars($request['phone']) ?></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Address</label>
                        <p class="text-gray-900 mt-1"><?= htmlspecialchars($request['address']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Course -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-graduation-cap text-cyan-600 mr-2"></i>Requested Course
                </h2>
                <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-5 mb-4">
                    <p class="text-gray-900 font-semibold text-lg"><?= htmlspecialchars($request['course']) ?></p>
                </div>
                <div class="bg-<?= (int)$request['consent_accepted'] === 1 ? 'green' : 'red' ?>-50 border border-<?= (int)$request['consent_accepted'] === 1 ? 'green' : 'red' ?>-200 rounded-xl p-5">
                    <p class="text-<?= (int)$request['consent_accepted'] === 1 ? 'green' : 'red' ?>-700 font-semibold text-sm">
                        <i class="fas fa-<?= (int)$request['consent_accepted'] === 1 ? 'check-circle' : 'times-circle' ?> mr-2"></i>
                        Terms and Conditions Consent: <?= (int)$request['consent_accepted'] === 1 ? 'Accepted' : 'Not Accepted' ?>
                    </p>
                </div>
            </div>

            <!-- Attached Documents -->
            <?php if (!empty($request['id_file']) || !empty($request['health_certificate_file'])): ?>
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-paperclip text-cyan-600 mr-2"></i>Attached Documents
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if (!empty($request['id_file'])): ?>
                    <a href="<?= $request['id_file'] ?>" target="_blank" 
                       class="flex items-center p-4 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition group">
                        <div class="w-12 h-12 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center mr-4 group-hover:bg-cyan-200 transition">
                            <i class="fas fa-id-card text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">ID / Passport</p>
                            <p class="text-xs text-gray-500">Click to view document</p>
                        </div>
                        <i class="fas fa-external-link-alt ml-auto text-gray-400 group-hover:text-cyan-600"></i>
                    </a>
                    <?php endif; ?>

                    <?php if (!empty($request['health_certificate_file'])): ?>
                    <a href="<?= $request['health_certificate_file'] ?>" target="_blank" 
                       class="flex items-center p-4 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition group">
                        <div class="w-12 h-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center mr-4 group-hover:bg-green-200 transition">
                            <i class="fas fa-file-medical text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Health Certificate</p>
                            <p class="text-xs text-gray-500">Click to view document</p>
                        </div>
                        <i class="fas fa-external-link-alt ml-auto text-gray-400 group-hover:text-green-600"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Update Status Panel -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-exchange-alt text-cyan-600 mr-2"></i>Update Status
                </h2>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">

                    <!-- Quick action buttons -->
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Quick Actions</p>
                    <div class="flex flex-wrap gap-2 mb-5">
                        <?php
                        $quickActions = [
                            'pending'  => ['label' => 'Pending',  'icon' => 'fa-clock',        'btn' => 'bg-yellow-500 hover:bg-yellow-600'],
                            'reviewed' => ['label' => 'Reviewed', 'icon' => 'fa-eye',           'btn' => 'bg-blue-500 hover:bg-blue-600'],
                            'approved' => ['label' => 'Approved', 'icon' => 'fa-check-circle',  'btn' => 'bg-green-500 hover:bg-green-600'],
                            'rejected' => ['label' => 'Rejected', 'icon' => 'fa-times-circle',  'btn' => 'bg-red-500 hover:bg-red-600'],
                        ];
                        foreach ($quickActions as $key => $action):
                            $active = $status === $key ? 'ring-2 ring-offset-2 ring-gray-400 opacity-60 cursor-default' : 'cursor-pointer';
                        ?>
                        <button type="button"
                                onclick="setStatus('<?= $key ?>')"
                                <?= $status === $key ? 'disabled' : '' ?>
                                class="<?= $action['btn'] ?> <?= $active ?> text-white px-4 py-2 rounded-lg font-semibold text-sm transition flex items-center gap-2">
                            <i class="fas <?= $action['icon'] ?>"></i>
                            <?= $action['label'] ?>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Notes textarea -->
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Notes <span class="font-normal normal-case">(optional)</span></p>
                    <textarea id="statusNotes" rows="3"
                              placeholder="Add internal notes about this request..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-cyan-500 focus:ring-2 focus:ring-cyan-200 focus:outline-none resize-none transition text-sm"><?= htmlspecialchars($request['notes'] ?? '') ?></textarea>

                    <!-- Feedback message -->
                    <div id="statusFeedback" class="hidden mt-3 px-4 py-2 rounded-lg text-sm font-medium"></div>
                </div>
            </div>

            <!-- Bottom actions -->
            <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
                <a href="/manager/admission-requests"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
                <a href="/manager/admission-requests/<?= $request['id'] ?>/download-word"
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-file-word mr-2"></i>Download Word
                </a>
                <button onclick="window.print()"
                        class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function setStatus(newStatus) {
    const notes   = document.getElementById('statusNotes').value.trim();
    const feedback = document.getElementById('statusFeedback');

    const formData = new FormData();
    formData.append('id', <?= $request['id'] ?>);
    formData.append('status', newStatus);
    formData.append('csrf_token', '<?= \Core\Security::generateCsrfToken() ?>');
    if (notes) formData.append('notes', notes);

    fetch('/manager/admission-requests/update-status', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            feedback.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
            if (data.success) {
                feedback.classList.add('bg-green-100', 'text-green-800');
                feedback.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Status updated successfully. Refreshing...';
                setTimeout(() => location.reload(), 1200);
            } else {
                feedback.classList.add('bg-red-100', 'text-red-800');
                feedback.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + data.message;
            }
        })
        .catch(() => {
            feedback.classList.remove('hidden');
            feedback.classList.add('bg-red-100', 'text-red-800');
            feedback.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Connection error. Please try again.';
        });
}
</script>

<style>
@media print {
    nav, a, button, #statusFeedback, textarea, .bg-gray-50 { display: none !important; }
    .shadow-lg { box-shadow: none !important; }
}
</style>
