<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
        <a href="/manager/categories/create" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg transition transform hover:scale-105 shadow-lg">
            <i class="fas fa-plus mr-2"></i>Add Category
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
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Products</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Order</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($categories as $category): ?>
                <tr class="hover:bg-gray-50 transition <?php echo isset($category['is_subcategory']) ? 'bg-blue-50' : ''; ?>">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if (isset($category['is_subcategory'])): ?>
                            <span class="ml-6 text-gray-400">└─</span>
                        <?php endif; ?>
                        <i class="fas <?php echo htmlspecialchars($category['icon']); ?> text-2xl <?php echo isset($category['is_subcategory']) ? 'text-blue-400' : 'text-cyan-600'; ?>"></i>
                    </td>
                    <td class="px-6 py-4">
                        <?php if (isset($category['is_subcategory'])): ?>
                            <div class="ml-8">
                        <?php endif; ?>
                        <div class="text-sm font-bold text-gray-900">
                            <?php echo htmlspecialchars($category['name']); ?>
                            <?php if ($category['has_subcategories']): ?>
                                <span class="ml-2 text-xs bg-cyan-100 text-cyan-800 px-2 py-1 rounded-full">
                                    Has subcategories
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($category['description']); ?></div>
                        <?php if (isset($category['is_subcategory']) && isset($category['parent_name'])): ?>
                            <div class="text-xs text-blue-600 mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>Parent: <?php echo htmlspecialchars($category['parent_name']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($category['is_subcategory'])): ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <code class="text-sm bg-gray-100 px-2 py-1 rounded"><?php echo htmlspecialchars($category['slug']); ?></code>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if (isset($category['is_subcategory'])): ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-level-down-alt mr-1"></i>Subcategory
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-folder mr-1"></i>Main
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <?php echo $category['product_count']; ?> courses
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php echo $category['display_order']; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="/manager/categories/<?php echo $category['id']; ?>/edit" class="text-cyan-600 hover:text-cyan-900">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="/manager/categories/<?php echo $category['id']; ?>/delete" method="POST" class="inline" 
                              onsubmit="return confirm('<?php echo $category['has_subcategories'] ? 'This category has subcategories! Please delete or reassign them first.' : 'Delete this category?'; ?>');">
                            <button type="submit" class="text-red-600 hover:text-red-900 <?php echo $category['has_subcategories'] ? 'opacity-50 cursor-not-allowed' : ''; ?>">
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
