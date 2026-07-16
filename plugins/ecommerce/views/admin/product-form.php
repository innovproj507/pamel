<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900"><?php echo $product ? 'Edit Product' : 'Add New Product'; ?></h1>
    </div>

    <form action="<?php echo $product ? '/manager/products/' . $product['id'] . '/update' : '/manager/products/store'; ?>" method="POST" enctype="multipart/form-data" class="bg-white shadow-lg rounded-lg p-8">
        
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
            <input type="text" id="name" name="name" required 
                   value="<?php echo $product ? htmlspecialchars($product['name']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
        </div>

        <div class="mb-6">
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
            <input type="text" id="slug" name="slug" 
                   value="<?php echo $product ? htmlspecialchars($product['slug']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from course name</p>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea id="description" name="description" rows="5"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo $product ? htmlspecialchars($product['description']) : ''; ?></textarea>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Fees (Price) *</label>
                <input type="number" step="0.01" id="price" name="price" required 
                       value="<?php echo $product ? $product['price'] : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="renewal_price" class="block text-sm font-medium text-gray-700 mb-2">Renewal Price</label>
                <input type="number" step="0.01" id="renewal_price" name="renewal_price" 
                       value="<?php echo $product ? $product['renewal_price'] : '0'; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>

            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
                <input type="number" id="stock" name="stock" required 
                       value="<?php echo $product ? $product['stock'] : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select id="category_id" name="category_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        onchange="updateSubcategories()">
                    <option value="">-- Select Category (Optional) --</option>
                    <?php
                    $categoryModel = new \Plugins\Ecommerce\Models\Category();
                    $allCategories = $categoryModel->getAllWithHierarchy();
                    foreach ($allCategories as $category): 
                        // Only show parent categories
                        if (!isset($category['is_subcategory'])):
                    ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($product && $product['category_id'] == $category['id']) ? 'selected' : ''; ?>
                                data-name="<?php echo strtolower($category['name']); ?>"
                                data-has-subs="<?php echo (isset($category['has_subcategories']) && $category['has_subcategories']) ? '1' : '0'; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </select>
                <p class="mt-1 text-sm text-gray-500">Select main category</p>
            </div>

            <div>
                <label for="subcategory_id" class="block text-sm font-medium text-gray-700 mb-2">Subcategory</label>
                <select id="subcategory_id" name="subcategory_id" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">-- Select Subcategory (Optional) --</option>
                    <?php
                    // Show all subcategories with parent info
                    foreach ($allCategories as $category): 
                        if (isset($category['is_subcategory'])):
                    ?>
                        <option value="<?php echo $category['id']; ?>" 
                                data-parent="<?php echo $category['parent_id'] ?? ''; ?>"
                                <?php echo ($product && isset($product['subcategory_id']) && $product['subcategory_id'] == $category['id']) ? 'selected' : ''; ?>
                                style="display:none;">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </select>
                <p class="mt-1 text-sm text-gray-500">Select subcategory if applicable</p>
            </div>
        </div>

        <div class="mb-6">
            <label for="modality" class="block text-sm font-medium text-gray-700 mb-2">Modality *</label>
            <select id="modality" name="modality" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="">-- Select Modality --</option>
                <option value="B-learning" <?php echo (isset($product['modality']) && $product['modality'] === 'B-learning') ? 'selected' : ''; ?>>B-learning</option>
                <option value="E-learning" <?php echo (isset($product['modality']) && $product['modality'] === 'E-learning') ? 'selected' : ''; ?>>E-learning</option>
                <option value="India Exclusive" id="opt-india" <?php echo (isset($product['modality']) && $product['modality'] === 'India Exclusive') ? 'selected' : ''; ?>>India Exclusive</option>
            </select>
            <p class="mt-1 text-sm text-gray-500">Select the course delivery modality</p>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="imo_model_course_no" class="block text-sm font-medium text-gray-700 mb-2">IMO Model Course No.</label>
                <input type="text" id="imo_model_course_no" name="imo_model_course_no" 
                       value="<?php echo $product ? htmlspecialchars($product['imo_model_course_no']) : ''; ?>"
                       placeholder="e.g., 1.02"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="mt-1 text-sm text-gray-500">Example: 1.02, 1.13, 1.21</p>
            </div>

            <div>
                <label for="course_code" class="block text-sm font-medium text-gray-700 mb-2">Course Code</label>
                <input type="text" id="course_code" name="course_code" 
                       value="<?php echo $product ? htmlspecialchars($product['course_code']) : ''; ?>"
                       placeholder="e.g., PA-MA-PSSR-01"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="mt-1 text-sm text-gray-500">Manual course code</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-2">Timetable (Hours)</label>
                <input type="number" id="duration_hours" name="duration_hours" min="0" step="0.5"
                       value="<?php echo $product ? $product['duration_hours'] : '0'; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <p class="mt-1 text-sm text-gray-500">Supports decimals (e.g., 17.5)</p>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
            
            <?php if ($product && !empty($product['image'])): ?>
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="Current product image" 
                         class="w-48 h-48 object-cover rounded-lg border-2 border-gray-200">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                </div>
            <?php endif; ?>

            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-cyan-500 transition">
                <input type="file" id="image" name="image" accept="image/*"
                       class="hidden" onchange="previewImage(event)">
                <label for="image" class="cursor-pointer">
                    <div id="imagePreview" class="mb-4">
                        <i class="fas fa-cloud-upload-alt text-5xl text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 5MB</p>
                </label>
            </div>
            
            <p class="mt-2 text-sm text-gray-500">Or enter image URL:</p>
            <input type="text" name="image_url" placeholder="https://example.com/image.jpg"
                   value="<?php echo ($product && !empty($product['image'])) ? htmlspecialchars($product['image']) : ''; ?>"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent mt-2">
        </div>

        <div class="mb-6">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select id="status" name="status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                <option value="active" <?php echo (!$product || $product['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($product && $product['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <div class="flex items-center justify-between">
            <a href="/manager/products" class="text-gray-600 hover:text-gray-900 font-medium">
                ← Back to Products
            </a>
            <button type="submit" class="bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-3 px-8 rounded-lg transition transform hover:scale-105 shadow-lg">
                <?php echo $product ? 'Update Product' : 'Create Product'; ?>
            </button>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" class="max-w-xs max-h-48 mx-auto rounded-lg border-2 border-cyan-500">';
        }
        reader.readAsDataURL(file);
    }
}

function updateSubcategories() {
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    const selectedCategoryId = categorySelect.value;
    
    console.log('Category changed to:', selectedCategoryId);
    
    // Get currently selected subcategory before hiding
    const currentSubcategoryValue = subcategorySelect.value;
    console.log('Current subcategory value:', currentSubcategoryValue);
    
    // Hide all subcategory options first
    const allSubOptions = subcategorySelect.querySelectorAll('option[data-parent]');
    allSubOptions.forEach(opt => {
        opt.style.display = 'none';
        opt.disabled = true; // Disable hidden options
    });
    
    // Reset subcategory selection only if it doesn't belong to new parent
    let shouldReset = true;
    
    if (selectedCategoryId) {
        // Show only subcategories that belong to selected parent
        const matchingSubOptions = subcategorySelect.querySelectorAll(`option[data-parent="${selectedCategoryId}"]`);
        matchingSubOptions.forEach(opt => {
            opt.style.display = 'block';
            opt.disabled = false; // Enable visible options
            
            // Check if current selection is still valid
            if (opt.value === currentSubcategoryValue) {
                shouldReset = false;
            }
        });
        
        console.log('Showing', matchingSubOptions.length, 'subcategories for category', selectedCategoryId);
    }
    
    // Only reset if current selection is invalid
    if (shouldReset) {
        subcategorySelect.value = '';
        console.log('Reset subcategory selection');
    }

    // Toggle India Exclusive modality
    const modalitySelect = document.getElementById('modality');
    const indiaOpt = document.getElementById('opt-india');
    const selectedCatOpt = categorySelect.options[categorySelect.selectedIndex];
    const catName = selectedCatOpt ? (selectedCatOpt.getAttribute('data-name') || '') : '';
    
    if (catName.includes('latin')) {
        indiaOpt.style.display = 'block';
        indiaOpt.disabled = false;
    } else {
        indiaOpt.style.display = 'none';
        indiaOpt.disabled = true;
        if (modalitySelect.value === 'India Exclusive') {
            modalitySelect.value = '';
        }
    }
}

// Slug generation logic
document.getElementById('name').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    // Only auto-generate if we are creating a new product or if slug is empty
    const isNew = <?php echo $product ? 'false' : 'true'; ?>;
    
    if (isNew) {
        let name = this.value;
        let slug = name.toLowerCase().trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing subcategories');
    updateSubcategories();
    
    // Debug: Log form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const subcategoryValue = document.getElementById('subcategory_id').value;
            const modalityValue = document.getElementById('modality').value;
            console.log('Form submitting with:');
            console.log('- subcategory_id:', subcategoryValue);
            console.log('- modality:', modalityValue);
        });
    }
});
</script>
