<!DOCTYPE html>
<html lang="es" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Administración</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full">
    <div class="min-h-full flex">
        <!-- Left Side: Login Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-8 xl:px-12 w-full lg:w-1/3 bg-white">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Header with Logo -->
                <div class="text-center">
                    <img class="h-40 w-auto mx-auto" src="/assets/images/logo.png" alt="Company Logo">
                    <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                        Administración
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Bienvenido de nuevo. Por favor ingrese sus credenciales.
                    </p>
                </div>

                <div class="mt-8">
                    <!-- Form -->
                    <div class="mt-6">
                        <form action="/manager/login" method="POST" class="space-y-6">
                            
                            <?php if (isset($error)): ?>
                            <div class="rounded-md bg-red-50 p-4 border-l-4 border-red-500">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm text-red-700">
                                            <?php echo htmlspecialchars($error); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Correo Electrónico
                                </label>
                                <div class="mt-1">
                                    <input id="email" name="email" type="email" autocomplete="email" required 
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="ejemplo@empresa.com">
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Contraseña
                                </label>
                                <div class="mt-1">
                                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="••••••••">
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    Iniciar Sesión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="mt-8 border-t border-gray-200 pt-6">
                   <p class="text-center text-xs text-gray-400">
                       &copy; <?php echo date('Y'); ?> Sistema de Gestión. Todos los derechos reservados.
                   </p>
                </div>
            </div>
        </div>

        <!-- Right Side: Image -->
        <div class="hidden lg:block relative w-0 flex-1">
            <!-- 
                PLACEHOLDER IMAGE 
                Cambia el 'src' de la imagen de abajo por la URL de tu imagen preferida such.
                Para imagen local usa: src="/assets/images/tu-imagen.jpg"
            -->
            <img class="absolute inset-0 h-full w-full object-cover" 
                 src="/assets/images/bnr-login-admin.png" 
                 alt="Background Image">

        </div>
    </div>
</body>
</html>
