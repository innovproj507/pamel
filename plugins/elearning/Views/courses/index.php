<style>
.card-entry { animation: cardIn .4s ease both; }
@keyframes cardIn { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
.card-entry:nth-child(2) { animation-delay:.05s; }
.card-entry:nth-child(3) { animation-delay:.1s; }
.card-entry:nth-child(4) { animation-delay:.15s; }
.card-entry:nth-child(5) { animation-delay:.2s; }
.card-entry:nth-child(6) { animation-delay:.25s; }
</style>

<div class="max-w-7xl mx-auto px-4 py-10">

    <!-- Hero header -->
    <div class="relative rounded-3xl overflow-hidden mb-10 shadow-xl"
         style="background:linear-gradient(135deg,#0a1628 0%,#1a3a6b 55%,#1a56db 100%);">
        <div class="absolute inset-0 opacity-10" style="background-image:repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 50px),repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 50px)"></div>
        <div class="relative z-10 px-8 py-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <p class="text-blue-300 text-xs font-bold uppercase tracking-widest mb-2">PAMEL Maritime Training Center</p>
                <h1 class="text-3xl font-black text-white mb-2">Catálogo de Cursos</h1>
                <p class="text-blue-200 text-sm">Formación marítima certificada · Estándares IMO / STCW</p>
                <div class="flex flex-wrap gap-4 mt-4">
                    <div class="flex items-center gap-2 text-white/80 text-xs font-semibold">
                        <i class="fas fa-book-open text-blue-300"></i>
                        <?= $courses['total'] ?> cursos disponibles
                    </div>
                    <div class="flex items-center gap-2 text-white/80 text-xs font-semibold">
                        <i class="fas fa-check-circle text-blue-300"></i>
                        Certificación IMO reconocida
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0">
                <div class="w-20 h-20 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-th-large text-white/60 text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        <!-- Sidebar filtros -->
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-6">
                <h3 class="font-black text-navy text-xs uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="fas fa-filter text-brand-500"></i>
                    Categorías
                </h3>
                <ul class="space-y-1">
                    <li>
                        <a href="/courses"
                           class="flex items-center justify-between text-sm px-3 py-2 rounded-xl font-semibold transition-all
                                  <?= !$currentCat ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/25' : 'text-gray-600 hover:bg-slate-50' ?>">
                            <span>Todos los cursos</span>
                            <span class="text-xs <?= !$currentCat ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' ?> font-bold px-1.5 py-0.5 rounded-md"><?= $courses['total'] ?></span>
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="/courses?categoria=<?= $cat['id'] ?>"
                           class="flex items-center justify-between text-sm px-3 py-2 rounded-xl font-semibold transition-all
                                  <?= $currentCat == $cat['id'] ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/25' : 'text-gray-600 hover:bg-slate-50' ?>">
                            <span class="truncate"><?= htmlspecialchars($cat['name']) ?></span>
                            <span class="text-xs <?= $currentCat == $cat['id'] ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' ?> font-bold px-1.5 py-0.5 rounded-md flex-shrink-0 ml-1"><?= $cat['course_count'] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <!-- Listado de cursos -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-5">
                <p class="text-sm text-gray-500 font-semibold">
                    Mostrando <span class="text-navy font-black"><?= count($courses['data']) ?></span> de <span class="text-navy font-black"><?= $courses['total'] ?></span> cursos
                </p>
            </div>

            <?php if (empty($courses['data'])): ?>
            <div class="py-20 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center text-3xl mx-auto mb-4">🔍</div>
                <p class="text-gray-500 font-bold mb-4">No hay cursos en esta categoría.</p>
                <a href="/courses" class="text-brand-500 hover:text-brand-600 font-bold text-sm">Ver todos los cursos →</a>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <?php foreach ($courses['data'] as $course): ?>
                <div class="card-entry">
                    <?php include __DIR__ . '/_card.php'; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginación -->
            <?php if ($courses['last_page'] > 1): ?>
            <div class="flex justify-center gap-2 mt-10">
                <?php for ($p = 1; $p <= $courses['last_page']; $p++): ?>
                <a href="/courses?page=<?= $p ?><?= $currentCat ? '&categoria=' . $currentCat : '' ?>"
                   class="w-9 h-9 flex items-center justify-center rounded-xl text-sm font-bold transition-all
                          <?= $p === $courses['current_page']
                              ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/25'
                              : 'bg-white text-gray-600 hover:bg-slate-50 border border-gray-200' ?>">
                    <?= $p ?>
                </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
