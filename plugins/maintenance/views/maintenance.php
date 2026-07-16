<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Under Maintenance — PAMEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        * { font-family: 'Poppins', sans-serif; }

        .gear-spin  { animation: spin 10s linear infinite; }
        .gear-spin2 { animation: spin  6s linear infinite reverse; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .bar-anim {
            background: linear-gradient(90deg, #0284c7 0%, #38bdf8 50%, #0284c7 100%);
            background-size: 200% 100%;
            animation: shimmer 2s linear infinite;
        }
        @keyframes shimmer {
            0%   { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .divider-line {
            background: linear-gradient(90deg, transparent, #cbd5e1, transparent);
        }
    </style>
</head>
<body class="min-h-screen bg-white flex flex-col">

    <!-- Top accent bar -->
    <div class="h-1 w-full bg-gradient-to-r from-cyan-500 via-blue-600 to-cyan-500"></div>

    <!-- Main content -->
    <div class="flex-1 flex flex-col items-center justify-center px-6 py-16 text-center">

        <!-- Logo — grande y centrado -->
        <div class="mb-10">
            <img src="/assets/images/logo.png"
                 alt="PAMEL"
                 class="h-40 w-auto mx-auto"
                 onerror="this.replaceWith(Object.assign(document.createElement('span'), {className:'text-5xl font-black text-slate-800', textContent:'PAMEL'}))">
        </div>

        <!-- Gears -->
        <div class="flex justify-center items-end gap-1 mb-5 text-blue-300">
            <i class="fas fa-cog text-4xl gear-spin"></i>
            <i class="fas fa-cog text-2xl gear-spin2 mb-0.5"></i>
        </div>

        <!-- Heading -->
        <h1 class="text-4xl md:text-5xl font-bold text-slate-800 mb-2">
            We'll Be Right Back
        </h1>
        <p class="text-blue-600 font-semibold text-xs uppercase tracking-widest mb-6">
            Scheduled Maintenance
        </p>

        <!-- Divider -->
        <div class="divider-line h-px w-48 mx-auto mb-6"></div>

        <!-- Message -->
        <p class="text-slate-500 text-sm leading-relaxed max-w-md mx-auto mb-6">
            <?php echo htmlspecialchars($message ?? 'We are performing scheduled maintenance to improve your experience. The site will be back online shortly.'); ?>
        </p>

        <!-- Return time badge -->
        <?php if (!empty($returnTime)): ?>
        <div class="inline-flex items-center gap-2 border border-slate-200 text-slate-600 text-sm rounded-full px-5 py-2 mb-6 bg-slate-50">
            <i class="fas fa-clock text-blue-500"></i>
            Expected return: <strong class="text-slate-800 ml-1"><?php echo htmlspecialchars($returnTime); ?></strong>
        </div>
        <?php endif; ?>

        <!-- Progress bar -->
        <div class="bg-slate-100 rounded-full h-1.5 w-64 overflow-hidden mb-8">
            <div class="bar-anim h-full rounded-full w-2/3"></div>
        </div>

        <!-- Feature pills -->
        <div class="flex justify-center gap-3 flex-wrap mb-10">
            <span class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 text-slate-500 text-xs px-4 py-2 rounded-full">
                <i class="fas fa-tools text-blue-400"></i> Updates
            </span>
            <span class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 text-slate-500 text-xs px-4 py-2 rounded-full">
                <i class="fas fa-shield-alt text-blue-400"></i> Security
            </span>
            <span class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 text-slate-500 text-xs px-4 py-2 rounded-full">
                <i class="fas fa-bolt text-blue-400"></i> Performance
            </span>
        </div>

        <!-- Contact -->
        <p class="text-slate-400 text-xs">
            Need urgent assistance?
            <a href="mailto:info@pamel.edu.pa" class="text-blue-500 hover:text-blue-700 transition underline underline-offset-2">
                info@pamel.edu.pa
            </a>
        </p>

    </div>

    <!-- Bottom footer -->
    <div class="border-t border-slate-100 py-4 text-center">
        <p class="text-slate-300 text-xs">
            &copy; <?php echo date('Y'); ?> PAMEL — Maritime Training Center
        </p>
    </div>

</body>
</html>
