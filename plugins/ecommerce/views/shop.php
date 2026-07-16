<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-12">
        <div class="flex flex-col lg:flex-row gap-10">
            
            <!-- Filters Sidebar -->
            <aside class="w-full lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100 sticky top-32">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-xl font-bold text-gray-900"><?php echo __('shop.filters.title'); ?></h2>
                        <a href="/shop" class="text-sm text-cyan-600 hover:text-cyan-700 font-medium transition">
                            <?php echo __('shop.filters.clear'); ?>
                        </a>
                    </div>

                    <form action="/shop" method="GET" class="space-y-8">
                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3"><?php echo __('shop.filters.search'); ?></label>
                            <div class="relative">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" 
                                       placeholder="Course name..." 
                                       class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition text-sm">
                                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3"><?php echo __('shop.filters.category'); ?></label>
                            <select name="category" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition text-sm appearance-none cursor-pointer">
                                <option value=""><?php echo __('shop.filters.all_categories'); ?></option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['slug']; ?>" <?php echo (isset($filters['category']) && $filters['category'] === $cat['slug']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Modality -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3"><?php echo __('shop.filters.modality'); ?></label>
                            <div class="space-y-2">
                                <?php 
                                $modalities = [
                                    'E-learning' => ['icon' => 'fa-desktop', 'color' => 'indigo'],
                                    'B-learning' => ['icon' => 'fa-laptop', 'color' => 'orange'],
                                    'India Exclusive' => ['icon' => 'fa-globe-asia', 'color' => 'amber']
                                ];
                                foreach ($modalities as $value => $info): 
                                    $checked = (isset($filters['modality']) && $filters['modality'] === $value);
                                ?>
                                <label class="flex items-center group cursor-pointer">
                                    <input type="radio" name="modality" value="<?php echo $value; ?>" <?php echo $checked ? 'checked' : ''; ?> 
                                           class="w-5 h-5 text-cyan-600 border-gray-300 focus:ring-cyan-500 rounded-full transition cursor-pointer">
                                    <span class="ml-3 text-sm text-gray-600 group-hover:text-cyan-600 transition flex items-center">
                                        <i class="fas <?php echo $info['icon']; ?> mr-2 text-<?php echo $info['color']; ?>-500 opacity-70"></i>
                                        <?php echo $value; ?>
                                    </span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3"><?php echo __('shop.filters.price_range'); ?></label>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="number" name="price_min" value="<?php echo htmlspecialchars($filters['price_min'] ?? ''); ?>" 
                                       placeholder="Min" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                                <input type="number" name="price_max" value="<?php echo htmlspecialchars($filters['price_max'] ?? ''); ?>" 
                                       placeholder="Max" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-cyan-600 to-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg hover:shadow-cyan-200 transition transform active:scale-95 flex items-center justify-center">
                            <i class="fas fa-filter mr-2"></i> <?php echo __('shop.filters.apply'); ?>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900"><?php echo __('shop.title'); ?></h1>
                        <p class="text-gray-500 mt-1"><?php echo __('shop.showing_results', ['count' => $totalProducts]); ?></p>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                    <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden card-hover group relative flex flex-col h-full border border-gray-100">
                        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-cyan-500 to-blue-600"></div>
                        
                        <!-- Image Container -->
                        <div class="relative overflow-hidden aspect-[4/3]">
                            <?php 
                            $imageUrl = !empty($product['image']) ? htmlspecialchars($product['image']) : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%230ea5e9" width="400" height="300"/%3E%3Ctext fill="%23ffffff" font-family="Arial" font-size="24" x="50%25" y="50%25" text-anchor="middle" dy=".3em"%3ENo Image%3C/text%3E%3C/svg%3E';
                            ?>
                            <img src="<?php echo $imageUrl; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            <!-- Category Badge -->
                            <?php if (!empty($product['category_name'])): ?>
                                <div class="absolute top-5 left-5">
                                    <?php 
                                        $isLatin = stripos($product['category_name'], 'latin') !== false;
                                        $badgeBg = $isLatin ? 'bg-[#D2691E]' : 'bg-cyan-600';
                                    ?>
                                    <span class="<?php echo $badgeBg; ?> text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl flex items-center">
                                        <i class="fas <?php echo $product['category_icon'] ?? 'fa-folder'; ?> mr-2"></i>
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Status Badge -->
                            <?php if ($product['status'] === 'active'): ?>
                                <div class="absolute top-5 right-5">
                                    <span class="bg-emerald-500 text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-xl flex items-center">
                                        <i class="fas fa-check-circle mr-2 text-white"></i> <?php echo __('shop.available'); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-8 flex flex-col flex-1">
                            <!-- Title -->
                            <h3 class="text-xl font-bold text-gray-900 mb-4 group-hover:text-cyan-600 transition leading-tight min-h-[4.5rem] flex items-center">
                                <a href="/shop/<?php echo htmlspecialchars($product['slug']); ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>
                            
                            <!-- Badges Grid -->
                            <div class="flex flex-wrap gap-2 mb-6 min-h-[2.5rem] items-start">
                                <?php if (!empty($product['modality'])): ?>
                                    <?php 
                                        $modColor = 'blue';
                                        $modIcon = 'fa-desktop';
                                        if ($product['modality'] === 'B-learning') { $modColor = 'orange'; $modIcon = 'fa-laptop'; }
                                        elseif ($product['modality'] === 'India Exclusive') { $modColor = 'amber'; $modIcon = 'fa-globe-asia'; }
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider bg-<?php echo $modColor; ?>-50 text-<?php echo $modColor; ?>-700 border border-<?php echo $modColor; ?>-100">
                                        <i class="fas <?php echo $modIcon; ?> mr-2"></i>
                                        <?php echo htmlspecialchars($product['modality']); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($product['duration_hours'])): ?>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-100">
                                        <i class="fas fa-clock mr-2"></i>
                                        <?php echo $product['duration_hours']; ?>.00h
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Rating -->
                            <div class="flex items-center mb-6">
                                <div class="flex text-yellow-400 text-xs">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                </div>
                                <span class="ml-2 text-xs font-bold text-gray-400">0.0</span>
                            </div>
                            
                            <!-- Description -->
                            <p class="text-sm text-gray-500 mb-8 line-clamp-2 min-h-[2.5rem]">
                                <?php echo htmlspecialchars($product['description'] ?? ''); ?>
                            </p>
                            
                            <!-- Price & Action -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200 mt-auto gap-2">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">Price</span>
                                    <p class="text-2xl font-black text-gray-900 leading-none">
                                        $<?php echo number_format($product['price'] ?? 0, 2); ?>
                                    </p>
                                </div>
                                <form action="/cart/add" method="POST" class="flex-shrink-0">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-5 py-3 rounded-xl transition shadow-lg hover:shadow-cyan-200 font-bold text-sm flex items-center whitespace-nowrap">
                                        <i class="fas fa-shopping-cart mr-2"></i> <?php echo __('shop.enroll'); ?>
                                     </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="mt-16 flex justify-center">
                    <nav class="flex items-center gap-2">
                        <?php if ($currentPage > 1): ?>
                            <a href="?<?php echo http_build_query(array_merge($filters, ['page' => $currentPage - 1])); ?>" 
                               class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-100 text-gray-600 hover:bg-cyan-600 hover:text-white transition shadow-sm">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?<?php echo http_build_query(array_merge($filters, ['page' => $i])); ?>" 
                               class="w-12 h-12 flex items-center justify-center rounded-2xl font-bold transition shadow-sm <?php echo $i === $currentPage ? 'bg-cyan-600 text-white' : 'bg-white border border-gray-100 text-gray-600 hover:bg-gray-50'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?<?php echo http_build_query(array_merge($filters, ['page' => $currentPage + 1])); ?>" 
                               class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-100 text-gray-600 hover:bg-cyan-600 hover:text-white transition shadow-sm">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>

                <?php if (empty($products)): ?>
                    <div class="bg-white rounded-3xl shadow-xl p-20 text-center border border-gray-100">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-search text-gray-300 text-4xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2"><?php echo __('shop.no_products_title'); ?></h2>
                        <p class="text-gray-500 max-w-sm mx-auto"><?php echo __('shop.no_products_text'); ?></p>
                        <a href="/shop" class="inline-block mt-8 text-cyan-600 font-bold hover:underline">
                            <?php echo __('shop.clear_all_filters'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
