<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid md:grid-cols-2 gap-12 mb-16">
        <!-- Product Image -->
        <div class="relative">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden sticky top-24">
                <?php
                $imageUrl = !empty($product['image']) ? htmlspecialchars($product['image']) : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="600" height="600"%3E%3Crect fill="%230ea5e9" width="600" height="600"/%3E%3Ctext fill="%23ffffff" font-family="Arial" font-size="36" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3ENo Image Available%3C/text%3E%3C/svg%3E';
                ?>
                <img src="<?php echo $imageUrl; ?>"
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     class="w-full h-auto object-cover">

                <?php if (isset($product['status']) && $product['status'] === 'active'): ?>
                    <div class="absolute top-4 right-4">
                        <span class="bg-green-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                            <i class="fas fa-check-circle mr-1"></i> <?php echo __('shop.available'); ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <!-- Category Badge -->
                <?php if (!empty($product['category_name'])): ?>
                    <div class="absolute top-4 left-4">
                        <?php 
                            $isLatin = stripos($product['category_name'], 'latin') !== false;
                            $badgeColorClass = $isLatin ? 'text-[#D2691E]' : 'text-cyan-700';
                        ?>
                        <span class="bg-white/90 backdrop-blur-sm <?php echo $badgeColorClass; ?> px-4 py-2 rounded-full text-sm font-bold shadow-lg flex items-center">
                            <i class="fas <?php echo $product['category_icon'] ?? 'fa-folder'; ?> mr-2"></i>
                            <?php echo htmlspecialchars($product['category_name']); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 mb-4">
                <?php echo htmlspecialchars($product['name']); ?>
            </h1>

            <!-- Rating Stars -->
            <?php 
            $rating = $product['avg_rating'] ?? 0;
            $totalReviews = $product['total_reviews'] ?? 0;
            ?>
            <div class="flex items-center mb-6">
                <div class="flex items-center mr-3">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= floor($rating)): ?>
                            <i class="fas fa-star text-yellow-400 text-xl"></i>
                        <?php elseif ($i - 0.5 <= $rating): ?>
                            <i class="fas fa-star-half-alt text-yellow-400 text-xl"></i>
                        <?php else: ?>
                            <i class="far fa-star text-gray-300 text-xl"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <span class="text-lg font-semibold text-gray-700">
                    <?php echo number_format($rating, 1); ?>
                </span>
                <?php if ($totalReviews > 0): ?>
                    <span class="ml-2 text-gray-500">
                        (<?php echo $totalReviews; ?> <?php echo $totalReviews == 1 ? __('shop.product.review') : __('shop.product.reviews_count'); ?>)
                    </span>
                <?php endif; ?>
            </div>

            <!-- Course Info Badges -->
            <div class="flex flex-wrap gap-3 mb-6">
                <?php if (!empty($product['modality'])): ?>
                    <?php 
                        $modalityBg = 'bg-indigo-50 border-indigo-200';
                        $modalityText = 'text-indigo-600';
                        $modalityDark = 'text-indigo-800';
                        $modalityIcon = 'fa-desktop';
                        
                        if ($product['modality'] === 'B-learning') {
                            $modalityBg = 'bg-orange-50 border-orange-200';
                            $modalityText = 'text-orange-600';
                            $modalityDark = 'text-orange-800';
                            $modalityIcon = 'fa-laptop';
                        } elseif ($product['modality'] === 'India Exclusive') {
                            $modalityBg = 'bg-amber-50 border-amber-200';
                            $modalityText = 'text-amber-600';
                            $modalityDark = 'text-amber-800';
                            $modalityIcon = 'fa-globe-asia';
                        }
                    ?>
                    <div class="flex items-center <?php echo $modalityBg; ?> border rounded-lg px-4 py-2">
                        <i class="fas <?php echo $modalityIcon; ?> <?php echo $modalityText; ?> text-xl mr-3"></i>
                        <div>
                            <div class="text-xs <?php echo $modalityText; ?> font-medium"><?php echo __('shop.product.modality'); ?></div>
                            <div class="text-sm font-bold <?php echo $modalityDark; ?>"><?php echo htmlspecialchars($product['modality']); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($product['imo_model_course_no'])): ?>
                    <div class="flex items-center bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                        <i class="fas fa-certificate text-blue-600 text-xl mr-3"></i>
                        <div>
                            <div class="text-xs text-blue-600 font-medium"><?php echo __('shop.product.imo_model'); ?></div>
                            <div class="text-sm font-bold text-blue-800"><?php echo htmlspecialchars($product['imo_model_course_no']); ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($product['duration_hours']) && $product['duration_hours'] > 0): ?>
                    <div class="flex items-center bg-purple-50 border border-purple-200 rounded-lg px-4 py-2">
                        <i class="fas fa-clock text-purple-600 text-xl mr-3"></i>
                        <div>
                            <div class="text-xs text-purple-600 font-medium"><?php echo __('shop.product.duration'); ?></div>
                            <div class="text-sm font-bold text-purple-800"><?php echo $product['duration_hours']; ?>h</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Price -->
            <div class="mt-6">
                <p class="text-5xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                    $<?php echo number_format($product['price'] ?? 0, 2); ?>
                </p>
            </div>

            <!-- Description -->
            <div class="mt-8">
                <h3 class="text-xl font-bold text-gray-900 mb-3"><?php echo __('shop.product.description'); ?></h3>
                <div class="text-base text-gray-700 space-y-4 leading-relaxed">
                    <p><?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?></p>
                </div>
            </div>

            <!-- Add to Cart Form -->
            <div class="mt-10">
                <form action="/cart/add" method="POST" class="space-y-4">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="flex items-center space-x-4">
                        <label for="quantity" class="text-sm font-medium text-gray-700"><?php echo __('shop.product.quantity'); ?>:</label>
                        <select id="quantity" name="quantity" class="border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-cyan-500">
                            <?php 
                            $maxStock = isset($product['stock']) ? min(10, $product['stock']) : 10;
                            for ($i = 1; $i <= $maxStock; $i++): 
                            ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        
                        <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-box mr-1"></i><?php echo __('shop.product.spots_available', ['count' => $product['stock']]); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($product['stock']) && $product['stock'] > 0): ?>
                        <button type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-4 px-8 rounded-xl transition transform hover:scale-105 shadow-xl text-lg">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            <?php echo __('shop.product.enroll_now'); ?>
                        </button>
                    <?php else: ?>
                        <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-4 px-8 rounded-xl cursor-not-allowed text-lg">
                            <?php echo __('shop.product.out_of_stock'); ?>
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-8"><?php echo __('shop.product.reviews'); ?></h2>
        
        <?php
        // Fetch reviews for this product
        $reviewModel = new \Plugins\Ecommerce\Models\ProductReview();
        $reviews = $reviewModel->getByProduct($product['id']);
        ?>
        
        <?php if (!empty($reviews)): ?>
            <div class="space-y-6">
                <?php foreach ($reviews as $review): ?>
                    <div class="border-b border-gray-200 pb-6 last:border-0">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <div class="font-bold text-gray-900"><?php echo htmlspecialchars($review['user_name'] ?? 'Anonymous'); ?></div>
                                <div class="flex items-center mt-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?> text-sm"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                            </div>
                        </div>
                        <?php if (!empty($review['comment'])): ?>
                            <p class="text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-center py-8"><?php echo __('shop.product.no_reviews'); ?></p>
        <?php endif; ?>
    </div>

    <!-- Related Products -->
    <?php
    $productModel = new \Plugins\Ecommerce\Models\Product();
    
    // Try to get related products first, fallback to similar by category
    $relatedProducts = $productModel->getRelatedProducts($product['id'], 4);
    if (empty($relatedProducts) && !empty($product['category_id'])) {
        $relatedProducts = $productModel->getSimilarByCategory($product['id'], $product['category_id'], 4);
    }
    ?>
    
    <?php if (!empty($relatedProducts)): ?>
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8"><?php echo __('shop.product.related'); ?></h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($relatedProducts as $related): ?>
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition group">
                        <div class="relative overflow-hidden">
                            <?php
                            $relatedImageUrl = !empty($related['image']) ? htmlspecialchars($related['image']) : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="200"%3E%3Crect fill="%230ea5e9" width="400" height="200"/%3E%3Ctext fill="%23ffffff" font-family="Arial" font-size="18" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3ENo Image%3C/text%3E%3C/svg%3E';
                            ?>
                            <img src="<?php echo $relatedImageUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($related['name']); ?>"
                                 class="w-full h-40 object-cover group-hover:scale-110 transition duration-500">
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">
                                <a href="/shop/<?php echo htmlspecialchars($related['slug']); ?>" class="hover:text-cyan-600">
                                    <?php echo htmlspecialchars($related['name']); ?>
                                </a>
                            </h3>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-cyan-600">
                                    $<?php echo number_format($related['price'] ?? 0, 2); ?>
                                </span>
                                <a href="/shop/<?php echo htmlspecialchars($related['slug']); ?>" class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                                    <?php echo __('shop.product.view'); ?> <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
