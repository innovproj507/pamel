<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-cyan-50 to-blue-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <img src="/assets/images/logo.png" alt="PAMEL Logo" class="h-20 mx-auto mb-4">
                <h2 class="text-3xl font-extrabold text-gray-900"><?php echo __('auth.login.title'); ?></h2>
                <p class="mt-2 text-sm text-gray-600"><?php echo __('auth.login.subtitle'); ?></p>
            </div>

            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['login_error'])): ?>
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <p class="text-red-700 text-sm"><?php echo htmlspecialchars($_SESSION['login_error']); ?></p>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <form method="POST" action="/login" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <?php echo __('auth.login.email'); ?>
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition"
                           placeholder="tu@email.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <?php echo __('auth.login.password'); ?>
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" 
                               name="remember-me" 
                               type="checkbox" 
                               class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            <?php echo __('auth.login.remember'); ?>
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-cyan-600 hover:text-cyan-500">
                            <?php echo __('auth.login.forgot'); ?>
                        </a>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-8 py-4 rounded-lg font-bold uppercase tracking-wider transition transform hover:scale-105 shadow-lg">
                    <?php echo __('auth.login.submit'); ?>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    <?php echo __('auth.login.no_account'); ?> 
                    <a href="/register" class="font-semibold text-cyan-600 hover:text-cyan-500">
                        <?php echo __('auth.login.register'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
