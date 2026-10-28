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

// Fecha de la barra superior, sin depender de la locale del servidor.
$mesesCortos = ['', 'ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];
$fechaBarra  = date('j') . ' ' . $mesesCortos[(int) date('n')] . ' ' . date('Y');

/** Primera página de configuración a la que el usuario tiene acceso. */
$urlAjustes = null;
foreach ([
    'settings.email'   => '/manager/email-settings',
    'settings.plugins' => '/manager/plugins',
    'users.view'       => '/manager/users',
] as $permiso => $url) {
    if (can($permiso)) { $urlAjustes = $url; break; }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) . ' - ' : ''; ?>Admin | PAMEL LMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome sigue cargado porque lo usan el resto de vistas del panel. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js"></script>
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
            --sb-ancho:    256px;
            --sb-rail:     74px;
        }

        body { font-family: 'Inter', sans-serif; background: var(--canvas); color: var(--ink); }

        /* ── Iconos de trazo (Lucide) ──
           Lucide sustituye cada <i data-lucide> por un <svg> de 24×24 y le
           traslada las clases, así que el tamaño se fija aquí. Mientras el
           script no ha corrido, el <i> vacío ya ocupa su hueco para que
           nada salte al aparecer el icono. */
        .ic, i[data-lucide] { width: 18px; height: 18px; flex-shrink: 0; display: inline-block; }
        .ic-14 { width: 14px; height: 14px; }
        .ic-16 { width: 16px; height: 16px; }
        .ic-17 { width: 17px; height: 17px; }
        .ic-20 { width: 20px; height: 20px; }
        svg.lucide { stroke-width: 2; }

        /* ── Navegación lateral ── */
        #sidebar {
            width: var(--sb-ancho);
            flex-shrink: 0;
            position: sticky; top: 0; height: 100vh;
            overflow-y: auto; overflow-x: hidden;
            background: linear-gradient(180deg, var(--navy-top) 0%, var(--navy-bottom) 100%);
            transition: width .2s ease;
        }
        #sidebar::-webkit-scrollbar { width: 6px; }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.14); border-radius: 3px; }

        .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 13px; margin: 1px 0;
            border-radius: 10px;
            font-size: 12.5px; font-weight: 600;
            color: #a9bbdd;
            white-space: nowrap;
            transition: background .15s, color .15s;
        }
        .nav-link:hover   { color: #fff; background: rgba(255,255,255,.08); }
        .nav-link.active  { background: #2563eb; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,.35); }
        .nav-link .ic     { opacity: .95; }
        .nav-section {
            font-size: 9px; font-weight: 800; color: #6f86b8;
            text-transform: uppercase; letter-spacing: .16em;
            padding: 0 13px; margin: 18px 0 6px;
            white-space: nowrap;
        }

        /* Barra estrecha: sólo iconos */
        body.nav-rail #sidebar { width: var(--sb-rail); }
        body.nav-rail .nav-link { justify-content: center; gap: 0; padding-left: 0; padding-right: 0; }
        body.nav-rail .solo-ancho { display: none !important; }
        body.nav-rail .nav-section { height: 1px; margin: 14px 13px; padding: 0; overflow: hidden;
                                     background: rgba(255,255,255,.1); color: transparent; }
        body.nav-rail .sb-marca { justify-content: center; }

        #navBackdrop { display: none; }

        @media (max-width: 767px) {
            #sidebar {
                position: fixed; left: 0; top: 0; z-index: 60;
                transform: translateX(-100%);
                transition: transform .22s ease;
                width: var(--sb-ancho);
            }
            body.nav-open #sidebar { transform: none; }
            body.nav-open #navBackdrop { display: block; position: fixed; inset: 0; z-index: 55;
                                         background: rgba(15,23,42,.5); }
            body.nav-rail #sidebar { width: var(--sb-ancho); }
            body.nav-rail .solo-ancho { display: initial !important; }
            body.nav-rail .nav-link { justify-content: flex-start; gap: 11px; padding: 9px 13px; }
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

        /* ── Tarjeta de indicador (blanca, usada en otras vistas) ── */
        .stat { display: flex; align-items: center; gap: 14px; }
        .stat-value { font-size: 26px; font-weight: 800; line-height: 1.1; color: var(--ink); }
        .stat-label { font-size: 11.5px; font-weight: 600; color: var(--ink-soft); margin-bottom: 2px; }
        .stat-foot  { font-size: 10.5px; font-weight: 700; margin-top: 3px; }

        /* ── Indicador a todo color (panel de inicio) ── */
        .statx {
            position: relative; overflow: hidden;
            border-radius: 16px; padding: 16px 18px 14px;
            color: #fff; display: block;
            box-shadow: 0 6px 18px rgba(16,24,40,.10);
            transition: transform .15s, box-shadow .15s;
        }
        .statx:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(16,24,40,.16); }
        .statx::after {
            content: ''; position: absolute; right: -28px; bottom: -42px;
            width: 128px; height: 128px; border-radius: 50%;
            background: rgba(255,255,255,.10);
        }
        .statx > * { position: relative; z-index: 1; }
        .statx-label { font-size: 11.5px; font-weight: 700; color: rgba(255,255,255,.88); }
        .statx-value { font-size: 30px; font-weight: 800; line-height: 1.05; margin-top: 10px; }
        .statx-foot  { font-size: 10.5px; font-weight: 600; color: rgba(255,255,255,.80); margin-top: 6px; }
        .statx-ico {
            width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
            background: rgba(255,255,255,.20);
            display: flex; align-items: center; justify-content: center; font-size: 14px;
        }
        .statx-spark { width: 86px; height: 30px; flex-shrink: 0; opacity: .95; }

        .sx-indigo  { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .sx-emerald { background: linear-gradient(135deg, #22c55e 0%, #0f9d58 100%); }
        .sx-amber   { background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%); }
        .sx-blue    { background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%); }
        .sx-rose    { background: linear-gradient(135deg, #f472b6 0%, #e11d48 100%); }
        .sx-teal    { background: linear-gradient(135deg, #2dd4bf 0%, #0d9488 100%); }

        /* ── Barra superior ── */
        .topbar-search {
            background: #f4f6fb; border: 1px solid transparent;
            border-radius: 10px; padding: 8px 14px 8px 38px;
            font-size: 12.5px; width: 260px; transition: border-color .15s, background .15s;
        }
        .topbar-search:focus { outline: none; background: #fff; border-color: #c7d7f5; }

        .tb-btn {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #94a3b8; background: #f6f8fc;
            transition: background .15s, color .15s;
        }
        .tb-btn:hover { background: #eef2fb; color: #2563eb; }

        .tb-chip {
            display: flex; align-items: center; gap: 7px;
            height: 36px; padding: 0 13px; border-radius: 10px;
            border: 1px solid var(--card-border);
            font-size: 11.5px; font-weight: 700; color: #64748b;
            white-space: nowrap;
        }

        .punto-vivo {
            width: 7px; height: 7px; border-radius: 50%; background: #22c55e;
            box-shadow: 0 0 0 0 rgba(34,197,94,.6);
            animation: latido 2s infinite;
        }
        @keyframes latido {
            0%   { box-shadow: 0 0 0 0 rgba(34,197,94,.55); }
            70%  { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
            100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
        }

        .btn-primario {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 16px; border-radius: 10px;
            background: linear-gradient(135deg, #fb923c, #f59e0b);
            color: #fff; font-size: 12.5px; font-weight: 700;
            box-shadow: 0 4px 12px rgba(245,158,11,.35);
            transition: transform .15s, box-shadow .15s;
        }
        .btn-primario:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(245,158,11,.45); }

        /* Resultados del buscador y menú de usuario */
        .menu-flotante {
            position: absolute; right: 0; top: calc(100% + 8px); z-index: 50;
            background: #fff; border: 1px solid var(--card-border);
            border-radius: 12px; box-shadow: 0 12px 30px rgba(16,24,40,.14);
            min-width: 210px; padding: 6px; display: none;
        }
        .menu-flotante.abierto { display: block; }
        .menu-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 11px; border-radius: 8px;
            font-size: 12.5px; font-weight: 600; color: #475569;
        }
        .menu-item:hover { background: #f4f6fb; color: #2563eb; }
        .menu-item.resaltado { background: #eef2fb; color: #2563eb; }

        /* ── Pantallas de listado ──
           Cabecera, barra de filtros, recuadros de resumen, tabla y
           paginador. Son clases nuevas: ninguna pisa a las que ya usan
           el resto de vistas del panel. */
        .btn-atras {
            width: 38px; height: 38px; border-radius: 11px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: #fff; border: 1px solid var(--card-border); color: #64748b;
            transition: background .15s, color .15s;
        }
        .btn-atras:hover { background: #f4f6fb; color: #2563eb; }

        .btn-sec, .btn-verde, .btn-violeta {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 9px 16px; border-radius: 10px;
            font-size: 12.5px; font-weight: 700; white-space: nowrap;
            transition: transform .15s, box-shadow .15s, background .15s, color .15s;
        }
        .btn-sec        { background: #fff; border: 1px solid var(--card-border); color: #475569; }
        .btn-sec:hover  { background: #f4f6fb; color: #2563eb; }
        .btn-verde      { background: #16a34a; color: #fff; box-shadow: 0 4px 12px rgba(22,163,74,.30); }
        .btn-violeta    { background: #6366f1; color: #fff; box-shadow: 0 4px 12px rgba(99,102,241,.30); }
        .btn-verde:hover, .btn-violeta:hover { transform: translateY(-1px); }

        /* Campos compactos de la barra de filtros */
        .campo {
            height: 34px; padding: 0 11px; border-radius: 8px;
            border: 1px solid var(--card-border); background: #fff;
            font-size: 12px; font-weight: 600; color: #475569;
            outline: none; transition: border-color .15s, box-shadow .15s;
        }
        .campo:focus { border-color: #c7d7f5; box-shadow: 0 0 0 3px rgba(37,99,235,.08); }
        select.campo { padding-right: 26px; cursor: pointer; }

        /* Recuadros de resumen sobre la tabla */
        .kpi { border: 1px solid var(--card-border); border-radius: 12px; padding: 11px 14px; background: #fff; }
        .kpi-label { font-size: 9.5px; font-weight: 800; color: #94a3b8;
                     text-transform: uppercase; letter-spacing: .1em; }
        .kpi-value { font-size: 21px; font-weight: 800; color: var(--ink); line-height: 1.15; margin-top: 4px; }

        /* Tabla de listado */
        .tabla { width: 100%; border-collapse: collapse; }
        .tabla th {
            text-align: left; white-space: nowrap; padding: 11px 12px;
            font-size: 9.5px; font-weight: 800; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .09em;
            border-bottom: 1px solid var(--card-border);
        }
        .tabla td {
            padding: 11px 12px; font-size: 12.5px; color: #475569;
            border-bottom: 1px solid #f1f5f9; vertical-align: middle;
        }
        .tabla tbody tr:last-child td { border-bottom: 0; }
        .tabla tbody tr:hover { background: #f8fafc; }
        .tabla .num    { text-align: right; font-variant-numeric: tabular-nums; }
        .tabla .centro { text-align: center; }
        /* Estado vacío: mismo bloque en todos los listados. */
        .vacio { padding: 56px 12px; text-align: center; }
        .vacio p { font-size: 13px; font-weight: 600; color: #94a3b8; }
        .vacio .ic { width: 30px; height: 30px; color: #cbd5e1; margin: 0 auto 12px; }
        /* Que la primera y la última columna queden a plomo con el resto de la tarjeta. */
        .tabla th:first-child, .tabla td:first-child { padding-left: 16px; }
        .tabla th:last-child,  .tabla td:last-child  { padding-right: 16px; }

        /* Pastillas de estado */
        .pastilla {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 7px;
            font-size: 10.5px; font-weight: 700; white-space: nowrap;
        }
        /* Puntito del mismo color que el texto, como en las pastillas de estado. */
        .pastilla .punto { width: 5px; height: 5px; border-radius: 50%;
                           background: currentColor; opacity: .75; flex-shrink: 0; }
        .p-azul  { background: #dbeafe; color: #1d4ed8; }
        .p-verde { background: #d7f5e5; color: #047857; }
        .p-ambar { background: #fef3c7; color: #b45309; }
        .p-gris  { background: #eef2f7; color: #64748b; }
        .p-rojo  { background: #fee2e2; color: #b91c1c; }
        .p-violeta { background: #ede9fe; color: #6d28d9; }
        .p-indigo  { background: #e0e7ff; color: #4338ca; }
        .p-cian    { background: #cffafe; color: #0e7490; }

        /* Botones de acción cuadrados */
        .ib {
            width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; transition: transform .12s, filter .12s;
        }
        .ib:hover { transform: translateY(-1px); filter: brightness(1.08); }
        .ib-azul   { background: #3b82f6; }
        .ib-ambar  { background: #f59e0b; }
        .ib-verde  { background: #10b981; }
        .ib-indigo { background: #6366f1; }
        .ib-rojo   { background: #ef4444; }
        /* Acciones secundarias: sin relleno, para que la fila no se vuelva un arcoíris. */
        .ib-fantasma { background: #fff; border: 1px solid var(--card-border); color: #64748b; }
        .ib-fantasma:hover { border-color: #c7d7f5; color: #2563eb; filter: none; }

        /* Paginador */
        .pag {
            min-width: 32px; height: 32px; padding: 0 9px; border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #64748b;
            background: #fff; border: 1px solid var(--card-border);
            transition: background .15s, color .15s;
        }
        .pag:hover  { background: #f4f6fb; color: #2563eb; }
        .pag.activa { background: #2563eb; border-color: #2563eb; color: #fff;
                      box-shadow: 0 4px 10px rgba(37,99,235,.28); }
        .pag.inerte { border: 0; background: transparent; color: #cbd5e1; }

        /* ── Celda principal: inicial, título, subtítulo y datos ── */
        .avatar-ini {
            width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: #fff; font-size: 14px; font-weight: 800; line-height: 1;
        }
        /* Tintes alternativos, para distinguir personas de un vistazo. */
        .av-verde  { background: linear-gradient(135deg, #065f46, #10b981); }
        .av-ambar  { background: linear-gradient(135deg, #92400e, #f59e0b); }
        .av-rosa   { background: linear-gradient(135deg, #9f1239, #f43f5e); }
        .av-violeta{ background: linear-gradient(135deg, #4c1d95, #8b5cf6); }
        .av-cian   { background: linear-gradient(135deg, #155e75, #06b6d4); }
        /* Categoría bajo el título, en versalitas. */
        .sub-cat {
            font-size: 9.5px; font-weight: 800; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .08em;
        }
        /* Código del curso: monoespaciado para que los dígitos cuadren. */
        .cod {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 7px; border-radius: 6px; background: #f1f5f9;
            font-family: ui-monospace, "SFMono-Regular", Menlo, monospace;
            font-size: 10px; font-weight: 700; color: #475569; letter-spacing: -.01em;
        }

        /* Columna de contenido: cifra arriba, unidad debajo. */
        .meta-dato { display: inline-flex; align-items: center; gap: 6px; }
        .meta-dato .cifra { font-size: 13px; font-weight: 800; color: #334155; line-height: 1.05; }
        .meta-dato .unidad { font-size: 9.5px; font-weight: 700; color: #94a3b8; }

        /* Acciones con texto. Tintes suaves: la fila se lee sin gritar. */
        .acc {
            /* Sin encoger: si no cabe, la tabla se desplaza en horizontal en
               vez de apilar los botones y triplicar el alto de la fila. */
            display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0;
            padding: 5px 9px; border-radius: 8px; white-space: nowrap;
            font-size: 10px; font-weight: 800; letter-spacing: .04em;
            text-transform: uppercase; border: 1px solid transparent;
            transition: filter .12s, transform .12s;
        }
        .acc:hover { filter: brightness(.96); transform: translateY(-1px); }
        .acc-gris  { background: #fff;     border-color: var(--card-border); color: #475569; }
        .acc-azul  { background: #eff6ff;  border-color: #dbeafe; color: #1d4ed8; }
        .acc-verde { background: #ecfdf5;  border-color: #d1fae5; color: #047857; }
        .acc-ambar { background: #fffbeb;  border-color: #fef3c7; color: #b45309; }
        .acc-rojo  { background: #fef2f2;  border-color: #fee2e2; color: #b91c1c; }

        /* Miniatura de producto en los listados. */
        .miniatura { width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
                     object-fit: cover; border: 1px solid var(--card-border); background: #f6f8fc; }
        .miniatura.vacia { display: flex; align-items: center; justify-content: center; color: #cbd5e1; }

        /* Barra de avance de los listados de seguimiento. */
        .barra { flex: 1; min-width: 60px; height: 6px; border-radius: 999px;
                 background: #eef2f7; overflow: hidden; }
        .barra > span { display: block; height: 100%; border-radius: 999px; background: #3b82f6; }
        .barra.b-verde > span { background: #10b981; }
        .barra.b-ambar > span { background: #f59e0b; }
        .barra.b-gris  > span { background: #cbd5e1; }

        /* Rótulo sobre cada filtro, para que «Con y sin producto» se entienda. */
        .filtro-rot {
            display: block; margin-bottom: 4px;
            font-size: 9px; font-weight: 800; color: #94a3b8;
            text-transform: uppercase; letter-spacing: .09em;
        }

        @media (max-width: 1280px) { .topbar-search { width: 200px; } }
        @media (max-width: 1024px) { .topbar-search { width: 170px; } }
    </style>
</head>
<body class="text-slate-800">

<div id="navBackdrop" onclick="document.body.classList.remove('nav-open')"></div>

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside id="sidebar" class="flex flex-col">

        <!-- Marca -->
        <div class="sb-marca flex items-center gap-3 px-5 pt-6 pb-5">
            <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center flex-shrink-0">
                <i data-lucide="graduation-cap" class="ic ic-20 text-white"></i>
            </div>
            <div class="solo-ancho min-w-0">
                <div class="text-white font-black text-base leading-none tracking-wide">PAMEL</div>
                <div class="text-[9px] text-blue-200/70 font-bold uppercase tracking-[.18em] mt-1.5">Maritime Training</div>
            </div>
        </div>

        <!-- Identidad -->
        <div class="mx-3 mb-1 px-3 py-2.5 rounded-xl bg-white/8 flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-black text-[11px] flex-shrink-0">
                <?= mb_strtoupper(mb_substr($_SESSION['username'] ?? 'AD', 0, 2)) ?>
            </div>
            <div class="solo-ancho min-w-0">
                <div class="text-white font-bold text-xs truncate"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
                <div class="text-[9px] text-blue-200/70 font-bold uppercase tracking-wider flex items-center gap-1 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span><?= htmlspecialchars($roleLabel) ?>
                </div>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-3 space-y-0.5">
            <a href="/manager" class="nav-link" title="Dashboard"><i data-lucide="house" class="ic"></i><span class="solo-ancho">Dashboard</span></a>

            <?php if (can('lms.courses.view') || can('lms.students.view') || can('lms.forums.view')): ?>
            <div class="nav-section">E-Learning</div>
            <?php endif; ?>
            <?php if (can('lms.courses.view')): ?>
            <a href="/manager/lms/courses" class="nav-link" title="Cursos"><i data-lucide="graduation-cap" class="ic"></i><span class="solo-ancho">Cursos</span></a>
            <?php endif; ?>
            <?php if (can('lms.students.view')): ?>
            <a href="/manager/lms/students" class="nav-link" title="Estudiantes"><i data-lucide="users" class="ic"></i><span class="solo-ancho">Estudiantes</span></a>
            <a href="/manager/lms/seguimiento" class="nav-link" title="Seguimiento"><i data-lucide="chart-line" class="ic"></i><span class="solo-ancho">Seguimiento</span></a>
            <?php endif; ?>
            <?php if (can('lms.teachers.view')): ?>
            <a href="/manager/lms/teachers" class="nav-link" title="Instructores"><i data-lucide="presentation" class="ic"></i><span class="solo-ancho">Instructores</span></a>
            <?php endif; ?>
            <?php if (can('lms.quizzes.view')): ?>
            <a href="/manager/lms/quizzes" class="nav-link" title="Quizzes"><i data-lucide="flask-conical" class="ic"></i><span class="solo-ancho">Quizzes</span></a>
            <?php endif; ?>
            <?php if (can('lms.quizzes.manage') && !empty($unassignedQuestions)): ?>
            <a href="/manager/lms/questions/unassigned" class="nav-link" title="<?= $unassignedQuestions ?> preguntas sin asignar"><i data-lucide="inbox" class="ic"></i><span class="solo-ancho">Sin asignar</span>
                <span class="solo-ancho ml-auto text-[9px] font-black bg-amber-500 text-white px-1.5 py-0.5 rounded-full"><?= $unassignedQuestions ?></span>
            </a>
            <?php endif; ?>
            <?php if (can('lms.categories.manage')): ?>
            <a href="/manager/lms/categories" class="nav-link" title="Categorías LMS"><i data-lucide="tags" class="ic"></i><span class="solo-ancho">Categorías LMS</span></a>
            <?php endif; ?>
            <?php if (can('lms.courses.manage')): ?>
            <a href="/manager/requisitos/cursos" class="nav-link" title="Requisitos"><i data-lucide="clipboard-check" class="ic"></i><span class="solo-ancho">Requisitos</span>
                <?php if (!empty($requisitosPendientes)): ?>
                <span class="solo-ancho ml-auto text-[9px] font-black bg-amber-500 text-white px-1.5 py-0.5 rounded-full"><?= $requisitosPendientes ?></span>
                <?php endif; ?>
            </a>
            <?php endif; ?>
            <?php if (can('lms.forums.view')): ?>
            <a href="/manager/lms/forums" class="nav-link" title="Foros"><i data-lucide="message-square" class="ic"></i><span class="solo-ancho">Foros</span></a>
            <?php endif; ?>

            <?php if (can('shop.manage') || can('shop.orders.view') || can('quotes.view')): ?>
            <div class="nav-section">Comercio</div>
            <?php endif; ?>
            <?php if (can('shop.manage')): ?>
            <a href="/manager/products"   class="nav-link" title="Productos"><i data-lucide="package" class="ic"></i><span class="solo-ancho">Productos</span></a>
            <a href="/manager/categories" class="nav-link" title="Categorías"><i data-lucide="folder-tree" class="ic"></i><span class="solo-ancho">Categorías</span></a>
            <a href="/manager/branches"   class="nav-link" title="Ramas"><i data-lucide="git-branch" class="ic"></i><span class="solo-ancho">Ramas</span></a>
            <a href="/manager/modalities" class="nav-link" title="Modalidades"><i data-lucide="laptop" class="ic"></i><span class="solo-ancho">Modalidades</span></a>
            <?php endif; ?>
            <?php if (can('shop.orders.view')): ?>
            <a href="/manager/orders" class="nav-link" title="Pedidos"><i data-lucide="receipt" class="ic"></i><span class="solo-ancho">Pedidos</span></a>
            <?php endif; ?>
            <?php if (can('quotes.view')): ?>
            <a href="/manager/quote-requests" class="nav-link" title="Cotizaciones"><i data-lucide="file-text" class="ic"></i><span class="solo-ancho">Cotizaciones</span></a>
            <?php endif; ?>

            <?php if (can('admissions.view') || can('surveys.view')): ?>
            <div class="nav-section">Servicios</div>
            <?php endif; ?>
            <?php if (can('admissions.view')): ?>
            <a href="/manager/admission-requests" class="nav-link" title="Solicitudes"><i data-lucide="file-signature" class="ic"></i><span class="solo-ancho">Solicitudes</span></a>
            <?php endif; ?>
            <?php if (can('surveys.view')): ?>
            <a href="/manager/satisfaction-surveys" class="nav-link" title="Encuestas"><i data-lucide="clipboard-list" class="ic"></i><span class="solo-ancho">Encuestas</span></a>
            <?php endif; ?>

            <?php if (can('users.view') || can('settings.email') || can('settings.plugins')): ?>
            <div class="nav-section">Sistema</div>
            <?php endif; ?>
            <?php if (can('users.view')): ?>
            <a href="/manager/users" class="nav-link" title="Usuarios"><i data-lucide="users-round" class="ic"></i><span class="solo-ancho">Usuarios</span></a>
            <?php endif; ?>
            <?php if (can('settings.email')): ?>
            <a href="/manager/email-settings" class="nav-link" title="Email SMTP"><i data-lucide="send" class="ic"></i><span class="solo-ancho">Email SMTP</span></a>
            <?php endif; ?>
            <?php if (can('settings.plugins')): ?>
            <a href="/manager/plugins" class="nav-link" title="Plugins"><i data-lucide="puzzle" class="ic"></i><span class="solo-ancho">Plugins</span></a>
            <?php endif; ?>
            <a href="/" target="_blank" class="nav-link" title="Ver sitio web"><i data-lucide="globe" class="ic"></i><span class="solo-ancho">Ver Sitio Web</span></a>
        </nav>

        <div class="solo-ancho px-5 py-4 border-t border-white/10">
            <p class="text-[9.5px] font-bold text-blue-200/50 tracking-wide">PAMEL — Panama Maritime E-Learning</p>
        </div>
    </aside>

    <!-- Content -->
    <div class="flex-1 min-w-0 flex flex-col">

        <!-- Top bar -->
        <header class="bg-white border-b px-4 lg:px-6 py-2.5 sticky top-0 z-40 flex items-center gap-3"
                style="border-color: var(--card-border)">

            <button type="button" id="btnMenu" class="tb-btn flex-shrink-0" title="Contraer menú">
                <i data-lucide="menu" class="ic ic-17"></i>
            </button>

            <!-- Buscador del panel -->
            <div class="relative flex-1 max-w-md min-w-0">
                <i data-lucide="search" class="ic ic-14 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" id="adminSearch" autocomplete="off" class="topbar-search w-full"
                       style="width:100%" placeholder="Buscar sección del panel…">
                <div id="searchResults" class="menu-flotante" style="left:0; right:auto; width:100%"></div>
            </div>

            <div class="flex items-center gap-2 lg:gap-2.5 flex-shrink-0 ml-auto">

                <div class="tb-chip hidden lg:flex">
                    <i data-lucide="clock" class="ic ic-14 text-slate-300"></i>
                    <span><?= $fechaBarra ?></span>
                </div>

                <div class="tb-chip hidden xl:flex" style="border:0">
                    <span class="punto-vivo"></span>
                    <span class="text-emerald-600">En vivo</span>
                    <span class="text-slate-400 font-mono" id="relojVivo">--:--</span>
                </div>

                <?php if (!empty($unassignedQuestions)): ?>
                <a href="/manager/lms/questions/unassigned" class="tb-btn relative" title="<?= $unassignedQuestions ?> preguntas sin asignar">
                    <i data-lucide="bell" class="ic ic-17"></i>
                    <span class="absolute -top-1 -right-1 min-w-[17px] h-[17px] px-1 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center">
                        <?= $unassignedQuestions > 99 ? '99+' : $unassignedQuestions ?>
                    </span>
                </a>
                <?php endif; ?>

                <?php if ($urlAjustes): ?>
                <a href="<?= $urlAjustes ?>" class="tb-btn hidden sm:flex" title="Configuración">
                    <i data-lucide="settings" class="ic ic-17"></i>
                </a>
                <?php endif; ?>

                <button type="button" id="btnPantalla" class="tb-btn hidden sm:flex" title="Pantalla completa">
                    <i data-lucide="maximize" class="ic ic-17"></i>
                </button>

                <!-- Usuario -->
                <div class="relative pl-2 lg:pl-3 border-l" style="border-color: var(--card-border)">
                    <button type="button" id="btnUsuario" class="flex items-center gap-2.5 py-1 pl-1 pr-2 rounded-xl hover:bg-slate-50 transition">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-black text-[10px] flex-shrink-0">
                            <?= mb_strtoupper(mb_substr($_SESSION['username'] ?? 'AD', 0, 2)) ?>
                        </div>
                        <div class="leading-tight text-left hidden md:block">
                            <div class="text-xs font-bold text-slate-700"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></div>
                            <div class="text-[9px] text-slate-400 font-semibold"><?= htmlspecialchars($roleLabel) ?></div>
                        </div>
                        <i data-lucide="chevron-down" class="ic ic-14 text-slate-300"></i>
                    </button>
                    <div id="menuUsuario" class="menu-flotante">
                        <?php if (can('users.view')): ?>
                        <a href="/manager/users" class="menu-item"><i data-lucide="users-round" class="ic ic-16 text-slate-300"></i>Usuarios</a>
                        <?php endif; ?>
                        <?php if ($urlAjustes): ?>
                        <a href="<?= $urlAjustes ?>" class="menu-item"><i data-lucide="settings" class="ic ic-16 text-slate-300"></i>Configuración</a>
                        <?php endif; ?>
                        <a href="/" target="_blank" class="menu-item"><i data-lucide="globe" class="ic ic-16 text-slate-300"></i>Ver sitio web</a>
                        <div class="my-1 border-t" style="border-color: var(--card-border)"></div>
                        <a href="/manager/logout" class="menu-item text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                            <i data-lucide="log-out" class="ic ic-16"></i>Cerrar sesión
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page content — natural browser scroll -->
        <main class="p-5 lg:p-7">
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
                            'success' => ['circle-check',   'text-emerald-500'],
                            'error'   => ['circle-x',       'text-red-500'],
                            'warning' => ['triangle-alert', 'text-amber-500'],
                            'info'    => ['info',           'text-blue-500'],
                        ];
                        $cls  = $colors[$type]  ?? 'bg-gray-50 border-gray-300 text-gray-800';
                        [$icono, $icoColor] = $icons[$type] ?? ['info', 'text-gray-500'];
                    ?>
                    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl border <?= $cls ?> text-sm font-medium">
                        <i data-lucide="<?= $icono ?>" class="ic <?= $icoColor ?>"></i>
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
(function () {
    // ── Índice del buscador ──
    // Se arma antes de dibujar los iconos, porque Lucide sustituye cada
    // <i data-lucide> por un <svg> y el nombre del icono se perdería.
    const secciones = [...document.querySelectorAll('#sidebar .nav-link')].map(a => ({
        texto: a.textContent.trim().replace(/\s+/g, ' '),
        href:  a.getAttribute('href'),
        icono: (a.querySelector('[data-lucide]') || { dataset: {} }).dataset.lucide || 'link'
    })).filter(s => s.texto);

    const pintarIconos = () => { if (window.lucide) { window.lucide.createIcons(); } };
    pintarIconos();

    // ── Enlace activo según la ruta ──────────────────────────────────
    const path = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(a => {
        const href = a.getAttribute('href');
        if (href === path || (path.startsWith(href) && href !== '/manager' && href !== '/')) {
            a.classList.add('active');
        }
    });

    // ── Menú: barra estrecha en escritorio, cajón en móvil ───────────
    const movil = () => window.matchMedia('(max-width: 767px)').matches;
    if (localStorage.getItem('pamelNavRail') === '1') {
        document.body.classList.add('nav-rail');
    }
    document.getElementById('btnMenu').addEventListener('click', () => {
        if (movil()) {
            document.body.classList.toggle('nav-open');
        } else {
            const estrecha = document.body.classList.toggle('nav-rail');
            localStorage.setItem('pamelNavRail', estrecha ? '1' : '0');
        }
    });

    // ── Pantalla completa ────────────────────────────────────────────
    const btnPantalla = document.getElementById('btnPantalla');
    if (btnPantalla) {
        btnPantalla.addEventListener('click', () => {
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                document.documentElement.requestFullscreen().catch(() => {});
            }
        });
        document.addEventListener('fullscreenchange', () => {
            btnPantalla.innerHTML = '<i data-lucide="' +
                (document.fullscreenElement ? 'minimize' : 'maximize') + '" class="ic ic-17"></i>';
            pintarIconos();
        });
    }

    // ── Reloj ────────────────────────────────────────────────────────
    const reloj = document.getElementById('relojVivo');
    if (reloj) {
        const pintar = () => {
            const d = new Date();
            reloj.textContent = String(d.getHours()).padStart(2, '0') + ':' +
                                String(d.getMinutes()).padStart(2, '0');
        };
        pintar();
        setInterval(pintar, 30000);
    }

    // ── Menú de usuario ──────────────────────────────────────────────
    const btnUsuario  = document.getElementById('btnUsuario');
    const menuUsuario = document.getElementById('menuUsuario');
    btnUsuario.addEventListener('click', e => {
        e.stopPropagation();
        menuUsuario.classList.toggle('abierto');
    });

    // ── Buscador: salta a las secciones del propio menú ──────────────
    const caja      = document.getElementById('adminSearch');
    const resultado = document.getElementById('searchResults');

    const sinTildes = t => t.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    let activo = -1;

    function pintarResultados() {
        const q = sinTildes(caja.value.trim());
        if (!q) { resultado.classList.remove('abierto'); return; }
        const hits = secciones.filter(s => sinTildes(s.texto).includes(q)).slice(0, 7);
        if (!hits.length) {
            resultado.innerHTML = '<p class="px-3 py-3 text-xs text-slate-400 font-medium">Sin coincidencias</p>';
        } else {
            resultado.innerHTML = hits.map((s, i) =>
                '<a href="' + s.href + '" class="menu-item' + (i === activo ? ' resaltado' : '') + '">' +
                '<i data-lucide="' + s.icono + '" class="ic ic-16 text-slate-300"></i>' + s.texto + '</a>'
            ).join('');
            pintarIconos();
        }
        resultado.classList.add('abierto');
    }

    caja.addEventListener('input', () => { activo = 0; pintarResultados(); });
    caja.addEventListener('keydown', e => {
        const enlaces = resultado.querySelectorAll('a');
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            if (!enlaces.length) return;
            activo = (activo + (e.key === 'ArrowDown' ? 1 : -1) + enlaces.length) % enlaces.length;
            pintarResultados();
        } else if (e.key === 'Enter') {
            if (enlaces[Math.max(activo, 0)]) { window.location.href = enlaces[Math.max(activo, 0)].href; }
        } else if (e.key === 'Escape') {
            caja.value = '';
            resultado.classList.remove('abierto');
            caja.blur();
        }
    });

    // Atajo: "/" enfoca el buscador
    document.addEventListener('keydown', e => {
        if (e.key === '/' && !/^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement.tagName)) {
            e.preventDefault();
            caja.focus();
        }
    });

    document.addEventListener('click', e => {
        if (!resultado.contains(e.target) && e.target !== caja) { resultado.classList.remove('abierto'); }
        if (!menuUsuario.contains(e.target)) { menuUsuario.classList.remove('abierto'); }
    });
})();
</script>
</body>
</html>
