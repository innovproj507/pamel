<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-cyan-50 to-blue-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center mb-8">
                <img src="/assets/images/logo.png" alt="PAMEL Logo" class="h-20 mx-auto mb-4">
                <h2 class="text-3xl font-extrabold text-gray-900"><?php echo __('auth.register.title'); ?></h2>
                <p class="mt-2 text-sm text-gray-600"><?php echo __('auth.register.subtitle'); ?></p>
            </div>

            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['register_error'])): ?>
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <p class="text-red-700 text-sm"><?php echo htmlspecialchars($_SESSION['register_error']); ?></p>
                </div>
                <?php unset($_SESSION['register_error']); ?>
            <?php endif; ?>

            <form method="POST" action="/register" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <?php echo __('auth.register.name'); ?> *
                    </label>
                    <input id="name" 
                           name="name" 
                           type="text" 
                           required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition"
                           placeholder="Juan Pérez">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <?php echo __('auth.register.email'); ?> *
                    </label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           required 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        <?php echo __('form.phone'); ?>
                    </label>
                    <input id="phone" 
                           name="phone" 
                           type="tel" 
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <?php echo __('auth.register.password'); ?> *
                    </label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           minlength="6"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                </div>

                <div>
                    <label for="password_confirm" class="block text-sm font-semibold text-gray-700 mb-2">
                        <?php echo __('auth.register.confirm_password'); ?> *
                    </label>
                    <input id="password_confirm" 
                           name="password_confirm" 
                           type="password" 
                           required 
                           minlength="6"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white px-8 py-4 rounded-lg font-bold uppercase tracking-wider transition transform hover:scale-105 shadow-lg">
                    <?php echo __('auth.register.submit'); ?>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    <?php echo __('auth.register.has_account'); ?> 
                    <a href="/login" class="font-semibold text-cyan-600 hover:text-cyan-500">
                        <?php echo __('auth.register.login'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
