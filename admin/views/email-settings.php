<?php $s = $settings; ?>

<div class="max-w-2xl mx-auto space-y-6">

    <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 flex items-start gap-3">
            <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
            <span><?php echo htmlspecialchars($success, ENT_QUOTES); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 flex items-start gap-3">
            <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
            <span><?php echo htmlspecialchars($error, ENT_QUOTES); ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="/manager/email-settings/save" class="space-y-6">
        <?php echo \Core\Security::getCsrfField(); ?>

        <!-- Remitente y destino -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-blue-500"></i> Remitente y Destino
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del remitente</label>
                    <input type="text" name="from_name"
                           value="<?php echo htmlspecialchars((string)($s['from_name'] ?? '')); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Ej: PAMEL">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email del remitente</label>
                    <input type="email" name="from_email"
                           value="<?php echo htmlspecialchars((string)($s['from_email'] ?? '')); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="correo@dominio.com">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email del administrador <span class="text-gray-400 font-normal">(recibe las notificaciones)</span>
                    </label>
                    <input type="email" name="admin_email"
                           value="<?php echo htmlspecialchars((string)($s['admin_email'] ?? '')); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="admin@dominio.com">
                </div>
            </div>
        </div>

        <!-- SMTP -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-server text-blue-500"></i> Configuración SMTP
                </h2>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <span class="text-sm text-gray-600">Activar SMTP</span>
                    <div class="relative">
                        <input type="checkbox" name="smtp_enabled" id="smtp_enabled"
                               <?php echo !empty($s['smtp_enabled']) ? 'checked' : ''; ?>
                               class="sr-only peer" onchange="toggleSmtp(this.checked)">
                        <div class="w-10 h-6 bg-gray-200 peer-checked:bg-blue-500 rounded-full transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                    </div>
                </label>
            </div>

            <div id="smtp-fields" class="grid grid-cols-1 sm:grid-cols-2 gap-4 <?php echo empty($s['smtp_enabled']) ? 'opacity-50 pointer-events-none' : ''; ?>">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Servidor SMTP</label>
                    <input type="text" name="smtp_host"
                           value="<?php echo htmlspecialchars((string)($s['smtp_host'] ?? 'smtp.gmail.com')); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="smtp.gmail.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Puerto</label>
                    <input type="number" name="smtp_port"
                           value="<?php echo (int)($s['smtp_port'] ?? 587); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="587">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cifrado</label>
                    <select name="smtp_encryption"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="tls" <?php echo ($s['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS (puerto 587)</option>
                        <option value="ssl" <?php echo ($s['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL (puerto 465)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuario SMTP</label>
                    <input type="text" name="smtp_username"
                           value="<?php echo htmlspecialchars((string)($s['smtp_username'] ?? '')); ?>"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="correo@gmail.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña / App Password</label>
                    <div class="relative">
                        <input type="password" name="smtp_password" id="smtp_password"
                               value="<?php echo htmlspecialchars((string)($s['smtp_password'] ?? '')); ?>"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye text-sm" id="pwd-icon"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Para Gmail use una <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-blue-500 hover:underline">App Password</a>.</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition text-sm flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Guardar configuración
            </button>
        </div>
    </form>

    <!-- Botón de prueba separado (GET) -->
    <form method="POST" action="/manager/email-settings/test">
        <?php echo \Core\Security::getCsrfField(); ?>
        <button type="submit"
                class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-6 rounded-lg transition text-sm flex items-center gap-2">
            <i class="fas fa-paper-plane text-blue-500"></i> Enviar correo de prueba
        </button>
    </form>

    <!-- Ayuda -->
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800 space-y-1">
        <p class="font-semibold flex items-center gap-2"><i class="fas fa-info-circle"></i> Configuración por defecto</p>
        <p>Si SMTP está desactivado, el sistema usa la función <code class="bg-blue-100 px-1 rounded">mail()</code> de PHP (requiere que el servidor tenga sendmail configurado).</p>
        <p>Para <strong>Gmail</strong>: active verificación en 2 pasos y genere una <em>App Password</em> en su cuenta de Google.</p>
    </div>
</div>

<script>
function toggleSmtp(enabled) {
    const fields = document.getElementById('smtp-fields');
    fields.classList.toggle('opacity-50', !enabled);
    fields.classList.toggle('pointer-events-none', !enabled);
}

function togglePassword() {
    const input = document.getElementById('smtp_password');
    const icon  = document.getElementById('pwd-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
