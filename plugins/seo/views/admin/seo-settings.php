<div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">SEO Settings</h1>

    <!-- Meta Tags Management -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Meta Tags</h2>
        
        <form action="/manager/seo/meta" method="POST" class="space-y-4">
            <div>
                <label for="page_url" class="block text-sm font-medium text-gray-700">Page URL</label>
                <input type="text" id="page_url" name="page_url" required placeholder="/" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                <p class="mt-1 text-sm text-gray-500">Enter the page path (e.g., /, /shop, /shop/product-1)</p>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                <input type="text" id="title" name="title" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                <textarea id="description" name="description" rows="3" 
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
            </div>

            <div>
                <label for="keywords" class="block text-sm font-medium text-gray-700">Keywords</label>
                <input type="text" id="keywords" name="keywords" placeholder="keyword1, keyword2, keyword3" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
            </div>

            <div class="border-t border-gray-200 pt-4 mt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Open Graph Tags</h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="og_title" class="block text-sm font-medium text-gray-700">OG Title</label>
                        <input type="text" id="og_title" name="og_title" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="og_description" class="block text-sm font-medium text-gray-700">OG Description</label>
                        <textarea id="og_description" name="og_description" rows="2" 
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>

                    <div>
                        <label for="og_image" class="block text-sm font-medium text-gray-700">OG Image URL</label>
                        <input type="text" id="og_image" name="og_image" placeholder="https://example.com/image.jpg" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>

            <button type="submit" class="bg-primary hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                Save Meta Tags
            </button>
        </form>

        <!-- Existing Meta Tags -->
        <?php if (!empty($metaTags)): ?>
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Configured Pages</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page URL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($metaTags as $meta): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo htmlspecialchars($meta['page_url']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo htmlspecialchars($meta['title']); ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo htmlspecialchars(substr($meta['description'], 0, 100)); ?>...
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Robots.txt Management -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Robots.txt</h2>
        
        <form action="/manager/seo/robots" method="POST" class="space-y-4">
            <div>
                <label for="robots_txt" class="block text-sm font-medium text-gray-700">Robots.txt Content</label>
                <textarea id="robots_txt" name="robots_txt" rows="10" 
                          class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary font-mono text-sm"><?php echo htmlspecialchars($robotsTxt); ?></textarea>
                <p class="mt-1 text-sm text-gray-500">Configure your robots.txt file. Leave empty to use default.</p>
            </div>

            <button type="submit" class="bg-primary hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                Save Robots.txt
            </button>
        </form>

        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                📄 Your robots.txt is available at: <a href="/robots.txt" target="_blank" class="underline font-medium">/robots.txt</a>
            </p>
        </div>
    </div>

    <!-- Sitemap Link -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Sitemap</h2>
        <p class="text-gray-700 mb-4">
            Your sitemap is automatically generated and includes all published pages and active products.
        </p>
        <a href="/sitemap.xml" target="_blank" class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
            View Sitemap
        </a>
    </div>
</div>
