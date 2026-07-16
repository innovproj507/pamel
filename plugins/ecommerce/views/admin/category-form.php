<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?php echo $category ? 'Edit Category' : 'Add New Category'; ?></h1>
    </div>

    <form action="<?php echo $category ? '/manager/categories/' . $category['id'] . '/update' : '/manager/categories/store'; ?>" method="POST" class="bg-white shadow-lg rounded-lg p-8">
        
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
            <input type="text" id="name" name="name" required 
                   value="<?php echo $category ? htmlspecialchars($category['name']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
            <input type="text" id="slug" name="slug" 
                   value="<?php echo $category ? htmlspecialchars($category['slug']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from name</p>
        </div>

        <div class="mb-6">
            <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">Parent Category</label>
            <select id="parent_id" name="parent_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <option value="">None (Main Category)</option>
                <?php if (isset($parentCategories)): ?>
                    <?php foreach ($parentCategories as $parent): ?>
                        <option value="<?php echo $parent['id']; ?>" 
                                <?php echo ($category && $category['parent_id'] == $parent['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parent['name']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <p class="mt-1 text-sm text-gray-500">Select a parent category to make this a subcategory</p>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"><?php echo $category ? htmlspecialchars($category['description']) : ''; ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">Icon (FontAwesome)</label>
                <input type="text" id="icon" name="icon" 
                       value="<?php echo $category ? htmlspecialchars($category['icon']) : 'fa-folder'; ?>"
                       placeholder="fa-folder"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <p class="mt-1 text-sm text-gray-500">Example: fa-ship, fa-anchor</p>
            </div>

            <div>
                <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                <input type="number" id="display_order" name="display_order" 
                       value="<?php echo $category ? $category['display_order'] : '0'; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="/manager/categories" class="text-gray-600 hover:text-gray-900 font-medium">
                ← Back to Categories
            </a>
            <button type="submit" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-lg transition transform hover:scale-105 shadow-lg">
                <?php echo $category ? 'Update Category' : 'Create Category'; ?>
            </button>
        </div>
    </form>
</div>
