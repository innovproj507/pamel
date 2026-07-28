<?php
$statusColors = [
    'pending'   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'quoted'    => 'bg-blue-100 text-blue-800 border-blue-200',
    'completed' => 'bg-green-100 text-green-800 border-green-200',
    'cancelled' => 'bg-red-100 text-red-800 border-red-200',
];
$statusIcons = [
    'pending'   => 'fa-clock',
    'quoted'    => 'fa-paper-plane',
    'completed' => 'fa-check-circle',
    'cancelled' => 'fa-times-circle',
];
$status     = $request['status'] ?? 'pending';
$colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
$iconClass  = $statusIcons[$status]  ?? 'fa-circle';
?>

<div class="p-6 max-w-4xl mx-auto">

    <div class="mb-6">
        <a href="/manager/quote-requests" class="text-cyan-600 hover:text-cyan-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Quote Requests
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

        <!-- Header -->
        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 px-8 py-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-file-invoice-dollar mr-3"></i>
                        Quote Request #<?= $request['id'] ?>
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

            <!-- Customer Information -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user text-cyan-600 mr-2"></i>Customer Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 rounded-xl p-5">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</label>
                        <p class="text-gray-900 mt-1 font-medium"><?= htmlspecialchars($request['customer_name']) ?></p>
                    </div>
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
                    <?php if (!empty($request['message'])): ?>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Message</label>
                        <p class="text-gray-900 mt-1"><?= nl2br(htmlspecialchars($request['message'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Requested Courses -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-graduation-cap text-cyan-600 mr-2"></i>Requested Courses
                </h2>
                <div class="bg-gray-50 rounded-xl overflow-hidden border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price at Request</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Current Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php foreach ($request['items'] as $item): ?>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= htmlspecialchars($item['product_name']) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-900"><?= (int)$item['quantity'] ?></td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <?= $item['price_at_request'] !== null ? '$' . number_format($item['price_at_request'], 2) : '—' ?>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                    <?= $item['current_price'] !== null ? '$' . number_format($item['current_price'], 2) : 'Not available' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Send Quote -->
            <?php if ($status === 'pending'): ?>
            <div class="mb-8">
                <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-6">
                    <p class="text-sm text-cyan-800 mb-4">
                        This will email the customer the current price for each course above, along with a link to complete their enrollment and payment.
                    </p>
                    <button onclick="sendQuote()" id="sendQuoteBtn"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        <i class="fas fa-paper-plane mr-2"></i>Send Quote
                    </button>
                    <div id="sendQuoteFeedback" class="hidden mt-3 px-4 py-2 rounded-lg text-sm font-medium"></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Manual status override -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-exchange-alt text-cyan-600 mr-2"></i>Update Status
                </h2>
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $quickActions = [
                            'pending'   => ['label' => 'Pending',   'icon' => 'fa-clock',        'btn' => 'bg-yellow-500 hover:bg-yellow-600'],
                            'quoted'    => ['label' => 'Quoted',    'icon' => 'fa-paper-plane',  'btn' => 'bg-blue-500 hover:bg-blue-600'],
                            'completed' => ['label' => 'Completed', 'icon' => 'fa-check-circle', 'btn' => 'bg-green-500 hover:bg-green-600'],
                            'cancelled' => ['label' => 'Cancelled', 'icon' => 'fa-times-circle', 'btn' => 'bg-red-500 hover:bg-red-600'],
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
                    <div id="statusFeedback" class="hidden mt-3 px-4 py-2 rounded-lg text-sm font-medium"></div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
                <a href="/manager/quote-requests"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function sendQuote() {
    const btn = document.getElementById('sendQuoteBtn');
    const feedback = document.getElementById('sendQuoteFeedback');
    btn.disabled = true;

    const formData = new FormData();
    formData.append('csrf_token', '<?= \Core\Security::generateCsrfToken() ?>');

    fetch('/manager/quote-requests/<?= $request['id'] ?>/send-quote', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            feedback.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
            if (data.success) {
                feedback.classList.add('bg-green-100', 'text-green-800');
                feedback.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Quote sent successfully. Refreshing...';
                setTimeout(() => location.reload(), 1200);
            } else {
                feedback.classList.add('bg-red-100', 'text-red-800');
                feedback.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + data.message;
                btn.disabled = false;
            }
        })
        .catch(() => {
            feedback.classList.remove('hidden');
            feedback.classList.add('bg-red-100', 'text-red-800');
            feedback.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>Connection error. Please try again.';
            btn.disabled = false;
        });
}

function setStatus(newStatus) {
    const feedback = document.getElementById('statusFeedback');

    const formData = new FormData();
    formData.append('id', <?= $request['id'] ?>);
    formData.append('status', newStatus);
    formData.append('csrf_token', '<?= \Core\Security::generateCsrfToken() ?>');

    fetch('/manager/quote-requests/update-status', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            feedback.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
            if (data.success) {
                feedback.classList.add('bg-green-100', 'text-green-800');
                feedback.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Status updated. Refreshing...';
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
