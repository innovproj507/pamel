<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-green-500 px-6 py-4">
            <div class="flex items-center">
                <svg class="h-8 w-8 text-white mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <h2 class="text-2xl font-bold text-white"><?php echo __('shop.success.title'); ?></h2>
            </div>
        </div>

        <div class="px-6 py-8">
            <p class="text-gray-700 text-lg mb-4">
                <?php echo __('shop.success.message'); ?>
            </p>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-600"><?php echo __('shop.success.order_number'); ?></p>
                <p class="text-2xl font-bold text-cyan-600">#<?php echo $orderId; ?></p>
            </div>

            <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-cyan-800">
                    <?php echo __('shop.success.email_conf'); ?>
                </p>
            </div>

            <div class="space-y-3">
                <a href="/shop" class="block w-full text-center bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                    <?php echo __('shop.success.continue'); ?>
                </a>
                <a href="/" class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg transition duration-300">
                    <?php echo __('shop.success.home'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
