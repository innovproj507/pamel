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

            <?php
                $branchThemes = [
                    ['from' => 'cyan-500', 'to' => 'blue-700', 'soft' => 'cyan-50', 'text' => 'cyan-100', 'accent' => 'cyan-500'],
                    ['from' => 'orange-500', 'to' => 'red-600', 'soft' => 'orange-50', 'text' => 'orange-100', 'accent' => 'orange-500'],
                    ['from' => 'emerald-500', 'to' => 'teal-700', 'soft' => 'emerald-50', 'text' => 'emerald-100', 'accent' => 'emerald-500'],
                    ['from' => 'purple-500', 'to' => 'indigo-700', 'soft' => 'purple-50', 'text' => 'purple-100', 'accent' => 'purple-500'],
                ];
                $branchCount = count($branches);
                $gridCols = $branchCount >= 3 ? 'md:grid-cols-2 lg:grid-cols-3' : 'md:grid-cols-2';
            ?>
            <div class="grid <?php echo $gridCols; ?> gap-8 items-start">

                <?php foreach (array_values($branches) as $i => $branch): ?>
                    <?php $theme = $branchThemes[$i % count($branchThemes)]; ?>
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-<?php echo $theme['from']; ?> to-<?php echo $theme['to']; ?> px-8 py-7 text-white text-center">
                            <div class="flex items-center justify-center mb-3">
                                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas <?php echo htmlspecialchars($branch['icon'] ?: 'fa-anchor'); ?> text-3xl text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs uppercase tracking-widest text-<?php echo $theme['text']; ?> font-semibold"><?php echo htmlspecialchars($branch['description'] ?: $branch['name']); ?></p>
                                    <h3 class="text-2xl font-extrabold leading-tight"><?php echo htmlspecialchars($branch['name']); ?> Courses</h3>
                                </div>
                            </div>
                            <p class="text-sm text-<?php echo $theme['text']; ?> mt-1">STCW CERTIFIED - <?php echo htmlspecialchars(strtoupper($branch['name'])); ?></p>
                        </div>

                        <!-- Course List -->
                        <div class="divide-y divide-gray-100 flex-1">
                            <?php if (!empty($branch['products'])): ?>
                                <?php foreach ($branch['products'] as $product): ?>
                                    <a href="/courses/<?php echo htmlspecialchars($product['slug']); ?>"
                                       class="flex items-center gap-4 px-6 py-4 hover:bg-<?php echo $theme['soft']; ?> transition group">
                                        <?php if (!empty($product['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>"
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                 class="w-16 h-16 object-cover rounded-xl flex-shrink-0 border border-gray-100">
                                        <?php else: ?>
                                            <div class="w-16 h-16 bg-gradient-to-br from-<?php echo $theme['from']; ?> to-<?php echo $theme['to']; ?> rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-ship text-white text-xl"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-bold text-gray-900 transition line-clamp-2 leading-snug">
                                                <?php echo htmlspecialchars($product['name']); ?>
                                            </h4>
                                            <?php if (!empty($product['course_code'])): ?>
                                                <p class="text-xs text-gray-400 mt-1 font-mono"><?php echo htmlspecialchars($product['course_code']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <i class="fas fa-chevron-right text-gray-300 transition flex-shrink-0 text-xs"></i>
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
                            <a href="/courses?branch=<?php echo htmlspecialchars($branch['slug']); ?>"
                               class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-<?php echo $theme['from']; ?> to-<?php echo $theme['to']; ?> hover:opacity-90 text-white font-bold py-3 px-6 rounded-xl transition transform hover:scale-105 shadow-md">
                                <i class="fas fa-th-list"></i>
                                <?php echo __('home.featured.view_all'); ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</section>

<!-- Our Facilities Section -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">

            <!-- Main Branch -->
            <div class="mb-12">
                <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl overflow-hidden shadow-2xl">
                    <div class="grid md:grid-cols-2 gap-8 p-10 md:p-12">
                        <div class="text-white">
                            <div class="inline-block bg-yellow-400 text-gray-900 px-4 py-2 rounded-full text-sm font-bold mb-6">
                                <i class="fas fa-star mr-1"></i> <?php echo __('branches.headquarters.badge'); ?>
                            </div>
                            <h2 class="text-4xl font-extrabold mb-4"><?php echo __('branches.headquarters.title'); ?></h2>
                            <p class="text-blue-100 text-lg mb-6 leading-relaxed">
                                <?php echo __('branches.headquarters.text'); ?>
                            </p>

                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-yellow-300 mt-1 mr-4 text-xl"></i>
                                    <div>
                                        <p class="font-semibold"><?php echo __('footer.address'); ?></p>
                                        <p class="text-blue-100">Parque Lefevre, Panamá</p>
                                        <p class="text-blue-100">St. 102 E, Panama Viejo Business Center</p>
                                        <p class="text-blue-100">G15-3(B), Panama City, Panama, PTY</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <i class="fas fa-phone text-yellow-300 mt-1 mr-4 text-xl"></i>
                                    <div>
                                        <p class="font-semibold"><?php echo __('footer.phone'); ?></p>
                                        <p class="text-blue-100">(507) 391-7492</p>
                                        <p class="text-blue-100">(507) 6298-8137</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <i class="fas fa-envelope text-yellow-300 mt-1 mr-4 text-xl"></i>
                                    <div>
                                        <p class="font-semibold"><?php echo __('footer.email'); ?></p>
                                        <p class="text-blue-100">info@pamel.edu.pa</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-8">
                            <h3 class="text-white font-bold text-xl mb-4"><?php echo __('branches.headquarters.facilities_title'); ?></h3>
                            <ul class="space-y-3 text-white">
                                <?php foreach (__('branches.headquarters.facilities') as $facility): ?>
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-yellow-300 mr-3"></i>
                                    <?php echo $facility; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Practical Facilities -->
            <div>
                <div class="text-center mb-10">
                    <span class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                        <?php echo __('branches.practical.badge'); ?>
                    </span>
                    <h2 class="text-3xl font-bold text-gray-900 mt-5 mb-2"><?php echo __('branches.practical.title'); ?></h2>
                    <p class="text-gray-500 max-w-2xl mx-auto"><?php echo __('branches.practical.subtitle'); ?></p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">

                    <!-- Piscina Los Ríos -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition group">
                        <div class="bg-gradient-to-br from-cyan-400 to-blue-600 h-40 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-20">
                                <div class="absolute bottom-0 left-0 right-0 h-16 bg-white/30 rounded-t-full"></div>
                                <div class="absolute bottom-2 left-4 right-4 h-10 bg-white/20 rounded-t-full"></div>
                            </div>
                            <div class="relative text-center text-white">
                                <i class="fas fa-swimming-pool text-5xl mb-2 drop-shadow-lg"></i>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-1"><?php echo __('branches.practical.pool.title'); ?></h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                <?php echo __('branches.practical.pool.text'); ?>
                            </p>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt text-cyan-500 mr-2"></i>
                                    <span><?php echo __('branches.practical.pool.location'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- POIDVEN - Contra Incendios -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition group">
                        <div class="bg-gradient-to-br from-orange-400 to-red-600 h-40 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute top-2 left-6 w-8 h-24 bg-yellow-300 rounded-full blur-sm"></div>
                                <div class="absolute top-4 right-8 w-6 h-16 bg-yellow-200 rounded-full blur-sm"></div>
                            </div>
                            <div class="relative text-center text-white">
                                <i class="fas fa-fire-extinguisher text-5xl mb-2 drop-shadow-lg"></i>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-1"><?php echo __('branches.practical.fire.title'); ?></h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                <?php echo __('branches.practical.fire.text'); ?>
                            </p>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                                    <span><?php echo __('branches.practical.fire.location'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cocina / Restaurante -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition group">
                        <div class="bg-gradient-to-br from-emerald-400 to-teal-600 h-40 flex items-center justify-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10">
                                <div class="absolute bottom-4 left-4 right-4 h-12 bg-white rounded-xl"></div>
                            </div>
                            <div class="relative text-center text-white">
                                <i class="fas fa-utensils text-5xl mb-2 drop-shadow-lg"></i>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-1"><?php echo __('branches.practical.kitchen.title'); ?></h3>
                            <p class="text-gray-600 text-sm leading-relaxed">
                                <?php echo __('branches.practical.kitchen.text'); ?>
                            </p>
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt text-emerald-500 mr-2"></i>
                                    <span><?php echo __('branches.practical.kitchen.location'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Map Section -->
            <div class="mt-16">
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-cyan-100 rounded-full mb-4">
                        <i class="fas fa-map-marked-alt text-3xl text-cyan-600"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4"><?php echo __('branches.map.title'); ?></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto"><?php echo __('branches.map.subtitle'); ?></p>
                </div>

                <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border-8 border-white">
                    <div class="relative h-[450px]">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.5738696381395!2d-79.488374!3d9.0112995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8faca9650f6aaae5%3A0x5afcb580ad86eda5!2sPanama%20Maritime%20E-learning%20(PAMEL)%20S.A.!5e0!3m2!1ses-419!2spa!4v1776706292449!5m2!1ses-419!2spa"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full h-full">
                        </iframe>

                        <!-- Floating Action Badge -->
                        <div class="absolute bottom-6 left-6 right-6 md:left-auto md:right-8">
                            <a href="https://maps.app.goo.gl/9R6YQnZ2j9A5b8G7A" target="_blank"
                               class="flex items-center justify-center gap-3 bg-white/90 backdrop-blur-md px-8 py-4 rounded-2xl shadow-xl text-cyan-700 font-bold hover:bg-white transition group">
                                <i class="fas fa-directions text-xl group-hover:rotate-12 transition"></i>
                                <span><?php echo __('branches.map.btn'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Competence Structure Section -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">

            <!-- Professional Training Approach -->
            <div class="mb-16">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-extrabold text-gray-900 mb-4"><?php echo __('services.competence.title'); ?></h2>
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <p class="text-gray-700 leading-relaxed text-justify mb-6">
                        <?php echo __('services.competence.text'); ?>
                    </p>

                    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4"><?php echo __('services.competence.subtitle'); ?></h3>
                        <ul class="grid md:grid-cols-2 gap-x-8 gap-y-3 text-gray-700">
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.1'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.2'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.3'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.4'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.5'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.6'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.7'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.8'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.9'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.10'); ?></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-3 text-sm"></i>
                                <span><?php echo __('services.competence.list.11'); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Process Flow -->
            <div class="bg-gradient-to-r from-cyan-500 to-blue-600 rounded-2xl p-12 text-white mb-16">
                <h2 class="text-3xl font-bold mb-8 text-center"><?php echo __('services.process.title'); ?></h2>

                <div class="grid md:grid-cols-4 gap-6">
                    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 text-center hover:bg-white/20 transition">
                        <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user-plus text-cyan-600 text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-lg mb-2">1. <?php echo __('services.process.steps.1.title'); ?></h4>
                        <p class="text-blue-100 text-sm"><?php echo __('services.process.steps.1.text'); ?></p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 text-center hover:bg-white/20 transition">
                        <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chalkboard-teacher text-cyan-600 text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-lg mb-2">2. <?php echo __('services.process.steps.2.title'); ?></h4>
                        <p class="text-blue-100 text-sm"><?php echo __('services.process.steps.2.text'); ?></p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 text-center hover:bg-white/20 transition">
                        <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-check text-cyan-600 text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-lg mb-2">3. <?php echo __('services.process.steps.3.title'); ?></h4>
                        <p class="text-blue-100 text-sm"><?php echo __('services.process.steps.3.text'); ?></p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 text-center hover:bg-white/20 transition">
                        <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-certificate text-cyan-600 text-2xl"></i>
                        </div>
                        <h4 class="font-bold text-lg mb-2">4. <?php echo __('services.process.steps.4.title'); ?></h4>
                        <p class="text-blue-100 text-sm"><?php echo __('services.process.steps.4.text'); ?></p>
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
