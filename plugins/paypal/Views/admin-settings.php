<div class="container mx-auto px-4 py-8">
    
    <?php if (isset($success)): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-3 text-green-600"></i>
            <span><?php echo $success; ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-exclamation-circle mr-3 text-red-600"></i>
            <span><?php echo $error; ?></span>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fab fa-paypal text-blue-600 mr-3"></i>
            Configuración de PayPal
        </h1>
        <p class="text-gray-600 mt-2">Configura tus credenciales de PayPal para procesar pagos</p>
    </div>

    <!-- Info Alert -->
    <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-info-circle mt-1 mr-3 text-blue-600"></i>
            <div>
                <p class="font-semibold mb-1">Información Importante</p>
                <ul class="text-sm space-y-1 list-disc list-inside">
                    <li><strong>Modo Sandbox:</strong> Para pruebas. Obtén credenciales en <a href="https://developer.paypal.com/" target="_blank" class="underline">PayPal Developer</a></li>
                    <li><strong>Modo Live:</strong> Para producción. Usa credenciales reales de tu cuenta PayPal Business</li>
                    <li>El <strong>Client Secret</strong> se obtiene junto con el Client ID en el dashboard de PayPal</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Configuration Form -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Credenciales de PayPal</h2>
        </div>

        <form method="POST" action="/manager/paypal-settings" class="p-6 space-y-6">
            
            <!-- Mode Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-cog mr-2 text-gray-500"></i>Modo de Operación
                </label>
                <select name="paypal_mode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="sandbox" <?php echo ($settings['paypal_mode'] ?? '') === 'sandbox' ? 'selected' : ''; ?>>
                        Sandbox (Pruebas)
                    </option>
                    <option value="live" <?php echo ($settings['paypal_mode'] ?? '') === 'live' ? 'selected' : ''; ?>>
                        Live (Producción)
                    </option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Usa "Sandbox" para pruebas y "Live" para producción</p>
            </div>

            <!-- Client ID -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-key mr-2 text-gray-500"></i>Client ID
                </label>
                <input type="text" 
                       name="paypal_client_id" 
                       value="<?php echo htmlspecialchars($settings['paypal_client_id'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                       placeholder="AeB1234567890abcdefghijklmnopqrstuvwxyz"
                       required>
                <p class="mt-1 text-xs text-gray-500">Obtén esto en tu dashboard de PayPal Developer</p>
            </div>

            <!-- Client Secret -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-gray-500"></i>Client Secret
                </label>
                <input type="password" 
                       name="paypal_client_secret" 
                       value="<?php echo htmlspecialchars($settings['paypal_client_secret'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                       placeholder="••••••••••••••••••••••••••••••••"
                       required>
                <p class="mt-1 text-xs text-gray-500">Se genera junto con el Client ID. Mantenlo seguro</p>
            </div>

            <!-- Merchant ID -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-building mr-2 text-gray-500"></i>Merchant ID (Opcional)
                </label>
                <input type="text" 
                       name="paypal_merchant_id" 
                       value="<?php echo htmlspecialchars($settings['paypal_merchant_id'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                       placeholder="ABCD1234EFGH">
                <p class="mt-1 text-xs text-gray-500">ID de comerciante de PayPal (opcional)</p>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2 text-gray-500"></i>Email de PayPal
                </label>
                <input type="email" 
                       name="paypal_email" 
                       value="<?php echo htmlspecialchars($settings['paypal_email'] ?? ''); ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="tu-negocio@example.com"
                       required>
                <p class="mt-1 text-xs text-gray-500">Email asociado a tu cuenta de PayPal Business</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="/manager/plugins" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Configuración
                </button>
            </div>

        </form>
    </div>

    <!-- Help Section -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h3 class="font-semibold text-yellow-900 mb-2">
            <i class="fas fa-question-circle mr-2"></i>¿Cómo obtener las credenciales?
        </h3>
        <ol class="text-sm text-yellow-800 space-y-2 list-decimal list-inside">
            <li>Ve a <a href="https://developer.paypal.com/" target="_blank" class="underline font-medium">PayPal Developer</a></li>
            <li>Inicia sesión con tu cuenta de PayPal</li>
            <li>Ve a "My Apps & Credentials"</li>
            <li>En la sección "Sandbox" o "Live", crea una nueva app</li>
            <li>Copia el Client ID y Client Secret que se generan</li>
            <li>Pega las credenciales en este formulario</li>
        </ol>
    </div>

</div>
