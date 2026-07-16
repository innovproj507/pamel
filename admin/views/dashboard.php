<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-2">Overview of your CMS and e-commerce system</p>
    </div>

    <?php
    use Core\Database;
    $db = Database::getInstance();
    
    // Get statistics
    $stats = [
        'products' => $db->fetchOne("SELECT COUNT(*) as count FROM products")['count'] ?? 0,
        'active_products' => $db->fetchOne("SELECT COUNT(*) as count FROM products WHERE status = 'active'")['count'] ?? 0,
        'categories' => $db->fetchOne("SELECT COUNT(*) as count FROM categories")['count'] ?? 0,
        'orders' => $db->fetchOne("SELECT COUNT(*) as count FROM orders")['count'] ?? 0,
        'reviews' => $db->fetchOne("SELECT COUNT(*) as count FROM product_reviews")['count'] ?? 0,
        'avg_rating' => $db->fetchOne("SELECT AVG(rating) as avg FROM product_reviews")['avg'] ?? 0,
    ];
    
    // Recent products
    $recentProducts = $db->fetchAll("SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC LIMIT 5");
    
    // Top rated products
    $topRated = $db->fetchAll("SELECT * FROM products WHERE total_reviews > 0 
        ORDER BY avg_rating DESC, total_reviews DESC LIMIT 5");
    ?>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <!-- Total Products -->
        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border-l-4 border-cyan-500 hover:shadow-xl transition">
            <div class="px-6 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-cyan-100 rounded-xl p-4">
                        <i class="fas fa-box text-cyan-600 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                            <dd class="flex items-baseline">
                                <div class="text-3xl font-bold text-gray-900"><?php echo $stats['products']; ?></div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    <?php echo $stats['active_products']; ?> active
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <a href="/manager/products" class="text-sm font-medium text-cyan-600 hover:text-cyan-800 flex items-center">
                    <span>View all</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Categories -->
        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border-l-4 border-purple-500 hover:shadow-xl transition">
            <div class="px-6 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-xl p-4">
                        <i class="fas fa-folder-tree text-purple-600 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Categories</dt>
                            <dd class="flex items-baseline">
                                <div class="text-3xl font-bold text-gray-900"><?php echo $stats['categories']; ?></div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <a href="/manager/categories" class="text-sm font-medium text-purple-600 hover:text-purple-800 flex items-center">
                    <span>Manage</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Orders -->
        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border-l-4 border-green-500 hover:shadow-xl transition">
            <div class="px-6 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-xl p-4">
                        <i class="fas fa-shopping-cart text-green-600 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                            <dd class="flex items-baseline">
                                <div class="text-3xl font-bold text-gray-900"><?php echo $stats['orders']; ?></div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <a href="/manager/orders" class="text-sm font-medium text-green-600 hover:text-green-800 flex items-center">
                    <span>View orders</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Reviews -->
        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border-l-4 border-yellow-500 hover:shadow-xl transition">
            <div class="px-6 py-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-xl p-4">
                        <i class="fas fa-star text-yellow-600 text-3xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Customer Reviews</dt>
                            <dd class="flex items-baseline">
                                <div class="text-3xl font-bold text-gray-900"><?php echo $stats['reviews']; ?></div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-yellow-600">
                                    <i class="fas fa-star text-xs mr-1"></i>
                                    <?php echo number_format($stats['avg_rating'], 1); ?> avg
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3">
                <a href="/manager/reviews" class="text-sm font-medium text-yellow-600 hover:text-yellow-800 flex items-center">
                    <span>Manage reviews</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-cyan-500 to-blue-600 overflow-hidden shadow-lg rounded-2xl hover:shadow-xl transition col-span-1 sm:col-span-2">
            <div class="px-6 py-6 text-white">
                <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="/manager/products/create" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg p-3 transition flex items-center">
                        <i class="fas fa-plus-circle mr-2"></i>
                        <span class="text-sm font-medium">Add Product</span>
                    </a>
                    <a href="/manager/categories/create" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm rounded-lg p-3 transition flex items-center">
                        <i class="fas fa-folder-plus mr-2"></i>
                        <span class="text-sm font-medium">Add Category</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Products -->
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <h2 class="text-xl font-bold text-gray-900">Recent Products</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php if (!empty($recentProducts)): ?>
                        <?php foreach ($recentProducts as $product): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center flex-1">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             class="w-12 h-12 rounded-lg object-cover mr-4">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-300 rounded-lg mr-4 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-500"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                        <div class="text-sm text-gray-500">
                                            <?php if (!empty($product['category_name'])): ?>
                                                <span class="text-cyan-600"><?php echo htmlspecialchars($product['category_name']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($product['imo_model_course_no'])): ?>
                                                <span class="mx-1">•</span>
                                                <span>IMO <?php echo htmlspecialchars($product['imo_model_course_no']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <div class="font-bold text-gray-900">$<?php echo number_format($product['price'], 2); ?></div>
                                    <a href="/manager/products/<?php echo $product['id']; ?>/edit" class="text-sm text-cyan-600 hover:text-cyan-800">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-8">No products yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Rated Products -->
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <h2 class="text-xl font-bold text-gray-900">Top Rated Courses</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php if (!empty($topRated)): ?>
                        <?php foreach ($topRated as $product): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="flex items-center mt-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= floor($product['avg_rating']) ? 'text-yellow-400' : 'text-gray-300'; ?> text-sm"></i>
                                        <?php endfor; ?>
                                        <span class="ml-2 text-sm text-gray-600">
                                            <?php echo number_format($product['avg_rating'], 1); ?> (<?php echo $product['total_reviews']; ?>)
                                        </span>
                                    </div>
                                </div>
                                <a href="/manager/products/<?php echo $product['id']; ?>/edit" class="text-cyan-600 hover:text-cyan-800 ml-4">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-8">No rated products yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
