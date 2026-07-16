<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8"><?php echo __('shop.cart.title'); ?></h1>

    <?php if (empty($items)): ?>
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h3 class="mt-2 text-xl font-medium text-gray-900"><?php echo __('shop.cart.empty'); ?></h3>
            <p class="mt-1 text-gray-500"><?php echo __('shop.cart.empty_text'); ?></p>
            <div class="mt-6">
                <a href="/shop" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-cyan-600 hover:bg-cyan-700">
                    <?php echo __('shop.cart.browse'); ?>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <ul role="list" class="divide-y divide-gray-200">
                <?php foreach ($items as $item): ?>
                <li class="flex py-6 px-4 sm:px-6">
                    <div class="flex-shrink-0">
                        <?php if ($item['image']): ?>
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-24 h-24 rounded-md object-center object-cover">
                        <?php else: ?>
                            <div class="w-24 h-24 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-md flex items-center justify-center">
                                <span class="text-white text-3xl">📦</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="ml-4 flex-1 flex flex-col">
                        <div>
                            <div class="flex justify-between text-base font-medium text-gray-900">
                                <h3>
                                    <a href="/shop/<?php echo htmlspecialchars($item['slug']); ?>">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </a>
                                </h3>
                                <p class="ml-4">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                            </div>
                        </div>
                        <div class="flex-1 flex items-end justify-between text-sm">
                            <form action="/cart/update" method="POST" class="flex items-center">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <label for="quantity-<?php echo $item['id']; ?>" class="text-gray-500 mr-2"><?php echo __('shop.cart.qty'); ?>:</label>
                                <input type="number" id="quantity-<?php echo $item['id']; ?>" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" 
                                       class="w-16 px-2 py-1 border border-gray-300 rounded-md"
                                       onchange="this.form.submit()">
                            </form>

                            <form action="/cart/remove" method="POST">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="font-medium text-red-600 hover:text-red-500"><?php echo __('shop.cart.remove'); ?></button>
                            </form>
                        </div>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <div class="flex justify-between text-base font-medium text-gray-900 mb-4">
                <p><?php echo __('shop.cart.subtotal'); ?></p>
                <p class="text-2xl font-bold text-cyan-600">$<?php echo number_format($total, 2); ?></p>
            </div>
            <p class="mt-0.5 text-sm text-gray-500"><?php echo __('shop.cart.shipping_info'); ?></p>
            <div class="mt-6">
                <a href="/checkout" class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-cyan-600 hover:bg-cyan-700 transition duration-300">
                    <?php echo __('shop.cart.checkout'); ?>
                </a>
            </div>
            <div class="mt-6 flex justify-center text-sm text-center text-gray-500">
                <p>
                    <?php echo __('shop.cart.or'); ?>
                    <a href="/shop" class="text-cyan-600 font-medium hover:text-cyan-700 ml-1">
                        <?php echo __('shop.cart.continue'); ?>
                    </a>
                </p>
            </div>
        </div>
    <?php endif; ?>
</div>
