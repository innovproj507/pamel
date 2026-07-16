<?php
$totalEnrolled  = count($enrollments);
$totalCompleted = count(array_filter($enrollments, fn($e) => $e['total_lessons'] > 0 && $e['completed_lessons'] >= $e['total_lessons']));
$totalLessonsOk = array_sum(array_column($enrollments, 'completed_lessons'));
$totalLessons   = array_sum(array_column($enrollments, 'total_lessons'));
$overallPct     = $totalLessons > 0 ? round(($totalLessonsOk / $totalLessons) * 100) : 0;
$inProgress     = $totalEnrolled - $totalCompleted;
?>

<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-navy">¡Hola, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>!</h1>
            <p class="text-gray-500 text-sm mt-1">Continúa tu formación marítima desde donde la dejaste.</p>
        </div>
        <a href="/courses" class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-brand-500/25 hover:-translate-y-0.5">
            <i class="fas fa-search"></i> Explorar Cursos
        </a>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        <aside class="w-full lg:w-64 flex-shrink-0 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-6">
                <h3 class="font-black text-navy text-xs mb-4 uppercase tracking-wider">Mi Progreso</h3>

                <div class="flex justify-center mb-5">
                    <div class="relative w-28 h-28">
                        <svg class="w-28 h-28 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e2e8f0" stroke-width="2.5"/>
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#1a56db" stroke-width="2.5"
                                    stroke-dasharray="<?= round($overallPct * 99.9 / 100, 1) ?> 100"
                                    stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-black text-navy"><?= $overallPct ?>%</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">General</span>
                        </div>
                    </div>
                </div>

                <ul class="space-y-1">
                    <li class="flex items-center justify-between text-xs px-3 py-2 rounded-xl bg-blue-50">
                        <span class="font-semibold text-blue-700">Inscritos</span>
                        <span class="font-black text-blue-700"><?= $totalEnrolled ?></span>
                    </li>
                    <li class="flex items-center justify-between text-xs px-3 py-2 rounded-xl">
                        <span class="font-semibold text-gray-600">En progreso</span>
                        <span class="font-black text-amber-600"><?= $inProgress ?></span>
                    </li>
                    <li class="flex items-center justify-between text-xs px-3 py-2 rounded-xl">
                        <span class="font-semibold text-gray-600">Completados</span>
                        <span class="font-black text-emerald-600"><?= $totalCompleted ?></span>
                    </li>
                </ul>

                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="<?= rtrim(\Core\Config::get('site.url'), '/') ?>/account" class="flex items-center justify-between text-xs px-3 py-2 rounded-xl font-bold text-gray-500 hover:bg-slate-50 transition-colors">
                        <span>Mi Perfil CMS</span>
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </aside>

        <div class="flex-1 min-w-0">
            <?php if (empty($enrollments)): ?>
            <div class="py-20 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
                <i class="fas fa-ship text-5xl text-gray-200 mb-4"></i>
                <p class="text-gray-500 font-semibold text-lg mb-2">Aún no estás inscrito en ningún curso</p>
                <a href="/courses" class="inline-flex items-center gap-2 bg-brand-500 text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-brand-600 transition-all">
                    Explorar Cursos
                </a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($enrollments as $e):
                    $total     = (int) $e['total_lessons'];
                    $completed = (int) $e['completed_lessons'];
                    $pct       = $total > 0 ? round(($completed / $total) * 100) : 0;
                    $isDone    = $pct >= 100;
                ?>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl hover:border-brand-100 transition-all hover:-translate-y-1 flex flex-col">
                    <div class="relative h-40 overflow-hidden flex-shrink-0" style="background:linear-gradient(135deg,#1a3a6b 0%,#0f4c8a 100%);">
                        <?php if ($e['image']): ?>
                            <img src="<?= htmlspecialchars($e['image']) ?>" class="w-full h-full object-cover" alt="">
                        <?php else: ?>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white/20 text-5xl"></i>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-3 right-3">
                            <span class="text-[10px] font-black px-2 py-1 rounded-full shadow-sm <?= $isDone ? 'bg-emerald-500 text-white' : ($pct > 0 ? 'bg-amber-400 text-amber-900' : 'bg-white/20 text-white backdrop-blur-sm') ?>">
                                <?= $isDone ? 'COMPLETADO' : ($pct > 0 ? 'EN PROGRESO' : 'DISPONIBLE') ?>
                            </span>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/20">
                            <div class="h-1 <?= $isDone ? 'bg-emerald-400' : 'bg-brand-400' ?> transition-all" style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>

                    <div class="p-5 flex flex-col flex-1">
                        <a href="/courses/<?= htmlspecialchars($e['slug']) ?>" class="font-black text-navy text-sm leading-snug hover:text-brand-500 transition-colors line-clamp-2 mb-2">
                            <?= htmlspecialchars($e['title']) ?>
                        </a>
                        <div class="flex items-center gap-2 mb-3">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full <?= $isDone ? 'bg-emerald-500' : 'bg-brand-500' ?>" style="width:<?= $pct ?>%"></div>
                            </div>
                            <span class="text-[10px] font-black <?= $isDone ? 'text-emerald-600' : 'text-brand-500' ?>"><?= $pct ?>%</span>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold mb-4"><?= $completed ?> / <?= $total ?> LECCIONES</p>

                        <div class="mt-auto space-y-2">
                            <a href="/courses/<?= htmlspecialchars($e['slug']) ?>" class="block w-full text-center py-2.5 rounded-xl text-xs font-black transition-all <?= $isDone ? 'bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100' : 'bg-brand-500 text-white hover:bg-brand-600 shadow-md shadow-brand-500/20' ?>">
                                <?= $isDone ? 'REVISAR CURSO' : 'CONTINUAR' ?>
                            </a>
                            <?php if ($isDone): ?>
                            <a href="/cursos/<?= htmlspecialchars($e['slug']) ?>/certificado" class="block w-full text-center py-2.5 rounded-xl text-xs font-black bg-emerald-500 text-white hover:bg-emerald-600 transition-all shadow-md shadow-emerald-500/20">
                                <i class="fas fa-certificate mr-1"></i> DESCARGAR CERTIFICADO
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
