<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <div class="h-12 w-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                    <i class="fas fa-user-edit text-xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit User</h1>
                    <p class="mt-1 text-sm text-gray-500">Update user profile and permissions.</p>
                </div>
            </div>
        </div>
        <a href="/manager/users" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-all focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Back to list
        </a>
    </div>

    <?php if (isset($_GET['error'])): ?>
    <div class="mb-6 rounded-lg bg-rose-50 p-4 border-l-4 border-rose-400 shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-rose-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-rose-800"><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="bg-white shadow-xl shadow-gray-200/50 border border-gray-200 rounded-2xl overflow-hidden">
        <form action="/manager/users/<?php echo $user['id']; ?>/update" method="POST" class="p-8 space-y-8">
            <?php
            require_once __DIR__ . '/../../../Core/Security.php';
            echo Core\Security::getCsrfField();
            ?>
            <div class="grid grid-cols-1 gap-y-8 gap-x-6 sm:grid-cols-6">
                <!-- Name -->
                <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-bold text-gray-700 tracking-wide uppercase text-xs mb-2">Full Name</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 border rounded-xl text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"
                            placeholder="John Doe">
                    </div>
                </div>

                <!-- Email -->
                <div class="sm:col-span-6">
                    <label for="email" class="block text-sm font-bold text-gray-700 tracking-wide uppercase text-xs mb-2">Email Address</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 border rounded-xl text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"
                            placeholder="user@example.com">
                    </div>
                </div>

                <!-- Role and Status -->
                <div class="sm:col-span-3">
                    <label for="role" class="block text-sm font-bold text-gray-700 tracking-wide uppercase text-xs mb-2">Role</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <select id="role" name="role" 
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 border rounded-xl text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none transition-all outline-none">
                            <option value="student" <?php echo ($user['role'] ?? 'student') === 'student' ? 'selected' : ''; ?>>Estudiante</option>
                            <option value="teacher" <?php echo ($user['role'] ?? '') === 'teacher' ? 'selected' : ''; ?>>Instructor</option>
                            <option value="editor" <?php echo ($user['role'] ?? '') === 'editor' ? 'selected' : ''; ?>>Editor</option>
                            <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="status" class="block text-sm font-bold text-gray-700 tracking-wide uppercase text-xs mb-2">Account Status</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                            <i class="fas fa-toggle-on"></i>
                        </div>
                        <select id="status" name="status" 
                            class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 border rounded-xl text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none transition-all outline-none">
                            <option value="active" <?php echo ($user['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($user['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="sm:col-span-6">
                    <div class="bg-indigo-50/50 rounded-2xl p-6 border border-indigo-100 mt-2">
                        <label for="password" class="block text-sm font-bold text-indigo-900 tracking-wide uppercase text-xs mb-3">Security — New Password</label>
                        <p class="text-xs text-indigo-600 mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-1.5 text-indigo-400"></i> No rellene este campo si desea mantener la contraseña actual.
                        </p>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-300 group-focus-within:text-indigo-500 transition-colors">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" id="password" placeholder="••••••••"
                                class="block w-full pl-11 pr-4 py-3 bg-white border-indigo-100 border rounded-xl text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <p class="text-xs text-gray-400 italic">
                    <i class="fas fa-history mr-1"></i> ID del Usuario: #<?php echo $user['id']; ?>
                </p>
                <div class="flex items-center space-x-4">
                    <a href="/manager/users" class="px-6 py-3 border border-gray-300 text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 text-sm font-bold rounded-xl text-white hover:from-indigo-700 hover:to-indigo-800 shadow-lg shadow-indigo-100 hover:shadow-indigo-200 transition-all active:scale-95 outline-none">
                        Save Changes <i class="fas fa-save ml-2 text-indigo-200"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
