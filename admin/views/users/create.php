<div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Crear Nuevo Usuario</h1>
            <p class="mt-2 text-sm text-gray-600">Agregar un nuevo usuario al sistema.</p>
        </div>
    </div>

    <?php if (isset($_GET['error'])): ?>
    <div class="rounded-md bg-red-50 p-4 border-l-4 border-red-400">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <form action="/manager/users/store" method="POST" class="p-6 space-y-6">
            <?php
            require_once __DIR__ . '/../../../Core/Security.php';
            echo Core\Security::getCsrfField();
            ?>
            
            <!-- Información Básica -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="Juan Pérez">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="usuario@ejemplo.com">
                    </div>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Contraseña</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required
                               minlength="6"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="Mínimo 6 caracteres">
                        <p class="mt-1 text-xs text-gray-500">Mínimo 6 caracteres</p>
                    </div>

                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="password_confirm" 
                               id="password_confirm" 
                               required
                               minlength="6"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                               placeholder="Repetir contraseña">
                    </div>
                </div>
            </div>

            <!-- Rol y Estado -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Permisos y Estado</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                            Rol <span class="text-red-500">*</span>
                        </label>
                        <?php $preselectedRole = $_GET['role'] ?? 'student'; ?>
                        <select name="role"
                                id="role"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
<?php
                            $assignable = \Core\Permissions::assignableRoles(\Core\Auth::getInstance()->user()['role'] ?? '');
                            foreach ($assignable as $r):
                            ?>
                            <option value="<?= $r ?>" <?= ($preselectedRole) === $r ? 'selected' : '' ?>><?= htmlspecialchars(\Core\Permissions::label($r)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <strong>Cliente:</strong> Acceso al panel de usuario<br>
                            <strong>Administrador:</strong> Acceso completo al panel admin
                        </p>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Estado <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            Los usuarios inactivos no pueden iniciar sesión
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="<?php echo $preselectedRole === 'teacher' ? '/manager/lms/teachers' : '/manager/users'; ?>"
                   class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm">
                    <i class="fas fa-user-plus mr-2"></i>
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validar que las contraseñas coincidan
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }
});
</script>
