<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?php echo $modality ? 'Edit Modality' : 'Add New Modality'; ?></h1>
    </div>

    <form action="<?php echo $modality ? '/manager/modalities/' . $modality['id'] . '/update' : '/manager/modalities/store'; ?>" method="POST" class="bg-white shadow-lg rounded-lg p-8">

        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Modality Name *</label>
            <input type="text" id="name" name="name" required
                   value="<?php echo $modality ? htmlspecialchars($modality['name']) : ''; ?>"
                   placeholder="e.g. E-learning"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
            <input type="text" id="slug" name="slug"
                   value="<?php echo $modality ? htmlspecialchars($modality['slug']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from name</p>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">Icon (FontAwesome)</label>
                <input type="text" id="icon" name="icon"
                       value="<?php echo $modality ? htmlspecialchars($modality['icon']) : 'fa-desktop'; ?>"
                       placeholder="fa-desktop"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <p class="mt-1 text-sm text-gray-500">Example: fa-desktop, fa-laptop, fa-globe-asia</p>
            </div>

            <div>
                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Badge Color</label>
                <select id="color" name="color"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    <?php
                    $colors = ['cyan', 'indigo', 'orange', 'amber', 'emerald', 'purple', 'blue', 'red'];
                    $selectedColor = $modality ? $modality['color'] : 'indigo';
                    foreach ($colors as $color):
                    ?>
                        <option value="<?php echo $color; ?>" <?php echo $selectedColor === $color ? 'selected' : ''; ?>>
                            <?php echo ucfirst($color); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">Restrict to Branch</label>
            <select id="branch_id" name="branch_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                <option value="">-- Available for all branches --</option>
                <?php foreach ($branches as $branch): ?>
                    <option value="<?php echo $branch['id']; ?>"
                            <?php echo ($modality && $modality['branch_id'] == $branch['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($branch['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="mt-1 text-sm text-gray-500">If set, this modality will only be selectable/shown for courses of that branch (e.g. "India Exclusive" only for the India branch)</p>
        </div>

        <div class="mb-6">
            <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
            <input type="number" id="display_order" name="display_order"
                   value="<?php echo $modality ? $modality['display_order'] : '0'; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
        </div>

        <div class="flex items-center justify-between">
            <a href="/manager/modalities" class="text-gray-600 hover:text-gray-900 font-medium">
                ← Back to Modalities
            </a>
            <button type="submit" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-lg transition transform hover:scale-105 shadow-lg">
                <?php echo $modality ? 'Update Modality' : 'Create Modality'; ?>
            </button>
        </div>
    </form>
</div>
