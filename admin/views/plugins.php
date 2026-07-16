<div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Plugins</h1>

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Installed Plugins</h3>
            <p class="mt-1 text-sm text-gray-500">Manage your CMS plugins and modules</p>
        </div>
        <div class="border-t border-gray-200">
            <ul role="list" class="divide-y divide-gray-200">
                <?php if (empty($plugins)): ?>
                <li class="px-6 py-4">
                    <p class="text-gray-500">No plugins loaded</p>
                </li>
                <?php else: ?>
                    <?php foreach ($plugins as $plugin): ?>
                    <li class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-gray-900"><?php echo ucfirst($plugin['name']); ?></h4>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($plugin['path']); ?></p>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <?php if ($plugin['name'] === 'paypal'): ?>
                                        <a href="/manager/paypal-settings" class="inline-flex items-center px-3 py-1 border border-blue-300 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                                            <i class="fas fa-cog mr-1"></i> Configurar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-2">How to create a plugin</h3>
        <div class="text-sm text-blue-700 space-y-2">
            <p>1. Create a new folder in <code class="bg-blue-100 px-2 py-1 rounded">/plugins/your-plugin-name/</code></p>
            <p>2. Create a main file: <code class="bg-blue-100 px-2 py-1 rounded">your-plugin-name.php</code></p>
            <p>3. Add your plugin name to <code class="bg-blue-100 px-2 py-1 rounded">config/config.php</code> in the 'plugins' array</p>
            <p>4. Use hooks: <code class="bg-blue-100 px-2 py-1 rounded">$pm->addAction('hook_name', 'callback')</code></p>
        </div>
    </div>
</div>
