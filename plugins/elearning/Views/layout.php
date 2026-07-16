<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'LMS', ENT_QUOTES) ?> | PAMEL E-Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50:  '#eff6ff',
                            100: '#dbeafe',
                            400: '#60a5fa',
                            500: '#1a56db',
                            600: '#1447c0',
                            700: '#1040b0',
                            900: '#0c2d6b',
                        },
                        gold: { DEFAULT: '#f59e0b', light: '#fef3c7', dark: '#b45309' },
                        navy: '#0a1628',
                    },
                    backgroundImage: {
                        'hero-gradient': 'linear-gradient(135deg, #0a1628 0%, #1a3a6b 50%, #0f4c8a 100%)',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .nav-link { position: relative; }
        .nav-link::after { content:''; position:absolute; bottom:-2px; left:0; width:0; height:2px; background:#1a56db; transition:width .25s; }
        .nav-link:hover::after { width:100%; }
        .card-hover { transition: transform .2s, box-shadow .2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(26,86,219,.15); }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 antialiased">

<!-- Topbar -->
<div class="bg-brand-500 text-white text-xs text-center py-2 px-4 font-medium tracking-wide">
    🚢 Certificaciones IMO reconocidas internacionalmente &mdash; ¡Inscríbete hoy y avanza en tu carrera marítima!
</div>

<!-- Navbar -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- Logo -->
            <a href="/" class="flex items-center gap-2.5 group">
                <div class="w-10 h-10 rounded-xl bg-brand-500 flex items-center justify-center shadow-lg shadow-brand-500/30 group-hover:scale-105 transition-transform">
                    <span class="text-white font-black text-base">P</span>
                </div>
                <div>
                    <span class="font-black text-navy text-lg leading-none">PAMEL</span>
                    <p class="text-xs text-gray-500 leading-none font-medium">Maritime Training Center</p>
                </div>
            </a>

            <!-- Nav links -->
            <div class="hidden md:flex items-center gap-7">
                <a href="/" class="nav-link text-sm font-semibold text-gray-600 hover:text-brand-500 transition-colors pb-0.5">Inicio</a>
                <a href="/courses" class="nav-link text-sm font-semibold text-gray-600 hover:text-brand-500 transition-colors pb-0.5">Cursos</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/dashboard" class="nav-link text-sm font-semibold text-gray-600 hover:text-brand-500 transition-colors pb-0.5">Mi Dashboard</a>
                <?php endif; ?>
            </div>

            <!-- Auth -->
            <div class="flex items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="hidden md:flex items-center gap-2 bg-slate-100 rounded-full px-3 py-1.5">
                        <div class="w-6 h-6 rounded-full bg-brand-500 flex items-center justify-center text-white text-xs font-bold">
                            <?= mb_strtoupper(mb_substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 max-w-[120px] truncate">
                            <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?>
                        </span>
                    </div>
                    <form method="POST" action="/logout">
                        <?= \Core\Security::getCsrfField() ?>
                        <button type="submit" class="text-xs font-semibold text-gray-500 hover:text-red-600 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition-colors">
                            Salir
                        </button>
                    </form>
                <?php else: ?>
                    <?php 
                        $mainSiteUrl = rtrim(\Core\Config::get('site.url'), '/');
                    ?>
                    <a href="<?= $mainSiteUrl ?>/login" class="text-sm font-semibold text-gray-600 hover:text-brand-500 transition-colors">
                        Iniciar Sesión
                    </a>
                    <a href="<?= $mainSiteUrl ?>/register" class="text-sm font-bold bg-brand-500 hover:bg-brand-600 text-white px-5 py-2 rounded-xl transition-all shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5">
                        Registrarse
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main><?= $content ?></main>

<!-- Footer -->
<footer class="bg-navy text-slate-400 mt-20">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center">
                        <span class="text-white font-black text-sm">P</span>
                    </div>
                    <span class="text-white font-black text-base">PAMEL LMS</span>
                </div>
                <p class="text-sm leading-relaxed">Plataforma de formación marítima profesional con certificaciones IMO reconocidas internacionalmente.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-3 text-sm uppercase tracking-widest">Cursos</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/courses" class="hover:text-white transition-colors">Catálogo Completo</a></li>
                    <li><a href="<?= rtrim(\Core\Config::get('site.url'), '/') ?>/shop" class="hover:text-white transition-colors">Tienda Online</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-3 text-sm uppercase tracking-widest">Plataforma</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="<?= rtrim(\Core\Config::get('site.url'), '/') ?>/my-account" class="hover:text-white transition-colors">Mi Cuenta</a></li>
                    <li><a href="/dashboard" class="hover:text-white transition-colors">Mi Dashboard</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 pt-6 text-center text-xs">
            &copy; <?= date('Y') ?> PAMEL Maritime Training Center. Todos los derechos reservados.
        </div>
    </div>
</footer>

</body>
</html>
