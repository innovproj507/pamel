<?php
/** Permisos del usuario en sesión: condicionan el menú y la etiqueta del rol. */
$currentUser = \Core\Auth::getInstance()->user();
$currentRole = $currentUser['role'] ?? '';
$roleLabel   = \Core\Permissions::label($currentRole);

// Preguntas que quedaron sin quiz: se muestran como insignia en el menú.
$requisitosPendientes = 0;
if (\Core\Permissions::roleCan($currentRole, 'lms.courses.manage')) {
    $requisitosPendientes = (int) (\Core\Database::getInstance()->fetchOne(
        "SELECT COUNT(*) AS c FROM requirement_imports WHERE status = 'pending'"
    )['c'] ?? 0);
}

$unassignedQuestions = 0;
if (\Core\Permissions::roleCan($currentRole, 'lms.quizzes.manage')) {
    $unassignedQuestions = (int) (\Core\Database::getInstance()->fetchOne(
        "SELECT COUNT(*) AS c FROM lms_questions q
         WHERE NOT EXISTS (SELECT 1 FROM lms_quizzes z WHERE z.id = q.quiz_id)"
    )['c'] ?? 0);
}
?>
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
                        navy:  { DEFAULT: '#132a5c', deep: '#0d1f47', light: '#1e3a7b' },
                        canvas: '#eef2f9'
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --navy-top:    #1b3a78;
            --navy-bottom: #0d1f47;
            --canvas:      #eef2f9;
            --card-border: #e8edf6;
            --ink:         #1e293b;
            --ink-soft:    #64748b;
        }

        body { font-family: 'Inter', sans-serif; background: var(--canvas); color: var(--ink); }

        /* ── Navegación lateral ── */
        .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 13px; margin: 1px 0;
            border-radius: 10px;
            font-size: 12.5px; font-weight: 600;
            color: #a9bbdd;
            transition: background .15s, color .15s;
        }
        .nav-link:hover   { color: #fff; background: rgba(255,255,255,.08); }
        .nav-link.active  { background: #2563eb; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,.35); }
        .nav-link i       { font-size: 13px; opacity: .9; }
        .nav-section {
            font-size: 9px; font-weight: 800; color: #6f86b8;
            text-transform: uppercase; letter-spacing: .16em;
            padding: 0 13px; margin: 18px 0 6px;
        }

        /* ── Tarjetas ── */
        .card {
            background: #fff;
            border: 1px solid var(--card-border);
            border-radius: 16px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04);
        }
        .card-pad { padding: 20px 22px; }

        /* Suaviza los paneles que ya existen en el resto de vistas */
        main .bg-white { box-shadow: 0 1px 2px rgba(16,24,40,.04); }

        /* ── Azulejos de icono en pastel ── */
        .tile {
            width: 46px; height: 46px; border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px; flex-shrink: 0;
        }
        .tile-blue    { background: #e7effe; color: #2563eb; }
        .tile-emerald { background: #e3f7ee; color: #059669; }
        .tile-violet  { background: #f0e9fe; color: #7c3aed; }
        .tile-amber   { background: #fff2de; color: #d97706; }
        .tile-rose    { background: #fde9ee; color: #e11d48; }
        .tile-sky     { background: #e2f3fd; color: #0284c7; }

        /* ── Tarjeta de indicador ── */
        .stat { display: flex; align-items: center; gap: 14px; }
        .stat-value { font-size: 26px; font-weight: 800; line-height: 1.1; color: var(--ink); }
        .stat-label { font-size: 11.5px; font-weight: 600; color: var(--ink-soft); margin-bottom: 2px; }
        .stat-foot  { font-size: 10.5px; font-weight: 700; margin-top: 3px; }

        /* ── Barra superior ── */
        .topbar-search {
            background: #f4f6fb; border: 1px solid transparent;
            border-radius: 10px; padding: 8px 14px 8px 38px;
            font-size: 12.5px; width: 260px; transition: border-color .15s, background .15s;
        }
        .topbar-search:focus { outline: none; background: #fff; border-color: #c7d7f5; }

        @media (max-width: 1024px) { .topbar-search { width: 170px; } }
    </style>
</head>
<body class="text-slate-800">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 hidden md:flex flex-col flex-shrink-0 sticky top-0 h-screen overflow-y-auto"
           style="background: linear-gradient(180deg, var(--navy-top) 0%, var(--navy-bottom) 100%);">

        <!-- Marca -->
        <div class="px-6 pt-7 pb-6 text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-white/12 backdrop-blur flex items-center justify-center mb-3">
                <i class="fas fa-graduation-cap text-white text-xl"></i>
            </div>
            <div class="text-white font-black text-lg leading-none tracking-wide">PAMEL</div>
            <div class="text-[9px] text-blue-200/70 font-bold uppercase tracking-[.2em] mt-1.5">Maritime Training</div>
        </div>

        <!-- Identidad -->
        <div class="mx-4 mb-2 px-3 py-3 rounded-xl bg-white/8 flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-black text-[11px] flex-shrink-0">
                <?= mb_strtoupper(mb_substr($_SESSION['username'] ?? 'AD', 0, 2)) ?>
            </div>
            <div class="min-w-0">
                <div class="text-white font-bold text-xs truncate"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
                <div class="text-[9px] text-blue-200/70 font-bold uppercase tracking-wider flex items-center gap-1 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span><?= htmlspecialchars($roleLabel) ?>
                </div>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-4 space-y-0.5">
            <a href="/manager"              class="nav-link"><i class="fas fa-home w-4 text-center"></i>Dashboard</a>
            <?php if (can('lms.courses.view')): ?>
            <a href="/manager/lms/courses"  class="nav-link"><i class="fas fa-book w-4 text-center"></i>Catálogo LMS</a>
            <?php endif; ?>

            <?php if (can('lms.courses.view') || can('lms.students.view') || can('lms.forums.view')): ?>
            <div class="nav-section">E-Learning</div>
            <?php endif; ?>
            <?php if (can('lms.courses.view')): ?>
            <a href="/manager/lms/courses"   class="nav-link"><i class="fas fa-graduation-cap w-4 text-center"></i>Cursos</a>
            <?php endif; ?>
            <?php if (can('lms.students.view')): ?>
            <a href="/manager/lms/students"  class="nav-link"><i class="fas fa-user-graduate w-4 text-center"></i>Estudiantes</a>
            <a href="/manager/lms/seguimiento" class="nav-link"><i class="fas fa-chart-line w-4 text-center"></i>Seguimiento</a>
            <?php endif; ?>
            <?php if (can('lms.teachers.view')): ?>
            <a href="/manager/lms/teachers"  class="nav-link"><i class="fas fa-chalkboard-teacher w-4 text-center"></i>Instructores</a>
            <?php endif; ?>
            <?php if (can('lms.quizzes.view')): ?>
            <a href="/manager/lms/quizzes"   class="nav-link"><i class="fas fa-vial-circle-check w-4 text-center"></i>Quizzes</a>
            <?php endif; ?>
            <?php if (can('lms.quizzes.manage') && !empty($unassignedQuestions)): ?>
            <a href="/manager/lms/questions/unassigned" class="nav-link"><i class="fas fa-inbox w-4 text-center"></i>Sin asignar
                <span class="ml-auto text-[9px] font-black bg-amber-500 text-white px-1.5 py-0.5 rounded-full"><?= $unassignedQuestions ?></span>
            </a>
            <?php endif; ?>
            <?php if (can('lms.categories.manage')): ?>
            <a href="/manager/lms/categories" class="nav-link"><i class="fas fa-tags w-4 text-center"></i>Categorías LMS</a>
            <?php endif; ?>
            <?php if (can('lms.courses.manage')): ?>
            <a href="/manager/requisitos/cursos" class="nav-link"><i class="fas fa-clipboard-check w-4 text-center"></i>Requisitos
                <?php if (!empty($requisitosPendientes)): ?>
                <span class="ml-auto text-[9px] font-black bg-amber-500 text-white px-1.5 py-0.5 rounded-full"><?= $requisitosPendientes ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>
            <?php if (can('lms.forums.view')): ?>
            <a href="/manager/lms/forums"    class="nav-link"><i class="fas fa-comments w-4 text-center"></i>Foros</a>
            <?php endif; ?>

            <?php if (can('shop.manage') || can('shop.orders.view') || can('quotes.view')): ?>
            <div class="nav-section">Comercio</div>
            <?php endif; ?>
            <?php if (can('shop.manage')): ?>
            <a href="/manager/products"    class="nav-link"><i class="fas fa-box-archive w-4 text-center"></i>Productos</a>
            <a href="/manager/categories"  class="nav-link"><i class="fas fa-folder-tree w-4 text-center"></i>Categorías</a>
            <a href="/manager/branches"    class="nav-link"><i class="fas fa-code-branch w-4 text-center"></i>Branches</a>
            <a href="/manager/modalities"  class="nav-link"><i class="fas fa-laptop w-4 text-center"></i>Modalities</a>
            <?php endif; ?>
            <?php if (can('shop.orders.view')): ?>
            <a href="/manager/orders"      class="nav-link"><i class="fas fa-receipt w-4 text-center"></i>Pedidos</a>
            <?php endif; ?>
            <?php if (can('quotes.view')): ?>
            <a href="/manager/quote-requests" class="nav-link"><i class="fas fa-file-invoice-dollar w-4 text-center"></i>Cotizaciones</a>
            <?php endif; ?>

            <?php if (can('admissions.view') || can('surveys.view')): ?>
            <div class="nav-section">Servicios</div>
            <?php endif; ?>
            <?php if (can('admissions.view')): ?>
            <a href="/manager/admission-requests"  class="nav-link"><i class="fas fa-file-signature w-4 text-center"></i>Solicitudes</a>
            <?php endif; ?>
            <?php if (can('surveys.view')): ?>
            <a href="/manager/satisfaction-surveys" class="nav-link"><i class="fas fa-poll w-4 text-center"></i>Encuestas</a>
            <?php endif; ?>

            <?php if (can('users.view') || can('settings.email') || can('settings.plugins')): ?>
            <div class="nav-section">Sistema</div>
            <?php endif; ?>
            <?php if (can('users.view')): ?>
            <a href="/manager/users"        class="nav-link"><i class="fas fa-users w-4 text-center"></i>Usuarios</a>
            <?php endif; ?>
            <?php if (can('settings.email')): ?>
            <a href="/manager/email-settings" class="nav-link"><i class="fas fa-paper-plane w-4 text-center"></i>Email SMTP</a>
            <?php endif; ?>
            <?php if (can('settings.plugins')): ?>
            <a href="/manager/plugins"      class="nav-link"><i class="fas fa-puzzle-piece w-4 text-center"></i>Plugins</a>
            <?php endif; ?>
            <a href="/" target="_blank"     class="nav-link"><i class="fas fa-globe w-4 text-center"></i>Ver Sitio Web</a>
        </nav>
    </aside>

    <!-- Content -->
    <div class="flex-1 min-w-0 flex flex-col">

        <!-- Top bar -->
        <header class="bg-white border-b px-6 lg:px-8 py-3 sticky top-0 z-30 flex items-center justify-between gap-4"
                style="border-color: var(--card-border)">
            <div class="min-w-0">
                <div class="text-sm font-black text-slate-800 truncate"><?= htmlspecialchars($title ?? 'Gestión') ?></div>
                <div class="text-[10px] text-slate-400 font-medium"><?= date('d/m/Y') ?></div>
            </div>

            <div class="flex items-center gap-3 lg:gap-4 flex-shrink-0">
                <div class="relative hidden sm:block">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                    <input type="text" class="topbar-search" placeholder="Buscar…" id="adminSearch">
                </div>

                <?php if (!empty($unassignedQuestions)): ?>
                <a href="/manager/lms/questions/unassigned" class="relative w-9 h-9 rounded-xl bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-400 transition" title="<?= $unassignedQuestions ?> preguntas sin asignar">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute -top-1 -right-1 min-w-[17px] h-[17px] px-1 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center">
                        <?= $unassignedQuestions > 99 ? '99+' : $unassignedQuestions ?>
                    </span>
                </a>
                <?php endif; ?>

                <div class="hidden md:flex items-center gap-2.5 pl-3 lg:pl-4 border-l" style="border-color: var(--card-border)">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-black text-[10px]">
                        <?= mb_strtoupper(mb_substr($_SESSION['username'] ?? 'AD', 0, 2)) ?>
                    </div>
                    <div class="leading-tight">
                        <div class="text-xs font-bold text-slate-700"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
                        <div class="text-[9px] text-slate-400 font-semibold"><?= htmlspecialchars($roleLabel) ?></div>
                    </div>
                </div>

                <a href="/manager/logout" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-rose-50 flex items-center justify-center text-slate-400 hover:text-rose-600 transition" title="Cerrar sesión">
                    <i class="fas fa-arrow-right-from-bracket text-sm"></i>
                </a>
            </div>
        </header>

        <!-- Page content — natural browser scroll -->
        <main class="p-6 lg:p-8">
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
