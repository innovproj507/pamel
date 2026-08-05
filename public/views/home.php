<!-- Hero Banner Section -->
<section class="hero-gradient text-white py-48 relative overflow-hidden">
    <!-- Background Image Slider -->
    <div id="hero-slider" class="absolute inset-0">
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-100" style="background-image: url('/assets/images/slide/SLIDE1.jpg');"></div>
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-0" style="background-image: url('/assets/images/slide/SLIDE4.jpg');"></div>
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-0" style="background-image: url('/assets/images/slide/SLIDE7.jpg');"></div>
        <div class="hero-slide absolute inset-0 bg-cover bg-center transition-opacity duration-1000 opacity-0" style="background-image: url('/assets/images/slide/SLIDE10.jpg');"></div>
    </div>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/50 via-blue-600/50 to-cyan-700/50"></div>
    
    <!-- Animated Circles -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-5xl mx-auto text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
                <span class="tracking-widest"><?php echo __('home.hero.title_main'); ?></span>
                <span class="block bg-gradient-to-r from-yellow-300 via-yellow-200 to-yellow-300 bg-clip-text text-transparent">
                    <?php echo __('home.hero.title_sub'); ?>
                </span>
            </h1>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-10">
                <a href="/admission" class="inline-block bg-cyan-400 text-white px-10 py-4 rounded-xl font-bold text-lg hover:bg-cyan-500 transition-all transform hover:scale-105 shadow-2xl w-full sm:w-auto uppercase tracking-wider">
                    <span class="flex items-center justify-center">
                        <i class="fas fa-user-graduate mr-2"></i>
                        <?php echo __('home.admission.btn'); ?>
                    </span>
                </a>
            </div>
            
            <!-- Stats -->
            
        </div>
    </div>
    
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
            <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
        </svg>
    </div>
</section>
</section>

<!-- About PAMEL Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            
            <!-- Welcome Section -->
            <div class="text-center mb-16">
                <span class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                    <?php echo __('home.about.badge'); ?>
                </span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mt-6 mb-6">
                    <?php echo __('home.about.title', ['name' => '<span class="text-cyan-600">PAMEL</span>']); ?>
                </h2>
                <p class="text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                    <?php echo __('home.about.text'); ?>
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 gap-8 mb-16">
                
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-8 hover:shadow-xl transition">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-certificate text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3"><?php echo __('home.about.feature_iso'); ?></h3>
                    <p class="text-gray-700"><?php echo __('home.about.feature_iso_text'); ?></p>
                </div>

                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-8 hover:shadow-xl transition">
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-500 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-ship text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3"><?php echo __('home.about.feature_stcw'); ?></h3>
                    <p class="text-gray-700"><?php echo __('home.about.feature_stcw_text'); ?></p>
                </div>

                <!-- <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-8 hover:shadow-xl transition">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-600 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-globe text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">International Recognition</h3>
                    <p class="text-gray-700">Panama Maritime Authority approved training center with global certifications.</p>
                </div> -->

            </div>

            <!-- Stats -->
           

        </div>
    </div>
</section>


<!-- Approved Courses — Two Panels -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">

            <div class="text-center mb-14">
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4"><?php echo __('home.featured.title'); ?></h2>
                <p class="text-xl text-gray-600"><?php echo __('home.featured.subtitle'); ?></p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 items-start">

                <!-- ══════════ PAMEL PANEL ══════════ -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-700 px-8 py-7 text-white text-center">
                        <div class="flex items-center justify-center mb-3">
                            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-anchor text-3xl text-white"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-xs uppercase tracking-widest text-cyan-100 font-semibold">Panama Maritime E-Learning</p>
                                <h3 class="text-2xl font-extrabold leading-tight">PANAMA Courses</h3>
                            </div>
                        </div>
                        <p class="text-sm text-cyan-100 mt-1">STCW Certified</p>
                    </div>

                    <!-- Course List -->
                    <div class="divide-y divide-gray-100 flex-1">
                        <?php if (!empty($pamelProducts)): ?>
                            <?php foreach ($pamelProducts as $product): ?>
                                <a href="/shop/<?php echo htmlspecialchars($product['slug']); ?>"
                                   class="flex items-center gap-4 px-6 py-4 hover:bg-cyan-50 transition group">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             class="w-16 h-16 object-cover rounded-xl flex-shrink-0 border border-gray-100">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-ship text-white text-xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-gray-900 group-hover:text-cyan-700 transition line-clamp-2 leading-snug">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h4>
                                        <?php if (!empty($product['course_code'])): ?>
                                            <p class="text-xs text-gray-400 mt-1 font-mono"><?php echo htmlspecialchars($product['course_code']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-cyan-500 transition flex-shrink-0 text-xs"></i>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="px-6 py-10 text-center text-gray-400">
                                <i class="fas fa-box-open text-3xl mb-2"></i>
                                <p class="text-sm"><?php echo __('home.featured.none'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- View All Button -->
                    <div class="p-6 border-t border-gray-100 bg-gray-50">
                        <a href="/shop?category=pamel"
                           class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl transition transform hover:scale-105 shadow-md">
                            <i class="fas fa-th-list"></i>
                            Ver todos los cursos PAMEL
                        </a>
                    </div>
                </div>

                <!-- ══════════ INDIA / LIMR PANEL ══════════ -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-orange-500 to-red-600 px-8 py-7 text-white text-center">
                        <div class="flex items-center justify-center mb-3">
                            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-globe-asia text-3xl text-white"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-xs uppercase tracking-widest text-orange-100 font-semibold">LATIN Indo Marine Registry</p>
                                <h3 class="text-2xl font-extrabold leading-tight">India Courses</h3>
                            </div>
                        </div>
                        <p class="text-sm text-orange-100 mt-1">STCW Certified</p>
                    </div>

                    <!-- Course List -->
                    <div class="divide-y divide-gray-100 flex-1">
                        <?php if (!empty($indiaProducts)): ?>
                            <?php foreach ($indiaProducts as $product): ?>
                                <a href="/shop/<?php echo htmlspecialchars($product['slug']); ?>"
                                   class="flex items-center gap-4 px-6 py-4 hover:bg-orange-50 transition group">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             class="w-16 h-16 object-cover rounded-xl flex-shrink-0 border border-gray-100">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-ship text-white text-xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition line-clamp-2 leading-snug">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </h4>
                                        <?php if (!empty($product['course_code'])): ?>
                                            <p class="text-xs text-gray-400 mt-1 font-mono"><?php echo htmlspecialchars($product['course_code']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-orange-500 transition flex-shrink-0 text-xs"></i>
                                </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="px-6 py-10 text-center text-gray-400">
                                <i class="fas fa-box-open text-3xl mb-2"></i>
                                <p class="text-sm"><?php echo __('home.featured.none'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- View All Button -->
                    <div class="p-6 border-t border-gray-100 bg-gray-50">
                        <a href="/shop?category=latin"
                           class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold py-3 px-6 rounded-xl transition transform hover:scale-105 shadow-md">
                            <i class="fas fa-th-list"></i>
                            Ver todos los cursos India
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>


<!-- Why Choose PAMEL Section -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4"><?php echo __('home.why_choose.title'); ?></h2>
                <p class="text-xl text-gray-600"><?php echo __('home.why_choose.subtitle'); ?></p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-2xl transition">
                    <div class="bg-cyan-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-graduation-cap text-cyan-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo __('home.why_choose.feature_1'); ?></h3>
                    <p class="text-gray-600"><?php echo __('home.why_choose.feature_1_text'); ?></p>
                </div>

                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-2xl transition">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-laptop text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo __('home.why_choose.feature_2'); ?></h3>
                    <p class="text-gray-600"><?php echo __('home.why_choose.feature_2_text'); ?></p>
                </div>

                <!-- <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-2xl transition">
                    <div class="bg-cyan-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-certificate text-cyan-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Global Certificates</h3>
                    <p class="text-gray-600">Internationally recognized certifications</p>
                </div> -->

                <div class="bg-white rounded-xl p-8 shadow-lg hover:shadow-2xl transition">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-headset text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2"><?php echo __('home.why_choose.feature_3'); ?></h3>
                    <p class="text-gray-600"><?php echo __('home.why_choose.feature_3_text'); ?></p>
                </div>

            </div>

        </div>
    </div>
</section>

<style>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}

.animate-fade-in-up {
    animation: fade-in-up 1s ease-out;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hero Slider Logic
    const slides = document.querySelectorAll('.hero-slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        setInterval(() => {
            slides[currentSlide].classList.remove('opacity-100');
            slides[currentSlide].classList.add('opacity-0');
            
            currentSlide = (currentSlide + 1) % slides.length;
            
            slides[currentSlide].classList.remove('opacity-0');
            slides[currentSlide].classList.add('opacity-100');
        }, 5000); // Cambia de imagen cada 5 segundos
    }
});
</script>
