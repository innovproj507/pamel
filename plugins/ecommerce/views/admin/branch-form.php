<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?php echo $branch ? 'Edit Branch' : 'Add New Branch'; ?></h1>
    </div>

    <form action="<?php echo $branch ? '/manager/branches/' . $branch['id'] . '/update' : '/manager/branches/store'; ?>" method="POST" class="bg-white shadow-lg rounded-lg p-8">

        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Branch Name *</label>
            <input type="text" id="name" name="name" required
                   value="<?php echo $branch ? htmlspecialchars($branch['name']) : ''; ?>"
                   placeholder="e.g. Grecia"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
            <input type="text" id="slug" name="slug"
                   value="<?php echo $branch ? htmlspecialchars($branch['slug']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from name</p>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (English)</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"><?php echo $branch ? htmlspecialchars($branch['description']) : ''; ?></textarea>
        </div>

        <div class="mb-6">
            <label for="description_es" class="block text-sm font-medium text-gray-700 mb-2">Description (Español)</label>
            <textarea id="description_es" name="description_es" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"><?php echo $branch && !empty($branch['description_es']) ? htmlspecialchars($branch['description_es']) : ''; ?></textarea>
            <p class="mt-1 text-sm text-gray-500">Se usa cuando el sitio esta en español. Si se deja vacio, se muestra la descripcion en ingles.</p>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">Icon (FontAwesome)</label>
                <input type="text" id="icon" name="icon"
                       value="<?php echo $branch ? htmlspecialchars($branch['icon']) : 'fa-anchor'; ?>"
                       placeholder="fa-anchor"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <p class="mt-1 text-sm text-gray-500">Example: fa-ship, fa-anchor, fa-globe-americas</p>
            </div>

            <div>
                <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                <input type="number" id="display_order" name="display_order"
                       value="<?php echo $branch ? $branch['display_order'] : '0'; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="/manager/branches" class="text-gray-600 hover:text-gray-900 font-medium">
                ← Back to Branches
            </a>
            <button type="submit" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-lg transition transform hover:scale-105 shadow-lg">
                <?php echo $branch ? 'Update Branch' : 'Create Branch'; ?>
            </button>
        </div>
    </form>
</div>
