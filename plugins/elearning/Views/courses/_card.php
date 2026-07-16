<?php
$levelLabel = ['beginner' => 'Principiante', 'intermediate' => 'Intermedio', 'advanced' => 'Avanzado'];
$levelColor = [
    'beginner'     => 'bg-emerald-100 text-emerald-700',
    'intermediate' => 'bg-blue-100 text-blue-700',
    'advanced'     => 'bg-purple-100 text-purple-700',
];
$catColors = [
    0 => 'from-blue-700 to-blue-900',
    1 => 'from-purple-700 to-purple-900',
    2 => 'from-amber-600 to-amber-800',
    3 => 'from-red-600 to-red-900',
    4 => 'from-emerald-600 to-emerald-900',
    5 => 'from-teal-600 to-teal-900',
];
$gradient   = $catColors[($course['category_id'] ?? 0) % 6];
// In the unified DB, these might need subqueries if not joined
$lessonsCount = (int)($course['lessons_count'] ?? 0);
$quizCount    = (int)($course['quizzes_count'] ?? 0);
?>
<div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-300 hover:-translate-y-1.5 flex flex-col border border-gray-100">

    <!-- Imagen / banner -->
    <a href="/courses/<?= htmlspecialchars($course['slug']) ?>" class="block relative overflow-hidden h-44 flex-shrink-0">
        <?php if (!empty($course['image'])): ?>
            <img src="<?= htmlspecialchars($course['image']) ?>"
                 alt="<?= htmlspecialchars($course['title']) ?>"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br <?= $gradient ?> flex items-center justify-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-10" style="background-image:repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 40px),repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 40px)"></div>
                <i class="fas fa-ship text-white/25 text-5xl relative z-10"></i>
                <?php if (!empty($course['category_name'])): ?>
                <div class="absolute bottom-3 left-3 bg-white/15 backdrop-blur-sm border border-white/20 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                    <?= htmlspecialchars(strtoupper($course['category_name'])) ?>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Badge nivel -->
        <div class="absolute top-3 right-3">
            <span class="text-xs font-bold px-2.5 py-1 rounded-full shadow-sm <?= $levelColor[$course['level']] ?? 'bg-gray-100 text-gray-600' ?>">
                <?= $levelLabel[$course['level']] ?? ucfirst($course['level'] ?? '') ?>
            </span>
        </div>
    </a>

    <!-- Contenido -->
    <div class="p-5 flex flex-col flex-1">
        <?php if (!empty($course['category_name'])): ?>
        <span class="text-xs font-bold text-brand-500 uppercase tracking-wider mb-1.5">
            <?= htmlspecialchars($course['category_name']) ?>
        </span>
        <?php endif; ?>

        <a href="/courses/<?= htmlspecialchars($course['slug']) ?>">
            <h3 class="font-black text-navy text-sm leading-snug hover:text-brand-500 transition-colors line-clamp-2 mb-2">
                <?= htmlspecialchars($course['title']) ?>
            </h3>
        </a>

        <?php if (!empty($course['teacher_name'])): ?>
        <div class="flex items-center gap-2 mb-4">
            <div class="w-5 h-5 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-xs font-black flex-shrink-0">
                <?= mb_strtoupper(mb_substr($course['teacher_name'], 0, 1)) ?>
            </div>
            <p class="text-xs text-gray-500 font-semibold truncate"><?= htmlspecialchars($course['teacher_name']) ?></p>
        </div>
        <?php endif; ?>

        <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
            <span class="text-brand-600 font-black">$<?= number_format($course['price'], 2) ?></span>
            <a href="/courses/<?= htmlspecialchars($course['slug']) ?>"
               class="flex items-center justify-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-md shadow-brand-500/20">
                <i class="fas fa-eye"></i>
                Ver Curso
            </a>
        </div>
    </div>
</div>
