<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8"><?php echo __('shop.checkout.title'); ?></h1>

    <?php if (!empty($error)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-md px-4 py-3">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!$priceVisible): ?>
        <div class="mb-6 bg-cyan-50 border border-cyan-200 text-cyan-800 rounded-md px-4 py-3">
            <?php echo __('shop.checkout.quote_notice'); ?>
        </div>
    <?php endif; ?>

    <form action="/checkout" method="POST" class="space-y-6">
        <?php echo \Core\Security::getCsrfField(); ?>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4"><?php echo __('shop.checkout.customer_info'); ?></h2>

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700"><?php echo __('shop.checkout.name'); ?></label>
                    <input type="text" id="name" name="name" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700"><?php echo __('shop.checkout.email'); ?></label>
                    <input type="email" id="email" name="email" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700"><?php echo __('shop.checkout.phone'); ?></label>
                    <input type="tel" id="phone" name="phone" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500">
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700"><?php echo __('shop.checkout.address'); ?></label>
                    <textarea id="address" name="address" rows="3" <?php echo $priceVisible ? 'required' : ''; ?>
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500"></textarea>
                </div>

                <?php if (!$priceVisible): ?>
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700"><?php echo __('shop.checkout.message'); ?></label>
                    <textarea id="message" name="message" rows="3"
                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-cyan-500 focus:border-cyan-500"></textarea>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4"><?php echo __('shop.checkout.summary'); ?></h2>

            <ul class="divide-y divide-gray-200">
                <?php foreach ($items as $item): ?>
                <li class="flex py-3">
                    <span class="flex-1 text-gray-900"><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                    <?php if ($priceVisible): ?>
                    <span class="font-medium text-gray-900">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($priceVisible): ?>
            <div class="border-t border-gray-200 mt-4 pt-4">
                <div class="flex justify-between text-lg font-bold text-gray-900">
                    <span><?php echo __('shop.checkout.total'); ?></span>
                    <span class="text-2xl text-cyan-600">$<?php echo number_format($total, 2); ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <button type="submit"
                class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition duration-300 transform hover:scale-105">
            <?php echo $priceVisible ? __('shop.checkout.place_order') : __('shop.checkout.request_quote_cta'); ?>
        </button>
    </form>
</div>
