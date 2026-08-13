<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) . ' - ' : ''; ?>Admin | PAMEL LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        navy: { DEFAULT: '#0a1628', light: '#1a3a6b' }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .nav-link { display:flex; align-items:center; gap:10px; padding:8px 12px; border-radius:8px; font-size:12px; font-weight:600; color:#94a3b8; transition:all .15s; }
        .nav-link:hover { color:#fff; background:rgba(255,255,255,.06); }
        .nav-link.active { background:#1a56db; color:#fff; }
        .nav-section { font-size:9px; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:.15em; padding:0 12px; margin:20px 0 4px; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800" style="font-family:'Inter',sans-serif">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-60 bg-[#0a1628] hidden md:flex flex-col flex-shrink-0 sticky top-0 h-screen overflow-y-auto">

        <!-- Logo -->
        <div class="px-5 pt-6 pb-4 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center font-black text-white text-sm">P</div>
                <div>
                    <div class="text-white font-black text-sm leading-none">PAMEL LMS</div>
                    <div class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">Maritime Training</div>
                </div>
            </div>
        </div>

        <!-- User -->
        <div class="px-5 py-4 border-b border-white/5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-blue-700 flex items-center justify-center text-white font-black text-[10px]">
                    <?= mb_strtoupper(mb_substr($_SESSION['username'] ?? 'AD', 0, 2)) ?>
                </div>
                <div class="min-w-0">
                    <div class="text-white font-bold text-xs truncate"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
                    <div class="text-[9px] text-slate-500 font-bold uppercase tracking-wider flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Admin
                    </div>
                </div>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-0.5">
            <a href="/manager"              class="nav-link"><i class="fas fa-home w-4 text-center"></i>Dashboard</a>
            <a href="/manager/lms/courses"  class="nav-link"><i class="fas fa-book w-4 text-center"></i>Catálogo LMS</a>

            <div class="nav-section">E-Learning</div>
            <a href="/manager/lms/courses"   class="nav-link"><i class="fas fa-graduation-cap w-4 text-center"></i>Cursos</a>
            <a href="/manager/lms/students"  class="nav-link"><i class="fas fa-user-graduate w-4 text-center"></i>Estudiantes</a>
            <a href="/manager/lms/quizzes"   class="nav-link"><i class="fas fa-vial-circle-check w-4 text-center"></i>Quizzes</a>
            <a href="/manager/lms/categories" class="nav-link"><i class="fas fa-tags w-4 text-center"></i>Categorías LMS</a>
            <a href="/manager/lms/forums"    class="nav-link"><i class="fas fa-comments w-4 text-center"></i>Foros</a>

            <div class="nav-section">Comercio</div>
            <a href="/manager/products"    class="nav-link"><i class="fas fa-box-archive w-4 text-center"></i>Productos</a>
            <a href="/manager/categories"  class="nav-link"><i class="fas fa-folder-tree w-4 text-center"></i>Categorías</a>
            <a href="/manager/branches"    class="nav-link"><i class="fas fa-code-branch w-4 text-center"></i>Branches</a>
            <a href="/manager/modalities"  class="nav-link"><i class="fas fa-laptop w-4 text-center"></i>Modalities</a>
            <a href="/manager/orders"      class="nav-link"><i class="fas fa-receipt w-4 text-center"></i>Pedidos</a>
            <a href="/manager/quote-requests" class="nav-link"><i class="fas fa-file-invoice-dollar w-4 text-center"></i>Cotizaciones</a>

            <div class="nav-section">Servicios</div>
            <a href="/manager/admission-requests"  class="nav-link"><i class="fas fa-file-signature w-4 text-center"></i>Solicitudes</a>
            <a href="/manager/satisfaction-surveys" class="nav-link"><i class="fas fa-poll w-4 text-center"></i>Encuestas</a>

            <div class="nav-section">Sistema</div>
            <a href="/manager/users"        class="nav-link"><i class="fas fa-users w-4 text-center"></i>Usuarios</a>
            <a href="/manager/email-settings" class="nav-link"><i class="fas fa-paper-plane w-4 text-center"></i>Email SMTP</a>
            <a href="/manager/plugins"      class="nav-link"><i class="fas fa-puzzle-piece w-4 text-center"></i>Plugins</a>
            <a href="/" target="_blank"     class="nav-link"><i class="fas fa-globe w-4 text-center"></i>Ver Sitio Web</a>
        </nav>
    </aside>

    <!-- Content -->
    <div class="flex-1 min-w-0 flex flex-col">

        <!-- Top bar -->
        <header class="bg-white border-b border-slate-200 px-8 py-3 sticky top-0 z-30 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest"><?= htmlspecialchars($title ?? 'Gestión') ?></div>
                <div class="text-[9px] text-slate-400 font-medium"><?= date('l, d \d\e F \d\e Y') ?></div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full border border-blue-100">Admin</span>
                <a href="/manager/logout" class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-red-600 transition">
                    <i class="fas fa-sign-out-alt"></i>Cerrar sesión
                </a>
            </div>
        </header>

        <!-- Page content — natural browser scroll -->
        <main class="p-8">
            <?php if (!empty($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $msg): ?>
                    <?php
                        $colors = [
                            'success' => 'bg-emerald-50 border-emerald-300 text-emerald-800',
                            'error'   => 'bg-red-50 border-red-300 text-red-800',
                            'warning' => 'bg-amber-50 border-amber-300 text-amber-800',
                            'info'    => 'bg-blue-50 border-blue-300 text-blue-800',
                        ];
                        $icons = [
                            'success' => 'fa-check-circle text-emerald-500',
                            'error'   => 'fa-times-circle text-red-500',
                            'warning' => 'fa-exclamation-triangle text-amber-500',
                            'info'    => 'fa-info-circle text-blue-500',
                        ];
                        $cls  = $colors[$type]  ?? 'bg-gray-50 border-gray-300 text-gray-800';
                        $icon = $icons[$type] ?? 'fa-info-circle text-gray-500';
                    ?>
                    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl border <?= $cls ?> text-sm font-medium">
                        <i class="fas <?= $icon ?> flex-shrink-0"></i>
                        <?= htmlspecialchars($msg) ?>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <?= $content ?>
        </main>

    </div>
</div>

<script>
    const path = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(a => {
        const href = a.getAttribute('href');
        if (href === path || (path.startsWith(href) && href !== '/manager')) {
            a.classList.add('active');
        }
    });
</script>
</body>
</html>
