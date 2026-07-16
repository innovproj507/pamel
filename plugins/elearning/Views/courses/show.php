<?php
$isManager   = $user && (
    $user['role'] === 'admin' ||
    ($user['role'] === 'teacher' && (int)$course['teacher_id'] === (int)$user['id'])
);
$lessons     = is_array($lessons  ?? null) ? $lessons  : [];
$quizzes     = is_array($quizzes  ?? null) ? $quizzes  : [];
$lessonCount = count($lessons);
$quizCount   = count($quizzes);
?>
<div class="max-w-7xl mx-auto px-4 py-10">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
        <a href="/courses" class="hover:text-brand-500 font-semibold transition-colors">Catálogo</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-navy font-semibold truncate"><?= htmlspecialchars($course['title']) ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Columna principal -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Cabecera del curso -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="h-52 relative overflow-hidden" style="background:linear-gradient(135deg,#0a1628 0%,#1a3a6b 60%,#1a56db 100%);">
                    <?php if ($course['image']): ?>
                    <img src="<?= htmlspecialchars($course['image']) ?>" class="w-full h-full object-cover opacity-40">
                    <?php endif; ?>
                    <div class="absolute inset-0 flex flex-col justify-end p-6">
                        <?php if (!empty($course['category_name'])): ?>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-white/20 text-white border border-white/30 px-3 py-1 rounded-full w-fit mb-3 backdrop-blur-sm">
                            <?= htmlspecialchars($course['category_name']) ?>
                        </span>
                        <?php endif; ?>
                        <h1 class="text-2xl font-black text-white leading-tight">
                            <?= htmlspecialchars($course['title']) ?>
                        </h1>
                    </div>
                </div>

                <div class="px-6 py-4 flex flex-wrap items-center gap-4 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-blue-700 flex items-center justify-center text-white font-black text-xs">
                            <?= mb_strtoupper(mb_substr($course['teacher_name'] ?? 'P', 0, 1)) ?>
                        </div>
                        <span class="text-sm text-gray-600">
                            Por <strong class="text-navy"><?= htmlspecialchars($course['teacher_name'] ?? 'PAMEL') ?></strong>
                        </span>
                    </div>
                    <?php
                    $levelColors = ['beginner'=>'bg-emerald-100 text-emerald-700','intermediate'=>'bg-amber-100 text-amber-700','advanced'=>'bg-red-100 text-red-700'];
                    $levelLabels = ['beginner'=>'Principiante','intermediate'=>'Intermedio','advanced'=>'Avanzado'];
                    $lvl = $course['level'] ?? 'beginner';
                    ?>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full <?= $levelColors[$lvl] ?? 'bg-gray-100 text-gray-600' ?>">
                        <?= $levelLabels[$lvl] ?? ucfirst($lvl) ?>
                    </span>
                    <span class="text-xs text-gray-400 font-semibold ml-auto">
                        <?= $lessonCount ?> lecciones &bull; <?= $quizCount ?> evaluaciones
                    </span>
                </div>
            </div>

            <!-- Descripción -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-black text-navy text-lg mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-brand-50 flex items-center justify-center text-brand-500 text-sm">📄</span>
                    Acerca del Curso
                </h2>
                <div class="text-gray-600 text-sm leading-relaxed space-y-3">
                    <?= nl2br(htmlspecialchars($course['description'] ?? '')) ?>
                </div>
            </div>

            <!-- Lecciones -->
            <?php if (!empty($lessons)): ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-black text-navy text-lg mb-5">
                    Contenido del Curso
                    <span class="text-sm font-semibold text-gray-400 ml-2"><?= $lessonCount ?> lecciones</span>
                </h2>
                <ul class="space-y-2">
                    <?php
                    $prevCompleted = true;
                    foreach ($lessons as $i => $lesson):
                        $isCompleted  = \in_array((int)$lesson['id'], $completedLessonIds ?? []);
                        $isDisabled   = !$lesson['is_active'];
                        $isLocked     = !$isManager && $enrolled && !$isDisabled && !$prevCompleted;
                        $canAccess    = ($enrolled || $isManager) && !$isLocked && !$isDisabled;
                        if ($lesson['is_active']) $prevCompleted = $isCompleted;
                    ?>
                    <li class="flex items-center gap-3 p-3 rounded-xl border transition-all
                        <?= $isDisabled ? 'opacity-50' : ($isCompleted ? 'border-emerald-100 bg-emerald-50/30' : ($isLocked ? 'border-gray-100 bg-gray-50/50' : 'border-gray-100 hover:bg-slate-50 hover:border-brand-100')) ?>">
                        <span class="w-8 h-8 rounded-xl text-xs font-black flex items-center justify-center flex-shrink-0
                            <?= $isCompleted ? 'bg-emerald-100 text-emerald-600' : ($isLocked || $isDisabled ? 'bg-gray-100 text-gray-400' : 'bg-brand-50 text-brand-500') ?>">
                            <?php if ($isCompleted): ?>
                                <i class="fas fa-check"></i>
                            <?php elseif ($isLocked): ?>
                                <i class="fas fa-lock"></i>
                            <?php else: ?>
                                <?= $i + 1 ?>
                            <?php endif; ?>
                        </span>
                        <?php if ($canAccess): ?>
                        <a href="/courses/<?= $course['id'] ?>/lessons/<?= $lesson['id'] ?>"
                           class="flex-1 text-sm font-semibold min-w-0 truncate transition-colors
                               <?= $isCompleted ? 'text-emerald-700 hover:text-emerald-600' : 'text-navy hover:text-brand-500' ?>">
                            <?= htmlspecialchars($lesson['title']) ?>
                        </a>
                        <?php else: ?>
                        <span class="flex-1 text-sm font-semibold text-gray-400 min-w-0 truncate">
                            <?= htmlspecialchars($lesson['title']) ?>
                        </span>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-lg p-6">
                    <div class="py-5 border-b border-gray-100">
                        <?php if ($enrolled): ?>
                        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl mb-4 font-bold">
                            <i class="fas fa-check-circle"></i>
                            Inscripción activa
                        </div>
                        <?php if (!empty($lessons)): ?>
                        <a href="/courses/<?= $course['id'] ?>/lessons/<?= $lessons[0]['id'] ?>"
                           class="block w-full text-center bg-brand-500 hover:bg-brand-600 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-brand-500/25 hover:-translate-y-0.5">
                            Continuar Curso →
                        </a>
                        <?php endif; ?>
                        <?php else: ?>
                        <form method="POST" action="/courses/<?= $course['id'] ?>/enroll">
                            <?= \Core\Security::getCsrfField() ?>
                            <button type="submit" class="block w-full text-center bg-brand-500 hover:bg-brand-600 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-brand-500/25 hover:-translate-y-0.5 mb-3">
                                Inscribirse Ahora
                            </button>
                        </form>
                        <p class="text-xs text-center text-gray-400 mt-2">Acceso inmediato al contenido</p>
                        <?php endif; ?>
                    </div>

                    <ul class="pt-5 space-y-3">
                        <li class="flex items-center gap-3 text-sm text-gray-600 font-semibold">
                            <span class="text-base w-6 text-center">📚</span>
                            <?= $lessonCount ?> lecciones
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600 font-semibold">
                            <span class="text-base w-6 text-center">📝</span>
                            <?= $quizCount ?> evaluaciones
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600 font-semibold">
                            <span class="text-base w-6 text-center">🏆</span>
                            Certificado al completar
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
