<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-60 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/slide/SLIDE8.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-700/60 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('services.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                    <?php echo __('services.hero.subtitle'); ?>
                </p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
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

    <!-- CTA -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-4"><?php echo __('services.cta.title'); ?></h2>
            <p class="text-xl text-gray-600 mb-8"><?php echo __('services.cta.subtitle'); ?></p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/shop" class="inline-block bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-10 py-4 rounded-xl font-bold hover:from-cyan-600 hover:to-blue-700 transition transform hover:scale-105 shadow-xl">
                    <i class="fas fa-shopping-cart mr-2"></i><?php echo __('services.cta.browse'); ?>
                </a>
                <a href="/contact" class="inline-block bg-white text-cyan-600 border-2 border-cyan-600 px-10 py-4 rounded-xl font-bold hover:bg-cyan-50 transition transform hover:scale-105 shadow-xl">
                    <i class="fas fa-envelope mr-2"></i><?php echo __('services.cta.contact'); ?>
                </a>
            </div>
        </div>
    </section>
</div>
