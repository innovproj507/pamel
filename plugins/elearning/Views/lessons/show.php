<?php
$currentIndex   = array_search($lesson['id'], array_column($allLessons, 'id'));
$prevLesson     = $allLessons[$currentIndex - 1] ?? null;
$nextLesson     = $allLessons[$currentIndex + 1] ?? null;
$totalActive    = count(array_filter($allLessons, fn($l) => $l['is_active']));
$wordCount      = str_word_count(strip_tags($lesson['content'] ?? ''));
$readingMinutes = max(1, (int) ceil($wordCount / 200));
?>

<div id="reading-bar" class="fixed top-0 left-0 right-0 z-50 h-1 bg-gray-200">
    <div id="reading-progress" class="h-1 bg-gradient-to-r from-brand-500 to-blue-400 transition-all duration-100" style="width:0%"></div>
</div>

<style>
.lesson-body h1,.lesson-body h2{font-size:1.35rem;font-weight:900;color:#0f172a;margin:2rem 0 .75rem;padding-bottom:.5rem;border-bottom:2px solid #e2e8f0;}
.lesson-body p{margin:.85rem 0;line-height:1.85;color:#374151;font-size:.95rem;}
.lesson-body img{max-width:100%;height:auto;border-radius:.75rem;margin:1rem auto;display:block;}
</style>

<div class="max-w-7xl mx-auto px-4 py-8">

    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="/courses" class="hover:text-brand-500 font-semibold transition-colors">Catálogo</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="/courses/<?= htmlspecialchars($course['slug']) ?>" class="hover:text-brand-500 font-semibold transition-colors truncate max-w-xs"><?= htmlspecialchars($course['title']) ?></a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-navy font-semibold truncate"><?= htmlspecialchars($lesson['title']) ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-5">
            <div class="rounded-2xl overflow-hidden shadow-lg" style="background:linear-gradient(135deg,#0a1628 0%,#1a3a6b 60%,#1a56db 100%);">
                <div class="p-7">
                    <p class="text-blue-300 text-xs font-bold uppercase tracking-widest mb-2"><?= htmlspecialchars($course['title']) ?></p>
                    <h1 class="text-2xl font-black text-white leading-snug mb-4"><?= htmlspecialchars($lesson['title']) ?></h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full bg-white/15 text-white backdrop-blur-sm">
                            <i class="fas <?= $lesson['type'] === 'video' ? 'fa-play-circle' : ($lesson['type'] === 'file' ? 'fa-paperclip' : 'fa-file-alt') ?>"></i>
                            <?= ucfirst($lesson['type']) ?>
                        </span>
                        <?php if ($isCompleted): ?>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full bg-emerald-500/40 text-emerald-100">
                            <i class="fas fa-check"></i> Completada
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($lesson['type'] === 'video' && $lesson['video_url']): ?>
            <div class="aspect-video rounded-2xl overflow-hidden bg-navy shadow-xl">
                <?php
                $url = $lesson['video_url'];
                if (preg_match('/youtube\.com\/watch\?v=([^&]+)|youtu\.be\/([^?]+)/', $url, $m)) {
                    $vid = $m[1] ?: $m[2];
                    echo "<iframe src='https://www.youtube.com/embed/{$vid}?rel=0' class='w-full h-full' allowfullscreen></iframe>";
                } else {
                    echo "<div class='flex items-center justify-center h-full text-white p-4 text-center'><a href='".htmlspecialchars($url)."' target='_blank' class='font-bold hover:underline'>Ver vídeo externo <i class='fas fa-external-link-alt ml-1'></i></a></div>";
                }
                ?>
            </div>
            <?php endif; ?>

            <div class="lesson-body bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <?= $lesson['content'] ?>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex items-center justify-between gap-4">
                <?php if ($prevLesson): ?>
                <a href="/courses/<?= $course['id'] ?>/lessons/<?= $prevLesson['id'] ?>" class="flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-brand-500 transition-colors">
                    <i class="fas fa-arrow-left"></i> Anterior
                </a>
                <?php else: ?><div></div><?php endif; ?>

                <?php if (!$isCompleted): ?>
                <form method="POST" action="/courses/<?= $course['id'] ?>/lessons/<?= $lesson['id'] ?>/complete">
                    <?= \Core\Security::getCsrfField() ?>
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-all shadow-lg">
                        Completar Lección
                    </button>
                </form>
                <?php else: ?>
                <span class="text-emerald-600 font-bold"><i class="fas fa-check-circle"></i> Lección completada</span>
                <?php endif; ?>

                <?php if ($nextLesson): ?>
                <a href="/courses/<?= $course['id'] ?>/lessons/<?= $nextLesson['id'] ?>" class="flex items-center gap-2 text-sm font-bold text-brand-500 hover:text-brand-600 transition-colors">
                    Siguiente <i class="fas fa-arrow-right"></i>
                </a>
                <?php else: ?><div></div><?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="sticky top-8 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-lg p-5">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-wider mb-4">Contenido del Curso</p>
                    <ul class="space-y-2 max-h-96 overflow-y-auto">
                        <?php foreach ($allLessons as $i => $ls): 
                            $lsActive = $ls['id'] == $lesson['id'];
                            $lsDone = in_array((int)$ls['id'], $completedLessonIds);
                        ?>
                        <li>
                            <a href="/courses/<?= $course['id'] ?>/lessons/<?= $ls['id'] ?>" 
                               class="flex items-center gap-3 p-3 rounded-xl transition-all <?= $lsActive ? 'bg-brand-50 border-l-4 border-brand-500' : 'hover:bg-gray-50' ?>">
                                <span class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold <?= $lsDone ? 'bg-emerald-100 text-emerald-600' : ($lsActive ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-400') ?>">
                                    <?php if ($lsDone): ?><i class="fas fa-check text-[10px]"></i><?php else: ?><?= $i + 1 ?><?php endif; ?>
                                </span>
                                <span class="text-xs font-semibold truncate <?= $lsActive ? 'text-brand-700' : 'text-gray-600' ?>"><?= htmlspecialchars($ls['title']) ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.addEventListener('scroll', () => {
    const doc  = document.documentElement;
    const pct  = (doc.scrollTop / (doc.scrollHeight - doc.clientHeight)) * 100;
    document.getElementById('reading-progress').style.width = Math.min(pct, 100) + '%';
});
</script>
