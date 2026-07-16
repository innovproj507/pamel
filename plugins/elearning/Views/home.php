<!-- HERO -->
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, #0a1628 0%, #0f2d5a 45%, #0a3d7a 100%);">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full opacity-10" style="background:radial-gradient(circle, #f59e0b, transparent)"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 rounded-full opacity-10" style="background:radial-gradient(circle, #1a56db, transparent)"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 py-28 lg:py-36">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-flex items-center gap-2 bg-gold/20 border border-gold/30 text-gold text-xs font-bold px-4 py-2 rounded-full mb-6 uppercase tracking-widest">
                    <i class="fas fa-star"></i>
                    Certificaciones IMO Reconocidas
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-tight mb-6">
                    PAMEL<br>
                    <span style="color:#f59e0b">E-LEARNING</span><br>
                    <span class="text-3xl md:text-4xl font-bold text-blue-200">CURSOS MARÍTIMOS</span>
                </h1>

                <p class="text-blue-100 text-lg leading-relaxed mb-8 max-w-lg">
                    Formación marítima profesional basada en competencias con certificaciones internacionalmente reconocidas. Avanza en tu carrera con PAMEL.
                </p>

                <div class="flex flex-wrap gap-4">
                    <a href="/courses" class="inline-flex items-center gap-2 bg-gold hover:bg-gold-dark text-white font-bold px-7 py-3.5 rounded-xl transition-all shadow-xl shadow-yellow-500/30 hover:shadow-yellow-500/50 hover:-translate-y-0.5 text-sm uppercase tracking-wide">
                        <i class="fas fa-book-open"></i>
                        Ver Cursos
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="<?= rtrim(\Core\Config::get('site.url'), '/') ?>/register" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold px-7 py-3.5 rounded-xl transition-all text-sm uppercase tracking-wide">
                        Crear Cuenta Gratis
                    </a>
                    <?php else: ?>
                    <a href="/dashboard" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/30 text-white font-bold px-7 py-3.5 rounded-xl transition-all text-sm uppercase tracking-wide">
                        Ir a Mi Dashboard →
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="hidden lg:flex flex-col gap-4">
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-4 text-center">
                        <p class="text-2xl font-black text-gold"><?= $heroStats['courses'] ?>+</p>
                        <p class="text-blue-200 text-xs mt-1 font-medium">Cursos</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-4 text-center">
                        <p class="text-2xl font-black text-blue-300"><?= $heroStats['students'] ?>+</p>
                        <p class="text-blue-200 text-xs mt-1 font-medium">Estudiantes</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-4 text-center">
                        <p class="text-2xl font-black text-emerald-300"><?= $heroStats['categories'] ?></p>
                        <p class="text-blue-200 text-xs mt-1 font-medium">Áreas</p>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-400/20 flex items-center justify-center text-xs text-emerald-300 flex-shrink-0">✓</span>
                        <span class="text-blue-100 text-sm font-medium">Certificaciones reconocidas por IMO</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-400/20 flex items-center justify-center text-xs text-emerald-300 flex-shrink-0">✓</span>
                        <span class="text-blue-100 text-sm font-medium">Acceso 24/7 desde cualquier dispositivo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- STATS BAR -->
<section class="bg-white py-8 border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="text-center">
                <p class="text-3xl font-black text-brand-500"><?= $heroStats['courses'] ?>+</p>
                <p class="text-xs text-gray-500 font-semibold mt-1">Cursos Disponibles</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-black text-emerald-600"><?= $heroStats['students'] ?>+</p>
                <p class="text-xs text-gray-500 font-semibold mt-1">Estudiantes Activos</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-black text-gold">IMO</p>
                <p class="text-xs text-gray-500 font-semibold mt-1">Certificación Internacional</p>
            </div>
            <div class="text-center">
                <p class="text-3xl font-black text-purple-600">24/7</p>
                <p class="text-xs text-gray-500 font-semibold mt-1">Disponibilidad</p>
            </div>
        </div>
    </div>
</section>

<!-- CURSOS DESTACADOS -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
                <h2 class="text-3xl font-black text-navy">Cursos Destacados</h2>
                <p class="text-gray-500 mt-1">Inicia tu formación profesional hoy mismo</p>
            </div>
            <a href="/courses" class="inline-flex items-center gap-2 text-brand-500 font-bold hover:text-brand-600 transition-colors text-sm whitespace-nowrap">
                Ver todos los cursos
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <?php if (empty($featured)): ?>
            <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
                <p class="text-gray-400 font-medium">No hay cursos publicados aún.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($featured as $course): ?>
                    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 card-hover flex flex-col">
                        <div class="aspect-video relative overflow-hidden bg-gray-100">
                            <?php if ($course['image']): ?>
                                <img src="<?= htmlspecialchars($course['image']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fas fa-image text-3xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 h-10"><?= htmlspecialchars($course['title']) ?></h3>
                            <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                                <span class="text-brand-600 font-black">$<?= number_format($course['price'], 2) ?></span>
                                <a href="/courses/<?= $course['slug'] ?>" class="text-xs font-bold bg-brand-50 hover:bg-brand-100 text-brand-600 px-3 py-2 rounded-lg transition-colors">
                                    Ver curso
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
