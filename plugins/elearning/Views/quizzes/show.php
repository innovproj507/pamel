<?php
$total     = count($quiz['questions']);
$timeLimit = (int)($quiz['time_limit'] ?? 0);
?>
<style>
.option-label { transition: all .15s ease; }
.option-label:has(input:checked) {
    border-color: #1a56db !important;
    background: #eff6ff !important;
}
.option-label:has(input:checked) .option-letter {
    background: #1a56db !important;
    color: #fff !important;
}
.q-card { animation: qIn .35s ease both; }
@keyframes qIn { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
</style>

<!-- Barra de progreso del quiz (sticky) -->
<div class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-3xl mx-auto px-4 py-2 flex items-center gap-4">
        <a href="/courses/<?= htmlspecialchars($course['slug']) ?>"
           class="text-xs font-bold text-gray-400 hover:text-brand-500 transition-colors flex items-center gap-1 flex-shrink-0">
            <i class="fas fa-times text-[10px]"></i> Salir
        </a>
        <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
            <div id="quiz-progress-bar" class="h-2 bg-gradient-to-r from-brand-500 to-blue-400 rounded-full transition-all duration-500" style="width:0%"></div>
        </div>
        <span id="quiz-progress-text" class="text-xs font-black text-navy flex-shrink-0">0/<?= $total ?></span>
        <?php if ($timeLimit > 0): ?>
        <span id="timer" class="text-xs font-black text-amber-600 bg-amber-50 px-2 py-1 rounded-lg flex-shrink-0">
            <?= sprintf('%02d:%02d', intdiv($timeLimit * 60, 60), ($timeLimit * 60) % 60) ?>
        </span>
        <?php endif; ?>
    </div>
</div>

<div class="max-w-3xl mx-auto pt-16 pb-10">

    <!-- Header del quiz -->
    <div class="rounded-3xl overflow-hidden mb-8 shadow-lg" style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 50%,#4f46e5 100%);">
        <div class="p-7">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center text-3xl flex-shrink-0 border border-white/20">📝</div>
                <div class="flex-1 min-w-0">
                    <p class="text-indigo-300 text-xs font-bold uppercase tracking-widest mb-1"><?= htmlspecialchars($course['title']) ?></p>
                    <h1 class="text-xl font-black text-white mb-3"><?= htmlspecialchars($quiz['title']) ?></h1>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black bg-white/15 text-white px-3 py-1.5 rounded-full uppercase tracking-tighter">
                            Aprobación: <?= $quiz['pass_percentage'] ?>%
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black bg-white/15 text-white px-3 py-1.5 rounded-full uppercase tracking-tighter">
                            <?= $total ?> preguntas
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($bestAttempt): ?>
            <div class="mt-5 flex items-center gap-3 p-4 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/15">
                <span class="text-2xl"><?= $bestAttempt['passed'] ? '🏆' : '📖' ?></span>
                <div>
                    <p class="text-sm font-black text-white">
                        <?= $bestAttempt['passed'] ? 'Ya lo aprobaste — puedes intentarlo de nuevo' : 'Aún no has aprobado' ?>
                    </p>
                    <p class="text-xs text-white/60 font-semibold">
                        Mejor puntaje: <strong class="text-white"><?= $bestAttempt['score'] ?>%</strong> · Mínimo: <?= $quiz['pass_percentage'] ?>%
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Formulario -->
    <form method="POST" action="/courses/<?= $course['id'] ?>/quizzes/<?= $quiz['id'] ?>/submit" id="quiz-form">
        <?= \Core\Security::getCsrfField() ?>

        <div class="space-y-6">
            <?php foreach ($quiz['questions'] as $i => $q): ?>
            <div class="q-card bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" style="animation-delay:<?= $i * 0.06 ?>s" data-question="<?= $i ?>">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-brand-500 to-blue-700 text-white text-xs font-black flex items-center justify-center flex-shrink-0 shadow-md"><?= $i + 1 ?></span>
                    <p class="text-navy font-bold text-sm leading-relaxed flex-1"><?= htmlspecialchars($q['question']) ?></p>
                </div>

                <div class="p-4 space-y-2">
                    <?php
                    $letters = ['A','B','C','D','E','F'];
                    foreach ($q['options'] as $oi => $opt):
                    ?>
                    <label class="option-label flex items-center gap-3 p-3.5 rounded-xl border-2 border-gray-100 hover:border-brand-200 hover:bg-brand-50/30 cursor-pointer">
                        <span class="option-letter w-7 h-7 rounded-lg bg-gray-100 text-gray-500 text-xs font-black flex items-center justify-center flex-shrink-0 transition-all">
                            <?= $letters[$oi] ?? ($oi + 1) ?>
                        </span>
                        <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $opt['id'] ?>" required class="sr-only" onchange="updateProgress()">
                        <span class="text-sm text-gray-700 font-semibold leading-snug"><?= htmlspecialchars($opt['option_text']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Submit -->
        <div class="mt-8 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row items-center gap-4 justify-between">
            <div class="text-xs text-gray-500 font-bold uppercase tracking-wider">
                <span id="answered-count" class="text-navy font-black">0</span> / <?= $total ?> Respondidas
            </div>
            <button type="submit" id="submit-btn" class="w-full sm:w-auto bg-brand-500 hover:bg-brand-600 text-white font-black px-8 py-3.5 rounded-xl text-sm transition-all shadow-lg">
                Enviar Respuestas
            </button>
        </div>
    </form>
</div>

<script>
const totalQ = <?= $total ?>;
function updateProgress() {
    const answered = document.querySelectorAll('input[type=radio]:checked').length;
    const pct = Math.round((answered / totalQ) * 100);
    document.getElementById('quiz-progress-bar').style.width = pct + '%';
    document.getElementById('quiz-progress-text').textContent = answered + '/' + totalQ;
    document.getElementById('answered-count').textContent = answered;
}

<?php if ($timeLimit > 0): ?>
let seconds = <?= $timeLimit * 60 ?>;
const timerEl = document.getElementById('timer');
const interval = setInterval(() => {
    seconds--;
    if (seconds <= 0) {
        clearInterval(interval);
        document.getElementById('quiz-form').submit();
        return;
    }
    const m = String(Math.floor(seconds / 60)).padStart(2,'0');
    const s = String(seconds % 60).padStart(2,'0');
    timerEl.textContent = m + ':' + s;
    if (seconds <= 60) {
        timerEl.classList.remove('text-amber-600','bg-amber-50');
        timerEl.classList.add('text-red-600','bg-red-50');
    }
}, 1000);
<?php endif; ?>

document.getElementById('quiz-form').addEventListener('submit', function(e) {
    const answered = document.querySelectorAll('input[type=radio]:checked').length;
    if (answered < totalQ) {
        if (!confirm(`Tienes ${totalQ - answered} pregunta(s) sin responder. ¿Enviar de todas formas?`)) {
            e.preventDefault();
        }
    }
});
</script>
