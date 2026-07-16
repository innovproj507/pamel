<?php
// Session is handled by the Controller.
// Ensure $user, $orders, $courses, etc are available from the Controller view context.

$currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
$username = $_SESSION['username'] ?? 'Usuario';
$userEmail = $user['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Cuenta - E-Shop Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .sidebar-link.active {
            background-color: #f3f4f6;
            border-left: 4px solid #3b82f6;
            font-weight: 600;
            color: #111827;
        }
        .sidebar-link:hover { background-color: #f9fafb; }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .payment-card { transition: all 0.3s ease; }
        .payment-card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .order-status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
        }
        .tab-button.active {
            background-color: #3b82f6;
            color: white;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md hidden md:flex md:flex-col">
            <div class="p-6 border-b">
                <h1 class="text-xl font-bold text-blue-600 flex items-center">
                    <i class="fas fa-shopping-bag mr-2"></i> E-Shop Pro
                </h1>
                <p class="text-sm text-gray-500 mt-1">Panel de Cliente</p>
            </div>
            
            <div class="p-4 flex-1">
                <h2 class="text-xs uppercase text-gray-500 font-semibold tracking-wider mb-4">Menú Principal</h2>
                <nav class="space-y-1">
                    <a href="/my-account?tab=dashboard" class="sidebar-link flex items-center p-3 rounded-lg text-gray-700 <?php echo $currentTab === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-desktop mr-3 text-gray-500"></i> <span>Escritorio</span>
                    </a>
                    <a href="/my-account?tab=orders" class="sidebar-link flex items-center p-3 rounded-lg text-gray-700 <?php echo $currentTab === 'orders' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart mr-3 text-gray-500"></i> <span>Pedidos</span>
                    </a>
                    <a href="/my-account?tab=downloads" class="sidebar-link flex items-center p-3 rounded-lg text-gray-700 <?php echo $currentTab === 'downloads' ? 'active' : ''; ?>">
                        <i class="fas fa-download mr-3 text-gray-500"></i> <span>Descargas</span>
                    </a>
                    <a href="/my-account?tab=addresses" class="sidebar-link flex items-center p-3 rounded-lg text-gray-700 <?php echo $currentTab === 'addresses' ? 'active' : ''; ?>">
                        <i class="fas fa-address-card mr-3 text-gray-500"></i> <span>Dirección</span>
                    </a>
                   <a href="#" class="sidebar-link flex items-center p-3 rounded-lg text-gray-700 <?php echo $currentTab === 'payment-methods' ? 'active' : ''; ?>" onclick="window.location.href='/my-account?tab=payment-methods'">
                        <i class="fas fa-credit-card mr-3 text-gray-500"></i> <span>Métodos de pago</span>
                    </a>
                </nav>
                
                <h2 class="text-xs uppercase text-gray-500 font-semibold tracking-wider mt-8 mb-4">Cuenta</h2>
                <nav class="space-y-1">
                     <a href="/my-account?tab=account-details" class="sidebar-link flex items-center p-3 rounded-lg text-gray-700 <?php echo $currentTab === 'account-details' ? 'active' : ''; ?>">
                        <i class="fas fa-user-circle mr-3 text-gray-500"></i> <span>Detalles de la cuenta</span>
                    </a>
                    <a href="/my-account?tab=my-courses" class="sidebar-link flex items-center p-3 rounded-lg text-gray-700 <?php echo $currentTab === 'my-courses' ? 'active' : ''; ?>">
                        <i class="fas fa-graduation-cap mr-3 text-gray-500"></i> <span>Mis Cursos</span>
                    </a>
                </nav>
                
                <div class="mt-auto pt-8">
                    <a href="/logout" class="sidebar-link flex items-center p-3 rounded-lg text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt mr-3"></i> <span>Salir</span>
                    </a>
                </div>
            </div>
            
            <div class="p-4 border-t">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium"><?php echo htmlspecialchars($username); ?></p>
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($userEmail); ?></p>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm py-4 px-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <button class="md:hidden mr-4 text-gray-600"><i class="fas fa-bars text-xl"></i></button>
                        <h2 class="text-lg font-semibold">
                            <?php 
                                $titles = [
                                    'dashboard' => 'Escritorio',
                                    'orders' => 'Pedidos',
                                    'downloads' => 'Descargas',
                                    'addresses' => 'Dirección',
                                    'payment-methods' => 'Métodos de pago',
                                    'account-details' => 'Detalles de la cuenta',
                                    'my-courses' => 'Mis Cursos'
                                ];
                                echo $titles[$currentTab] ?? 'Panel de Cliente';
                            ?>
                        </h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-500 hover:text-gray-700"><i class="fas fa-bell text-xl"></i></button>
                    </div>
                </div>
            </header>
            
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                
                <?php if ($currentTab === 'dashboard'): ?>
                    <?php
                        // Calculate Dashboard Stats
                        $pendingCount = count(array_filter($orders ?? [], fn($o) => in_array(strtolower($o['status']), ['pending', 'processing', 'on-hold'])));
                        $coursesCount = count($courses ?? []);
                        $walletBalance = "0.00"; // Placeholder for future wallet feature
                        $recentOrders = array_slice($orders ?? [], 0, 3);
                    ?>
                    
                    <!-- DIAGNOSTIC BLOCK (REMOVE AFTER FIXING) -->
                    <div class="bg-yellow-100 p-4 rounded-lg mb-6 text-sm text-yellow-800 border border-yellow-200">
                        <p><strong>Diagnóstico:</strong></p>
                        <ul class="list-disc ml-4 mt-1">
                            <li>Email del usuario: <strong><?php echo htmlspecialchars($userEmail); ?></strong></li>
                            <li>Total Pedidos Encontrados: <strong><?php echo count($orders ?? []); ?></strong></li>
                            <li>Estados: <?php 
                                if (!empty($orders)) {
                                    echo implode(', ', array_map(function($o) { return $o['status'] . ' (' . $o['source'] . ')'; }, $orders));
                                } else {
                                    echo "Ninguno";
                                }
                            ?></li>
                        </ul>
                    </div>
                    <!-- END DIAGNOSTIC -->
                    
                    <!-- Resumen rápido -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-xl shadow-sm border card-hover transition transform hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Pedidos Pendientes</p>
                                    <p class="text-2xl font-bold mt-1"><?php echo $pendingCount; ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
                                </div>
                            </div>
                            <a href="/my-account?tab=orders" class="text-blue-600 text-sm font-medium mt-4 inline-block hover:underline">Ver todos →</a>
                        </div>
                        
                        <div class="bg-white p-6 rounded-xl shadow-sm border card-hover transition transform hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Saldo Disponible</p>
                                    <p class="text-2xl font-bold mt-1">$<?php echo $walletBalance; ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-wallet text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <a href="#" class="text-blue-600 text-sm font-medium mt-4 inline-block hover:underline opacity-50 cursor-not-allowed" title="Próximamente">Agregar fondos →</a>
                        </div>
                        
                        <div class="bg-white p-6 rounded-xl shadow-sm border card-hover transition transform hover:-translate-y-1 hover:shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-500 text-sm">Cursos en Progreso</p>
                                    <p class="text-2xl font-bold mt-1"><?php echo $coursesCount; ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                                </div>
                            </div>
                            <a href="/my-account?tab=my-courses" class="text-blue-600 text-sm font-medium mt-4 inline-block hover:underline">Continuar aprendiendo →</a>
                        </div>
                    </div>
                    
                    <!-- Pedidos recientes -->
                    <div class="bg-white rounded-xl shadow-sm border mb-8">
                        <div class="p-6 border-b flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold">Pedidos Recientes</h3>
                                <p class="text-gray-500 text-sm">Tus últimos pedidos en la plataforma</p>
                            </div>
                            <a href="/my-account?tab=orders" class="text-blue-600 text-sm font-medium hover:underline">Ver todos</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número de pedido</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 text-sm">
                                    <?php if (!empty($recentOrders)): ?>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="py-4 px-6 font-medium text-blue-600">#ORD-<?php echo $order['id']; ?></td>
                                                <td class="py-4 px-6 text-gray-600"><?php echo date('d M, Y', strtotime($order['date'] ?? 'now')); ?></td>
                                                <td class="py-4 px-6">
                                                    <?php 
                                                    $statusLabels = [
                                                        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Entregado'],
                                                        'processing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'En proceso'],
                                                        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Pendiente'],
                                                        'on-hold' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'En espera'],
                                                        'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Cancelado'],
                                                    ];
                                                    $st = $statusLabels[strtolower($order['status'])] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => ucfirst($order['status'])];
                                                    ?>
                                                    <span class="px-3 py-1 text-xs font-semibold rounded-full <?php echo $st['bg'] . ' ' . $st['text']; ?>">
                                                        <?php echo $st['label']; ?>
                                                    </span>
                                                </td>
                                                <td class="py-4 px-6 font-medium text-gray-900"><?php echo $order['currency'] ?? '$'; ?><?php echo number_format($order['total'], 2); ?></td>
                                                <td class="py-4 px-6">
                                                    <a href="/my-account?tab=orders&order_id=<?php echo $order['id']; ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Ver detalle</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="py-6 text-center text-gray-500">No hay pedidos recientes.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Cursos recomendados -->
                    <div class="bg-white rounded-xl shadow-sm border mb-8">
                        <div class="p-6 border-b flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold">Cursos Recomendados</h3>
                                <p class="text-gray-500 text-sm">Contenido educativo para mejorar tus habilidades</p>
                            </div>
                            <a href="/shop" class="text-blue-600 text-sm font-medium hover:underline">Ver catálogo</a>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <?php if (!empty($recommended_courses)): ?>
                                <?php foreach ($recommended_courses as $index => $course): ?>
                                    <?php 
                                        $gradients = [
                                            'from-blue-500 to-blue-700',
                                            'from-purple-500 to-purple-700',
                                            'from-green-500 to-green-700',
                                            'from-orange-500 to-red-600',
                                            'from-teal-500 to-cyan-600'
                                        ];
                                        $icons = ['fa-chart-line', 'fa-camera', 'fa-chart-pie', 'fa-ship', 'fa-anchor'];
                                        $bg = $gradients[$index % count($gradients)];
                                        $icon = $icons[$index % count($icons)];
                                        $price = $course['price'] ? '$' . $course['price'] : 'Ver precio';
                                    ?>
                                    <div class="border rounded-lg overflow-hidden group hover:shadow-lg transition cursor-pointer flex flex-col h-full transform hover:-translate-y-1">
                                        <div class="h-40 bg-gradient-to-r <?php echo $bg; ?> flex items-center justify-center relative overflow-hidden">
                                            <div class="absolute inset-0 bg-white opacity-10" style="background-image: radial-gradient(circle, #fff 10%, transparent 10%); background-size: 10px 10px;"></div>
                                            <i class="fas <?php echo $icon; ?> text-white text-4xl transform group-hover:scale-110 transition z-10"></i>
                                            <?php if(isset($course['regular_price']) && $course['regular_price'] > $course['price']): ?>
                                                <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-sm animate-pulse">Oferta</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="p-5 flex-1 flex flex-col">
                                            <h4 class="font-bold text-gray-800 line-clamp-2 h-12 mb-2"><?php echo htmlspecialchars($course['post_title']); ?></h4>
                                            
                                            <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                                <span class="text-lg font-bold text-blue-600"><?php echo $price; ?></span>
                                                <a href="/product?id=<?php echo $course['ID']; ?>" class="px-4 py-2 bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition text-sm font-medium">Ver Curso</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-span-3 text-center py-8 text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                    <p>No hay recomendaciones disponibles en este momento.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($currentTab === 'orders' && isset($viewing_order)): ?>
                    <!-- Order Details View -->
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <div class="flex justify-between items-center mb-6 border-b pb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Pedido #<?php echo $viewing_order['id']; ?></h3>
                                <p class="text-sm text-gray-500">Realizado el <?php echo date('d M, Y', strtotime($viewing_order['date'])); ?></p>
                            </div>
                            <span class="order-status-badge bg-blue-100 text-blue-800 uppercase"><?php echo htmlspecialchars($viewing_order['status']); ?></span>
                        </div>
                        
                        <div class="mb-8">
                            <h4 class="font-semibold mb-4 text-gray-800">Productos</h4>
                            <div class="space-y-4">
                                <?php foreach ($viewing_order['items'] as $item): ?>
                                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3 text-gray-400">
                                                <i class="fas fa-box"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($item['name'] ?? 'Producto'); ?></p>
                                                <p class="text-xs text-gray-500">Cantidad: <?php echo $item['quantity'] ?? 1; ?></p>
                                            </div>
                                        </div>
                                        <p class="font-semibold text-gray-900">
                                            <?php echo htmlspecialchars($viewing_order['currency'] ?? '$'); ?> 
                                            <?php echo number_format($item['total'] ?? 0, 2); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="flex justify-between items-center mt-4 pt-4 border-t">
                                <span class="text-lg font-bold">Total</span>
                                <span class="text-2xl font-bold text-blue-600">
                                    <?php echo htmlspecialchars($viewing_order['currency'] ?? '$'); ?> 
                                    <?php echo number_format($viewing_order['total'] ?? 0, 2); ?>
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="/my-account?tab=orders" class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-gray-600">Volver</a>
                            <?php if (in_array(strtolower($viewing_order['status']), ['pending', 'failed', 'on-hold'])): ?>
                                <button onclick="openPaymentModal('<?php echo $viewing_order['id']; ?>', '<?php echo number_format($viewing_order['total'] ?? 0, 2); ?>')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                    Pagar Ahora
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($currentTab === 'orders'): ?>
                    <!-- Orders List Redesign -->
                    <div class="space-y-6">
                        <!-- Filters and Actions Header -->
                        <div class="flex flex-col md:flex-row justify-between items-center bg-white p-4 rounded-xl shadow-sm border gap-4">
                            <div class="flex space-x-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                                <?php $currentFilter = $_GET['status'] ?? 'all'; ?>
                                <a href="/my-account?tab=orders" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $currentFilter === 'all' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">Todos</a>
                                <a href="/my-account?tab=orders&status=pending" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $currentFilter === 'pending' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">Pendientes</a>
                                <a href="/my-account?tab=orders&status=processing" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $currentFilter === 'processing' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">En proceso</a>
                                <a href="/my-account?tab=orders&status=completed" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $currentFilter === 'completed' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">Entregados</a>
                                <a href="/my-account?tab=orders&status=cancelled" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $currentFilter === 'cancelled' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">Cancelados</a>
                            </div>
                            <div class="flex w-full md:w-auto space-x-3">
                                <div class="relative w-full md:w-64">
                                    <input type="text" placeholder="Buscar pedido..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                                </div>
                                <a href="/shop" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium whitespace-nowrap hidden md:block">
                                    <i class="fas fa-plus mr-2"></i> Nuevo Pedido
                                </a>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                            <div class="p-6 border-b">
                                <h3 class="text-lg font-bold text-gray-800">Todos los Pedidos</h3>
                                <p class="text-xs text-gray-500">Historial completo de tus pedidos</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b">
                                        <tr>
                                            <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Pedido</th>
                                            <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha</th>
                                            <th class="py-4 px-6 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Productos</th>
                                            <th class="py-4 px-6 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                                            <th class="py-4 px-6 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                                            <th class="py-4 px-6 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 text-sm">
                                        <?php if (!empty($orders)): ?>
                                            <?php foreach ($orders as $order): ?>
                                                <tr class="hover:bg-gray-50 transition group">
                                                    <td class="py-4 px-6 font-bold text-gray-900 border-l-4 border-transparent hover:border-blue-600">
                                                        #ORD-<?php echo $order['id']; ?>
                                                    </td>
                                                    <td class="py-4 px-6 text-gray-600">
                                                        <?php echo date('d M, Y', strtotime($order['date'] ?? 'now')); ?>
                                                    </td>
                                                    <td class="py-4 px-6">
                                                        <div class="flex items-center">
                                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 mr-3 flex-shrink-0">
                                                                <i class="fas fa-box"></i>
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-gray-900 line-clamp-1">
                                                                    <?php echo htmlspecialchars($order['first_item'] ?? 'Producto'); ?>
                                                                </p>
                                                                <?php if (($order['item_count'] ?? 1) > 1): ?>
                                                                    <p class="text-xs text-gray-500">+ <?php echo ($order['item_count'] - 1); ?> más</p>
                                                                <?php else: ?>
                                                                    <p class="text-xs text-gray-500">Cantidad: 1</p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="py-4 px-6 text-center">
                                                        <?php 
                                                        $status = strtolower($order['status']);
                                                        $statusStyles = [
                                                            'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'label' => 'Entregado'],
                                                            'processing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'En proceso'],
                                                            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'label' => 'Pendiente'],
                                                            'on-hold' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'En espera'],
                                                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Cancelado'],
                                                            'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Fallido'],
                                                        ];
                                                        $style = $statusStyles[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => ucfirst($status)];
                                                        ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $style['bg'] . ' ' . $style['text']; ?>">
                                                            <?php echo $style['label']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="py-4 px-6 text-right font-bold text-gray-900">
                                                        <?php echo $order['currency'] ?? '$'; ?> <?php echo number_format($order['total'], 2); ?>
                                                    </td>
                                                    <td class="py-4 px-6 text-center">
                                                        <div class="flex justify-center items-center space-x-3">
                                                            <a href="/my-account?tab=orders&order_id=<?php echo $order['id']; ?>&source=cms" class="text-blue-500 hover:text-blue-700 bg-blue-50 p-2 rounded-full hover:bg-blue-100 transition" title="Ver Detalle">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <?php if (in_array(strtolower($order['status']), ['pending', 'failed', 'on-hold'])): ?>
                                                                <button onclick="openPaymentModal('<?php echo $order['id']; ?>', '<?php echo number_format($order['total'], 2); ?>')" class="text-green-500 hover:text-green-700 bg-green-50 p-2 rounded-full hover:bg-green-100 transition" title="Pagar">
                                                                    <i class="fas fa-dollar-sign"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <button class="text-gray-400 hover:text-gray-600 bg-gray-50 p-2 rounded-full hover:bg-gray-100 transition" title="Factura">
                                                                    <i class="fas fa-file-invoice"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="6" class="py-12 text-center text-gray-500 italic">No se encontraron pedidos.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination Mock -->
                            <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-between">
                                <span class="text-sm text-gray-600">Mostrando <?php echo count($orders); ?> de <?php echo count($orders); ?> pedidos</span>
                                <div class="flex space-x-1">
                                    <button class="px-3 py-1 border rounded bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50"><i class="fas fa-chevron-left"></i></button>
                                    <button class="px-3 py-1 border rounded bg-blue-600 text-white font-medium">1</button>
                                    <button class="px-3 py-1 border rounded bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <?php
                                $totalOrders = count($orders);
                                $totalSpent = array_sum(array_column($orders, 'total'));
                                $pendingOrders = count(array_filter($orders, fn($o) => in_array(strtolower($o['status']), ['pending', 'processing', 'on-hold'])));
                                $completedOrders = count(array_filter($orders, fn($o) => strtolower($o['status']) === 'completed'));
                            ?>
                            <div class="bg-white p-5 rounded-xl border shadow-sm flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Pedidos Totales</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo $totalOrders; ?></p>
                                </div>
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                            <div class="bg-white p-5 rounded-xl border shadow-sm flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Gasto Total</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1">$<?php echo number_format($totalSpent, 2); ?></p>
                                </div>
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <div class="bg-white p-5 rounded-xl border shadow-sm flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Pedidos Pendientes</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo $pendingOrders; ?></p>
                                </div>
                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <div class="bg-white p-5 rounded-xl border shadow-sm flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Pedidos Entregados</p>
                                    <p class="text-2xl font-bold text-gray-900 mt-1"><?php echo $completedOrders; ?></p>
                                </div>
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-purple-600">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($currentTab === 'downloads'): ?>
                    <div class="bg-white rounded-xl shadow-sm border p-8 text-center animate-fade-in-up">
                        <?php if (empty($downloads)): ?>
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-cloud-download-alt text-4xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Sin Descargas</h3>
                            <p class="text-gray-500 max-w-md mx-auto">No tienes archivos disponibles para descargar en este momento. Los productos digitales aparecerán aquí una vez adquiridos.</p>
                            <a href="/" class="mt-6 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Explorar Tienda</a>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($downloads as $download): ?>
                                    <div class="flex items-center justify-between p-4 border rounded-lg hover:border-blue-500 transition group bg-gray-50 hover:bg-white">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 text-blue-600 group-hover:scale-110 transition">
                                                <i class="fas fa-file-alt text-xl"></i>
                                            </div>
                                            <div class="text-left">
                                                <h4 class="font-semibold text-gray-800"><?php echo htmlspecialchars($download['product_name'] ?? 'Archivo'); ?></h4>
                                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($download['file_name'] ?? 'Descarga'); ?> - Expira: Nunca</p>
                                            </div>
                                        </div>
                                        <a href="<?php echo htmlspecialchars($download['download_url'] ?? '#'); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center shadow-sm">
                                            <i class="fas fa-download mr-2"></i> Descargar
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($currentTab === 'addresses'): ?>
                    <div class="space-y-6 animate-fade-in-up">
                         <div class="flex justify-between items-center mb-2">
                            <h3 class="text-2xl font-bold text-gray-800">Mis Direcciones</h3>
                            <p class="text-gray-500 text-sm">Gestiona tus direcciones de envío y facturación</p>
                        </div>
                        
                        <div class="max-w-2xl mx-auto">
                            <!-- Billing Address -->
                            <div class="bg-white rounded-xl shadow-sm border overflow-hidden group hover:shadow-md transition">
                                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                                    <h4 class="font-bold text-gray-700 flex items-center">
                                        <i class="fas fa-file-invoice-dollar mr-2 text-blue-500"></i> Facturación
                                    </h4>
                                    <span class="text-xs font-semibold bg-blue-100 text-blue-800 px-2 py-1 rounded">Predeterminada</span>
                                </div>
                                <div class="p-6 relative">
                                    <?php if (!empty($addresses['billing'])): ?>
                                        <address class="not-italic text-sm text-gray-600 space-y-2">
                                            <p class="font-bold text-lg text-gray-900 mb-1"><?php echo htmlspecialchars(($addresses['billing']['first_name']??'') . ' ' . ($addresses['billing']['last_name']??'')); ?></p>
                                            <div class="flex items-start"><i class="fas fa-map-marker-alt mt-1 mr-2 text-gray-400 w-4"></i> <span><?php echo htmlspecialchars($addresses['billing']['address_1'] ?? ''); ?></span></div>
                                            <div class="flex items-start"><i class="fas fa-city mt-1 mr-2 text-gray-400 w-4"></i> <span><?php echo htmlspecialchars($addresses['billing']['city'] ?? '') . ', ' . htmlspecialchars($addresses['billing']['postcode'] ?? ''); ?></span></div>
                                            <div class="flex items-start"><i class="fas fa-globe mt-1 mr-2 text-gray-400 w-4"></i> <span><?php echo htmlspecialchars($addresses['billing']['country'] ?? ''); ?></span></div>
                                            <div class="flex items-start"><i class="fas fa-phone mt-1 mr-2 text-gray-400 w-4"></i> <span><?php echo htmlspecialchars($addresses['billing']['phone'] ?? ''); ?></span></div>
                                        </address>
                                    <?php else: ?>
                                        <div class="text-center py-4 text-gray-400">
                                            <i class="fas fa-map-marked-alt text-3xl mb-2"></i>
                                            <p>No definida</p>
                                        </div>
                                    <?php endif; ?>
                                    
                                     <button onclick='openAddressModal("billing", <?php echo json_encode($addresses["billing"] ?? []); ?>)' class="mt-6 w-full py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 font-medium transition flex items-center justify-center">
                                        <i class="fas fa-edit mr-2"></i> Editar Dirección
                                    </button>
                                </div>
                            </div>
                            

                        </div>
                    </div>

                <?php elseif ($currentTab === 'account-details'): ?>
                    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border overflow-hidden animate-fade-in-up">
                        <div class="p-8 border-b">
                            <h3 class="text-2xl font-bold text-gray-800">Detalles de la Cuenta</h3>
                            <p class="text-gray-500">Actualiza tu información personal y contraseña</p>
                        </div>
                        <div class="p-8">
                            <?php if(isset($_GET['success'])): ?>
                                <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i> Detalles de la cuenta actualizados correctamente.
                                </div>
                            <?php endif; ?>
                            <?php if(isset($_GET['error'])): ?>
                                <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                                </div>
                            <?php endif; ?>

                            <form action="/my-account/update-details" method="POST" class="space-y-6">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos</label>
                                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre visible</label>
                                    <input type="text" name="display_name" value="<?php echo htmlspecialchars($user['items']['display_name'] ?? $username ); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                    <p class="text-xs text-gray-500 mt-1">Así es como aparecerá tu nombre en la sección de cuenta y valoraciones.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Dirección de correo electrónico</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['items']['user_email'] ?? $userEmail); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition bg-gray-50" readonly>
                                </div>

                                <div class="border-t pt-6 mt-6">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Cambio de contraseña</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña actual (déjalo en blanco para no cambiarla)</label>
                                            <input type="password" name="password_current" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Nueva contraseña</label>
                                            <input type="password" name="password_1" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar nueva contraseña</label>
                                            <input type="password" name="password_2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition transform hover:scale-105 shadow-lg flex items-center">
                                        <i class="fas fa-save mr-2"></i> Guardar los cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                
                <?php elseif ($currentTab === 'my-courses'): ?>
                    <div class="animate-fade-in-up">
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-gray-800">Mis Cursos</h3>
                            <p class="text-gray-500">Accede al contenido de tus cursos adquiridos</p>
                        </div>

                        <?php if (empty($courses)): ?>
                             <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
                                <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-graduation-cap text-5xl text-blue-200"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">No estás inscrito en ningún curso</h3>
                                <p class="text-gray-500 max-w-lg mx-auto mb-8">Parece que aún no has comprado ningún curso. ¡Explora nuestro catálogo para empezar a aprender!</p>
                                <a href="/shop" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                                    <i class="fas fa-search mr-2"></i> Ver Cursos Disponibles
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($courses as $course): ?>
                                    <div class="bg-white rounded-xl shadow-sm border overflow-hidden group hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                                        <!-- Course Header Placeholder (Gradient) -->
                                        <div class="h-40 bg-gradient-to-br from-blue-600 to-indigo-700 relative flex items-center justify-center overflow-hidden">
                                            <div class="absolute inset-0 bg-pattern opacity-10"></div> <!-- Mock Pattern -->
                                            <i class="fas fa-book-open text-white text-5xl opacity-80 group-hover:scale-110 transition duration-500"></i>
                                            <div class="absolute bottom-3 right-3">
                                                 <span class="bg-white/20 backdrop-blur-md text-white text-xs font-bold px-2 py-1 rounded">Online</span>
                                            </div>
                                        </div>
                                        
                                        <div class="p-6">
                                            <h4 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2 h-14 group-hover:text-blue-600 transition">
                                                <?php echo htmlspecialchars($course['course_name'] ?? 'Curso Sin Título'); ?>
                                            </h4>
                                            
                                            <!-- Mock Progress -->
                                            <div class="mt-4 mb-4">
                                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                                    <span>Progreso estimado</span>
                                                    <span class="font-semibold text-blue-600">0%</span>
                                                </div>
                                                <div class="w-full bg-gray-100 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                                                <span class="text-xs text-gray-400 font-medium">Acceso Ilimitado</span>
                                                <a href="<?php echo htmlspecialchars($course['course_url'] ?? '#'); ?>" class="text-blue-600 font-bold text-sm hover:text-blue-800 flex items-center">
                                                    Ir al Aula <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($currentTab === 'payment-methods'): ?>
                     <!-- Payment Methods Section from pago.html -->
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">Métodos de Pago</h3>
                        <p class="text-gray-600">Administra tus tarjetas y métodos de pago guardados</p>
                    </div>
                    
                    <!-- Tarjetas guardadas -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-lg font-semibold">Tarjetas Guardadas</h4>
                            <button onclick="openPaymentModal(null, '0.00')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium flex items-center">
                                <i class="fas fa-plus mr-2"></i> Agregar Tarjeta
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <?php if (!empty($payment_methods)): ?>
                                <?php foreach($payment_methods as $index => $card): ?>
                                    <?php 
                                        $bgClass = $card['is_default'] ? 'from-blue-500 to-blue-700' : 'from-gray-800 to-gray-900'; 
                                        $cardName = $card['is_default'] ? 'Tarjeta Principal' : 'Tarjeta Secundaria';
                                    ?>
                                    <div class="payment-card bg-gradient-to-r <?php echo $bgClass; ?> text-white rounded-xl p-6 shadow-lg">
                                        <div class="flex justify-between items-start mb-8">
                                            <div>
                                                <p class="text-sm opacity-90"><?php echo $cardName; ?></p>
                                                <p class="text-xl font-bold mt-1">•••• •••• •••• <?php echo htmlspecialchars($card['last_four']); ?></p>
                                            </div>
                                            <div class="flex items-center">
                                                <div class="w-12 h-8 bg-white rounded flex items-center justify-center mr-2">
                                                    <span class="text-gray-800 font-bold"><?php echo htmlspecialchars($card['card_type'] ?? 'CARD'); ?></span>
                                                </div>
                                                <form action="/my-account/payment-methods/delete" method="POST" class="inline">
                                                    <input type="hidden" name="card_id" value="<?php echo $card['id']; ?>">
                                                    <button type="submit" class="text-white opacity-80 hover:opacity-100" title="Eliminar tarjeta">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-end">
                                            <div>
                                                <p class="text-sm opacity-90">Titular</p>
                                                <p class="font-medium"><?php echo htmlspecialchars(strtoupper($username)); ?></p>
                                            </div>
                                            <div>
                                                <p class="text-sm opacity-90">Válida hasta</p>
                                                <p class="font-medium"><?php echo htmlspecialchars($card['expiry_month'] . '/' . substr($card['expiry_year'], -2)); ?></p>
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-4 border-t <?php echo $card['is_default'] ? 'border-blue-400' : 'border-gray-700'; ?>">
                                            <?php if($card['is_default']): ?>
                                                <div class="flex items-center text-sm">
                                                    <i class="fas fa-check-circle mr-2 text-green-300"></i>
                                                    <span>Método de pago predeterminado</span>
                                                </div>
                                            <?php else: ?>
                                                <form action="/my-account/payment-methods/set-default" method="POST">
                                                    <input type="hidden" name="card_id" value="<?php echo $card['id']; ?>">
                                                    <button type="submit" class="text-sm text-blue-300 hover:text-blue-200">
                                                        <i class="far fa-star mr-1"></i> Establecer como predeterminada
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-span-1 md:col-span-2 text-center py-8 text-gray-500 bg-gray-50 rounded-xl border border-dashed">
                                    <i class="fas fa-credit-card text-3xl mb-3 text-gray-300"></i>
                                    <p>No tienes tarjetas guardadas.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Otros métodos de pago -->
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold mb-4">Otros Métodos de Pago</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- PayPal -->
                            <div class="payment-card bg-white border rounded-xl p-6 hover:border-blue-500">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center mr-4">
                                        <i class="fab fa-paypal text-blue-600 text-2xl"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold">PayPal</h5>
                                        <p class="text-sm text-gray-600">pago@ejemplo.com</p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">Conectado</span>
                                    <button class="text-red-600 hover:text-red-800 text-sm font-medium">Desconectar</button>
                                </div>
                            </div>
                            
                            <!-- Transferencia bancaria -->
                            <div class="payment-card bg-white border rounded-xl p-6 hover:border-blue-500">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center mr-4">
                                        <i class="fas fa-university text-green-600 text-2xl"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold">Transferencia Bancaria</h5>
                                        <p class="text-sm text-gray-600">Cuenta guardada</p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Disponible</span>
                                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Gestionar</button>
                                </div>
                            </div>
                            
                            <!-- Efectivo -->
                            <div class="payment-card bg-white border rounded-xl p-6 hover:border-blue-500">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center mr-4">
                                        <i class="fas fa-money-bill-wave text-yellow-600 text-2xl"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-semibold">Pago en Efectivo</h5>
                                        <p class="text-sm text-gray-600">Al recibir el pedido</p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">Disponible</span>
                                    <button class="text-gray-600 hover:text-gray-800 text-sm font-medium">Configurar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Historial de transacciones (Mock / Demo) -->
                    <div class="bg-white rounded-xl shadow-sm border">
                        <div class="p-6 border-b">
                            <h4 class="text-lg font-semibold">Historial de Transacciones Recientes</h4>
                            <p class="text-gray-500 text-sm">Últimos pagos realizados</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="py-4 px-6 text-sm text-gray-500">15 May, 2023</td>
                                        <td class="py-4 px-6 font-medium">Pago Pedido #ORD-7842</td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center">
                                                <div class="w-6 h-4 bg-blue-700 rounded mr-2"></div>
                                                <span class="text-sm">VISA ****4512</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 font-medium">$89.99</td>
                                        <td class="py-4 px-6">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completado</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($currentTab === 'account-details'): ?>
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Detalles de la cuenta</h3>
                        <form class="space-y-6 max-w-2xl">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                                    <input type="text" value="Carlos" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos</label>
                                    <input type="text" value="Lopez" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                                <input type="email" value="<?php echo htmlspecialchars($userEmail); ?>" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" readonly>
                            </div>
                            <div class="border-t pt-6 mt-6">
                                <h4 class="font-semibold mb-4">Cambio de Contraseña</h4>
                                <div class="space-y-4">
                                     <input type="password" placeholder="Contraseña actual" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                     <input type="password" placeholder="Nueva contraseña" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>
                            <button type="button" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Guardar Cambios</button>
                        </form>
                    </div>

                <?php elseif ($currentTab === 'my-courses'): ?>
                     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <div class="bg-white p-6 rounded-xl shadow-sm border card-hover">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4 text-blue-600">
                                        <i class="fas fa-graduation-cap text-2xl"></i>
                                    </div>
                                    <h4 class="font-bold text-lg mb-2 line-clamp-2"><?php echo htmlspecialchars($course['course_name'] ?? 'Curso sin nombre'); ?></h4>
                                    <p class="text-sm text-gray-500 mb-4">Pedido #<?php echo $course['order_id']; ?></p>
                                    <a href="<?php echo $course['course_url']; ?>" target="_blank" class="block w-full text-center py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition font-medium">
                                        Acceder al Curso
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-3 text-center py-12 bg-white rounded-xl border border-dashed">
                                <i class="fas fa-book-open text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">No tienes cursos activos.</p>
                            </div>
                        <?php endif; ?>
                     </div>
                <?php endif; ?>

            </main>
        </div>
    </div>

    <?php if (isset($viewing_order) && $viewing_order): ?>
    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform transition-all">
            <div class="bg-gray-50 p-6 border-b flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Detalles del Pedido #ORD-<?php echo $viewing_order['id']; ?></h3>
                <a href="/my-account?tab=orders" class="text-gray-500 hover:text-gray-700 transition">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <div class="mb-6 grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Fecha del pedido:</p>
                        <p class="font-bold"><?php echo date('d M, Y H:i', strtotime($viewing_order['date'])); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Estado:</p>
                        <p class="font-bold uppercase <?php echo $viewing_order['status'] === 'completed' ? 'text-green-600' : 'text-yellow-600'; ?>"><?php echo htmlspecialchars($viewing_order['status']); ?></p>
                    </div>
                </div>
                
                <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Artículos</h4>
                <div class="space-y-4 mb-6">
                    <?php foreach ($viewing_order['items'] as $item): ?>
                    <div class="flex justify-between items-center border-b pb-4 last:border-0">
                        <div>
                            <p class="font-medium text-gray-800"><?php echo htmlspecialchars($item['name']); ?></p>
                            <p class="text-sm text-gray-500">Cant: <?php echo $item['quantity']; ?> x $<?php echo number_format($item['price'] ?? 0, 2); ?></p>
                        </div>
                        <div class="font-bold text-gray-900">
                            $<?php echo number_format($item['total'] ?? 0, 2); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center">
                    <span class="font-bold text-gray-700 uppercase">Total del Pedido</span>
                    <span class="text-2xl font-bold text-blue-600">$<?php echo number_format($viewing_order['total'], 2); ?></span>
                </div>
            </div>
            <div class="p-6 border-t bg-gray-50 flex justify-end">
                <a href="/my-account?tab=orders" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">Cerrar</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Address Edit Modal -->
    <div id="addressModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden transform transition-all">
            <div class="bg-gray-50 p-6 border-b flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800" id="addressModalTitle">Editar Dirección</h3>
                <button onclick="closeAddressModal()" class="text-gray-500 hover:text-gray-700 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="addressForm" onsubmit="saveAddress(event)">
                    <input type="hidden" id="addr_type">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                            <input type="text" id="addr_first_name" name="first_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos</label>
                            <input type="text" id="addr_last_name" name="last_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección (Calle y número)</label>
                        <input type="text" id="addr_address_1" name="address_1" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                            <input type="text" id="addr_city" name="city" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                            <input type="text" id="addr_postcode" name="postcode" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                        <input type="text" id="addr_country" name="country" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" id="addr_phone" name="phone" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAddressModal()" class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-gray-600">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Guardar Dirección</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Modal backdrop -->
    <div id="paymentModalBackdrop" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(17,24,39,0.7); z-index:9998;"></div>
    <!-- Payment Modal dialog -->
    <div id="paymentModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999; width:calc(100% - 2rem); max-width:480px; max-height:90vh;">
        <div class="bg-white rounded-xl shadow-2xl w-full overflow-hidden" style="max-height:90vh; display:flex; flex-direction:column;">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 flex justify-between items-center text-white">
                <div>
                    <h3 class="text-xl font-bold">Pagar Pedido #<span id="modalOrderId">000</span></h3>
                    <p class="text-sm opacity-90">Total a pagar: <span id="modalOrderTotal">$0.00</span></p>
                </div>
                <button onclick="closePaymentModal()" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Payment Methods Tabs -->
            <div class="flex border-b">
                <button onclick="switchPaymentTab('card')" id="tab-card" class="flex-1 py-3 text-sm font-semibold text-blue-600 border-b-2 border-blue-600 bg-blue-50 focus:outline-none transition">
                    <i class="fas fa-credit-card mr-2"></i> Tarjeta
                </button>
                <button onclick="switchPaymentTab('paypal')" id="tab-paypal" class="flex-1 py-3 text-sm font-semibold text-gray-500 hover:text-blue-600 focus:outline-none transition">
                    <i class="fab fa-paypal mr-2"></i> PayPal
                </button>
                <button onclick="switchPaymentTab('transfer')" id="tab-transfer" class="flex-1 py-3 text-sm font-semibold text-gray-500 hover:text-blue-600 focus:outline-none transition">
                    <i class="fas fa-university mr-2"></i> Transf.
                </button>
            </div>
            
            <div class="p-6" style="overflow-y:auto; flex:1;"> <!-- Scrollable body -->
                 <!-- Loader Overlay -->
                <div id="paymentLoader" class="absolute inset-0 bg-white bg-opacity-90 hidden flex-col items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-3"></div>
                    <p class="text-blue-600 font-semibold animate-pulse">Procesando pago...</p>
                </div>

                <!-- Card Form -->
                <div id="content-card">
                    <form id="cardPaymentForm" onsubmit="processCardPayment(event)">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titular de la tarjeta</label>
                            <input type="text" id="cardHolder" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nombre completo" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de tarjeta</label>
                            <div class="relative">
                                <input type="text" id="cardNumber" maxlength="19" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pl-10" placeholder="0000 0000 0000 0000" required>
                                <i class="fas fa-credit-card absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Vencimiento</label>
                                <input type="text" id="cardExpiry" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="MM/YY" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
                                <input type="text" id="cardCvc" maxlength="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="123" required>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform hover:scale-[1.02]">
                            Pagar Ahora
                        </button>
                    </form>
                </div>

                <!-- PayPal Content -->
                <div id="content-paypal" class="hidden text-center py-4">
                    <i class="fab fa-paypal text-6xl text-blue-600 mb-4 animate-bounce"></i>
                    <p class="mb-6 text-gray-600">Serás redirigido a PayPal para completar tu pago de forma segura.</p>
                    <button onclick="processPayPalPayment()" class="w-full bg-[#0070ba] hover:bg-[#003087] text-white font-bold py-3 rounded-lg shadow-lg transition flex items-center justify-center">
                        <i class="fab fa-paypal mr-2"></i> Pagar con PayPal
                    </button>
                </div>

                <!-- Transfer Content -->
                <div id="content-transfer" class="hidden">
                    <div class="bg-gray-50 p-4 rounded-lg border mb-4 text-sm text-center">
                        <p class="font-bold text-gray-800 mb-2 border-b pb-2">Datos Bancarios para Transferencia / ACH</p>
                        <div class="text-left inline-block space-y-1">
                            <p><span class="font-semibold text-gray-600 w-24 inline-block">Banco:</span> <span class="font-bold text-gray-900">BANCO GENERAL, S.A.</span></p>
                            <p><span class="font-semibold text-gray-600 w-24 inline-block">Beneficiario:</span> <span class="font-bold text-gray-900">PAMEL SA</span></p>
                            <p><span class="font-semibold text-gray-600 w-24 inline-block">Cuenta:</span> <span class="font-mono font-bold text-lg text-blue-800">04-51-98-483249-4</span></p>
                            <p><span class="font-semibold text-gray-600 w-24 inline-block">Tipo:</span> <span class="text-gray-900">Ahorro</span></p>
                            <p><span class="font-semibold text-gray-600 w-24 inline-block">Correo:</span> <span class="text-gray-900">info@pamel.edu.pa</span></p>
                        </div>
                    </div>
                    <form id="transferForm" onsubmit="processTransferReport(event)">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Referencia</label>
                            <input type="text" id="transferRef" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej: 8492033" required>
                        </div>
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Comprobante de Pago <span class="text-gray-400">(opcional)</span></label>
                            <label for="transferReceipt" class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition group">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-300 group-hover:text-blue-400 mb-1 transition"></i>
                                <span class="text-sm text-gray-500 group-hover:text-blue-500" id="receiptFileName">Haz clic para subir imagen o PDF</span>
                            </label>
                            <input type="file" id="transferReceipt" accept="image/*,.pdf" class="hidden" onchange="updateReceiptName(this)">
                        </div>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-lg transition">
                            <i class="fas fa-paper-plane mr-2"></i> Reportar Transferencia
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Move modals to document.body so position:fixed works relative to viewport
        document.addEventListener('DOMContentLoaded', function() {
            ['paymentModalBackdrop','paymentModal','addressModal','orderDetailsModal'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) document.body.appendChild(el);
            });
        });

        // Modal Logic
        let currentOrderId = null;

        function openPaymentModal(orderId, total) {
            currentOrderId = orderId;
            document.getElementById('modalOrderId').textContent = orderId;
            document.getElementById('modalOrderTotal').textContent = '$' + total;
            document.getElementById('paymentModalBackdrop').style.display = 'block';
            document.getElementById('paymentModal').style.display = 'block';
            switchPaymentTab('transfer');
        }

        function closePaymentModal() {
            document.getElementById('paymentModalBackdrop').style.display = 'none';
            document.getElementById('paymentModal').style.display = 'none';
        }

        function switchPaymentTab(tab) {
            ['card', 'paypal', 'transfer'].forEach(t => {
                document.getElementById('content-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).classList.remove('text-blue-600', 'border-b-2', 'border-blue-600', 'bg-blue-50');
                document.getElementById('tab-' + t).classList.add('text-gray-500');
            });

            document.getElementById('content-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.add('text-blue-600', 'border-b-2', 'border-blue-600', 'bg-blue-50');
            document.getElementById('tab-' + tab).classList.remove('text-gray-500');
        }

        function showLoader(show) {
            const loader = document.getElementById('paymentLoader');
            if(show) loader.classList.remove('hidden'), loader.classList.add('flex');
            else loader.classList.add('hidden'), loader.classList.remove('flex');
        }

        async function sendPaymentRequest(method, data) {
            showLoader(true);
            try {
                const response = await fetch('/payment/process', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        order_id: currentOrderId,
                        method: method,
                        data: data
                    })
                });
                const result = await response.json();
                showLoader(false);
                
                if (result.success) {
                    alert('✅ ' + result.message);
                    if (result.redirect) window.location.href = result.redirect;
                    else location.reload();
                } else {
                    alert('❌ Error: ' + result.message);
                }
            } catch (error) {
                showLoader(false);
                alert('❌ Error de conexión: ' + error.message);
            }
        }

        function processCardPayment(e) {
            e.preventDefault();
            const cardData = {
                holder: document.getElementById('cardHolder').value,
                last_four: document.getElementById('cardNumber').value.slice(-4),
                token: 'tok_' + Math.random().toString(36).substr(2, 9) 
            };
            sendPaymentRequest('credit_card', cardData);
        }

        function processPayPalPayment() {
            sendPaymentRequest('paypal', { orderID: 'PAY-MOCK-' + Date.now() });
        }

        function updateReceiptName(input) {
            const label = document.getElementById('receiptFileName');
            label.textContent = input.files[0] ? input.files[0].name : 'Haz clic para subir imagen o PDF';
        }

        async function processTransferReport(e) {
            e.preventDefault();
            showLoader(true);
            try {
                const formData = new FormData();
                formData.append('order_id', currentOrderId);
                formData.append('method', 'transfer');
                formData.append('reference', document.getElementById('transferRef').value);
                const file = document.getElementById('transferReceipt').files[0];
                if (file) formData.append('receipt', file);

                const response = await fetch('/payment/process', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                showLoader(false);

                if (result.success) {
                    alert('✅ ' + result.message);
                    if (result.redirect) window.location.href = result.redirect;
                    else location.reload();
                } else {
                    alert('❌ Error: ' + result.message);
                }
            } catch (error) {
                showLoader(false);
                alert('❌ Error de conexión: ' + error.message);
            }
        }

        // Address Modal
        function openAddressModal(type, data) {
            document.getElementById('addressModal').classList.remove('hidden');
            document.getElementById('addressModal').classList.add('flex');
            document.getElementById('addressModalTitle').textContent = type === 'billing' ? 'Editar Dirección de Facturación' : 'Editar Dirección de Envío';
            document.getElementById('addr_type').value = type;

            const fields = ['first_name', 'last_name', 'address_1', 'city', 'postcode', 'country', 'phone'];
            fields.forEach(field => {
                document.getElementById('addr_' + field).value = data[field] || '';
            });
        }

        function closeAddressModal() {
            document.getElementById('addressModal').classList.add('hidden');
            document.getElementById('addressModal').classList.remove('flex');
        }

        async function saveAddress(e) {
            e.preventDefault();
            const type = document.getElementById('addr_type').value;
            const data = { 
                type: type,
                csrf_token: '<?php echo htmlspecialchars($csrf_token ?? ""); ?>'
            };
            ['first_name', 'last_name', 'address_1', 'city', 'postcode', 'country', 'phone'].forEach(field => {
                data[field] = document.getElementById('addr_' + field).value;
            });

            try {
                const response = await fetch('/my-account/address/update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Dirección actualizada correctamente');
                    location.reload();
                } else {
                    alert('❌ Error: ' + result.message);
                }
            } catch (error) {
                alert('❌ Error de conexión: ' + error.message);
            }
        }
    </script>
</body>
</html>
