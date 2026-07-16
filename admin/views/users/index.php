<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Users</h1>
            <p class="mt-2 text-sm text-gray-600">Gestión de usuarios registrados en la plataforma.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="/manager/users/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                <i class="fas fa-user-plus mr-2"></i> Crear Usuario
            </a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="rounded-md bg-green-50 p-4 border-l-4 border-green-400">
        <p class="text-sm font-medium text-green-800"><i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($_GET['success']); ?></p>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="rounded-md bg-red-50 p-4 border-l-4 border-red-400">
        <p class="text-sm font-medium text-red-800"><i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($_GET['error']); ?></p>
    </div>
    <?php endif; ?>

    <!-- Search & Filters -->
    <form method="GET" action="/manager/users" class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="sm:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 text-sm"></i>
                </div>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                       placeholder="Buscar por nombre o email..."
                       class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Role filter -->
            <select name="role" class="block w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los roles</option>
                <option value="admin"    <?php echo $roleFilter   === 'admin'    ? 'selected' : ''; ?>>Admin</option>
                <option value="student"  <?php echo $roleFilter   === 'student'  ? 'selected' : ''; ?>>Student</option>
                <option value="customer" <?php echo $roleFilter   === 'customer' ? 'selected' : ''; ?>>Customer</option>
            </select>

            <!-- Status filter -->
            <select name="status" class="block w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Todos los estados</option>
                <option value="active"   <?php echo $statusFilter === 'active'   ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <div class="mt-3 flex items-center gap-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-all">
                <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
            <?php if ($search || $roleFilter || $statusFilter): ?>
            <a href="/manager/users" class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-all">
                <i class="fas fa-times mr-2"></i> Limpiar
            </a>
            <?php endif; ?>
            <span class="text-sm text-gray-500 ml-2">
                <?php echo $totalUsers; ?> usuario<?php echo $totalUsers !== 1 ? 's' : ''; ?> encontrado<?php echo $totalUsers !== 1 ? 's' : ''; ?>
            </span>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500 text-sm">No se encontraron usuarios</p>
                            <?php if ($search || $roleFilter || $statusFilter): ?>
                            <a href="/manager/users" class="text-indigo-600 text-sm hover:underline mt-1 inline-block">Limpiar filtros</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <?php
                        $role   = $user['role']   ?? 'customer';
                        $status = $user['status'] ?? 'active';
                        $roleColors = [
                            'admin'    => 'bg-purple-100 text-purple-700 ring-1 ring-purple-200',
                            'student'  => 'bg-blue-100 text-blue-700 ring-1 ring-blue-200',
                            'editor'   => 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-200',
                            'customer' => 'bg-green-100 text-green-700 ring-1 ring-green-200',
                        ];
                        $roleColor = $roleColors[$role] ?? $roleColors['customer'];
                        $statusColor = $status === 'active'
                            ? 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200'
                            : 'bg-rose-100 text-rose-700 ring-1 ring-rose-200';
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                    <?php echo strtoupper(substr($user['name'] ?? $user['email'], 0, 1)); ?>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $roleColor; ?>">
                                <?php echo ucfirst($role); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusColor; ?>">
                                <span class="h-1.5 w-1.5 rounded-full mr-1.5 <?php echo $status === 'active' ? 'bg-emerald-500' : 'bg-rose-500'; ?>"></span>
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <i class="far fa-calendar-alt mr-2 text-gray-400"></i>
                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="/manager/users/<?php echo $user['id']; ?>/toggle-status"
                               class="inline-flex items-center p-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-all"
                               title="<?php echo $status === 'active' ? 'Desactivar' : 'Activar'; ?>">
                                <i class="fas <?php echo $status === 'active' ? 'fa-user-slash text-amber-500' : 'fa-user-check text-emerald-500'; ?>"></i>
                            </a>
                            <a href="/manager/users/<?php echo $user['id']; ?>/edit"
                               class="inline-flex items-center p-2 border border-blue-300 rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 transition-all"
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="/manager/users/<?php echo $user['id']; ?>/delete" method="POST" class="inline-block"
                                  onsubmit="return confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.');">
                                <?php echo \Core\Security::getCsrfField(); ?>
                                <button type="submit"
                                        class="inline-flex items-center p-2 border border-rose-300 rounded-md text-rose-700 bg-rose-50 hover:bg-rose-100 transition-all"
                                        title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <?php
            // Build base query preserving filters
            $qp = array_filter(['search' => $search, 'role' => $roleFilter, 'status' => $statusFilter]);
            $baseQuery = $qp ? '&' . http_build_query($qp) : '';
            $from = ($currentPage - 1) * 10 + 1;
            $to   = min($currentPage * 10, $totalUsers);
        ?>
        <div class="bg-white px-6 py-4 flex items-center justify-between border-t border-gray-200">
            <p class="text-sm text-gray-700">
                Mostrando <span class="font-medium"><?php echo $from; ?></span>–<span class="font-medium"><?php echo $to; ?></span>
                de <span class="font-medium"><?php echo $totalUsers; ?></span>
            </p>
            <nav class="inline-flex rounded-md shadow-sm -space-x-px">
                <!-- Prev -->
                <?php if ($currentPage > 1): ?>
                <a href="?page=<?php echo $currentPage - 1; ?><?php echo $baseQuery; ?>"
                   class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php else: ?>
                <span class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-200 bg-gray-50 text-sm text-gray-300 cursor-not-allowed">
                    <i class="fas fa-chevron-left"></i>
                </span>
                <?php endif; ?>

                <!-- Page numbers -->
                <?php
                    $start = max(1, $currentPage - 2);
                    $end   = min($totalPages, $currentPage + 2);
                    if ($start > 1): ?>
                        <a href="?page=1<?php echo $baseQuery; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50">1</a>
                        <?php if ($start > 2): ?><span class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm text-gray-400">…</span><?php endif; ?>
                    <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo $baseQuery; ?>"
                       class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?php echo $i === $currentPage ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?><span class="relative inline-flex items-center px-3 py-2 border border-gray-300 bg-white text-sm text-gray-400">…</span><?php endif; ?>
                    <a href="?page=<?php echo $totalPages; ?><?php echo $baseQuery; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50"><?php echo $totalPages; ?></a>
                <?php endif; ?>

                <!-- Next -->
                <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?php echo $currentPage + 1; ?><?php echo $baseQuery; ?>"
                   class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm text-gray-500 hover:bg-gray-50">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php else: ?>
                <span class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-200 bg-gray-50 text-sm text-gray-300 cursor-not-allowed">
                    <i class="fas fa-chevron-right"></i>
                </span>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
