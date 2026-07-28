<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Quote Requests</h1>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Total: <?php echo $totalCount; ?></span>
        </div>
    </div>

    <!-- Status Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <a href="/manager/quote-requests"
               class="status-card <?php echo empty($currentStatus) ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">All Requests</p>
                        <p class="text-2xl font-bold"><?php echo array_sum($statusCounts); ?></p>
                    </div>
                    <i class="fas fa-list text-gray-400 text-2xl"></i>
                </div>
            </a>

            <a href="/manager/quote-requests?status=pending"
               class="status-card <?php echo $currentStatus === 'pending' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600"><?php echo $statusCounts['pending']; ?></p>
                    </div>
                    <i class="fas fa-clock text-yellow-400 text-2xl"></i>
                </div>
            </a>

            <a href="/manager/quote-requests?status=quoted"
               class="status-card <?php echo $currentStatus === 'quoted' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Quoted</p>
                        <p class="text-2xl font-bold text-blue-600"><?php echo $statusCounts['quoted']; ?></p>
                    </div>
                    <i class="fas fa-paper-plane text-blue-400 text-2xl"></i>
                </div>
            </a>

            <a href="/manager/quote-requests?status=completed"
               class="status-card <?php echo $currentStatus === 'completed' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-green-600"><?php echo $statusCounts['completed']; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                </div>
            </a>

            <a href="/manager/quote-requests?status=cancelled"
               class="status-card <?php echo $currentStatus === 'cancelled' ? 'active' : ''; ?>">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Cancelled</p>
                        <p class="text-2xl font-bold text-red-600"><?php echo $statusCounts['cancelled']; ?></p>
                    </div>
                    <i class="fas fa-times-circle text-red-400 text-2xl"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="/manager/quote-requests" class="flex gap-4">
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
                <a href="/manager/quote-requests" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courses</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>No quote requests found.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $request): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #<?php echo $request['id']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($request['customer_name']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo htmlspecialchars($request['email']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs truncate">
                                        <?php echo count($request['items']); ?> course(s):
                                        <?php echo htmlspecialchars(implode(', ', array_map(fn($i) => $i['product_name'], $request['items']))); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge status-<?php echo $request['status']; ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('Y-m-d H:i', strtotime($request['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="/manager/quote-requests/<?php echo $request['id']; ?>"
                                       class="text-cyan-600 hover:text-cyan-900 mr-3">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <?php if ($request['status'] === 'pending'): ?>
                                        <button onclick="sendQuote(<?php echo $request['id']; ?>, this)"
                                                class="text-green-600 hover:text-green-900 font-medium">
                                            <i class="fas fa-paper-plane"></i> Send Quote
                                        </button>
                                    <?php endif; ?>
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

<style>
.status-card {
    @apply bg-white rounded-lg p-4 border-2 border-gray-200 hover:border-cyan-500 transition cursor-pointer;
}
.status-card.active {
    @apply border-cyan-500 bg-cyan-50;
}
.status-badge {
    @apply px-3 inline-flex text-xs leading-5 font-semibold rounded-full;
}
.status-pending   { @apply bg-yellow-100 text-yellow-800; }
.status-quoted    { @apply bg-blue-100 text-blue-800; }
.status-completed { @apply bg-green-100 text-green-800; }
.status-cancelled { @apply bg-red-100 text-red-800; }
</style>

<script>
function sendQuote(id, btn) {
    if (!confirm('Send this quote to the customer with the current course price(s)?')) return;

    btn.disabled = true;
    const formData = new FormData();
    formData.append('csrf_token', '<?= \Core\Security::generateCsrfToken() ?>');

    fetch(`/manager/quote-requests/${id}/send-quote`, { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert('Error: ' + data.message); btn.disabled = false; }
        })
        .catch(() => { alert('Connection error. Please try again.'); btn.disabled = false; });
}
</script>
