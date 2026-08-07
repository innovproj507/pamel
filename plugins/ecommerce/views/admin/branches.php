<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Branches</h1>
        <a href="/manager/branches/create" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg transition transform hover:scale-105 shadow-lg">
            <i class="fas fa-plus mr-2"></i>Add Branch
        </a>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <p class="text-green-700"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <p class="text-red-700"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Icon</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Products</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Order</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($branches)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">No branches yet.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($branches as $branch): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <i class="fas <?php echo htmlspecialchars($branch['icon']); ?> text-2xl text-cyan-600"></i>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($branch['name']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($branch['description']); ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <code class="text-sm bg-gray-100 px-2 py-1 rounded"><?php echo htmlspecialchars($branch['slug']); ?></code>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <?php echo $branch['product_count']; ?> courses
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo $branch['display_order']; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="/manager/branches/<?php echo $branch['id']; ?>/edit" class="text-cyan-600 hover:text-cyan-900">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="/manager/branches/<?php echo $branch['id']; ?>/delete" method="POST" class="inline"
                              onsubmit="return confirm('Delete this branch?');">
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
