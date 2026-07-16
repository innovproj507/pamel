<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-60 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/slide/SLIDE3.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-700/60 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('branches.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                    <?php echo __('branches.hero.subtitle'); ?>
                </p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Branches Section -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                
                <!-- Introduction -->
                <div class="text-center mb-16">
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        <?php echo __('branches.intro'); ?>
                    </p>
                </div>

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
                                <p class="text-cyan-600 text-sm font-semibold mb-3"><?php echo __('branches.practical.pool.subtitle'); ?></p>
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
                                <p class="text-orange-600 text-sm font-semibold mb-3"><?php echo __('branches.practical.fire.subtitle'); ?></p>
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
                                <p class="text-emerald-600 text-sm font-semibold mb-3"><?php echo __('branches.practical.kitchen.subtitle'); ?></p>
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
</div>
