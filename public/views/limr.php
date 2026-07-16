<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-60 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/slide/SLIDE2.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-700/60 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('limr.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                    <?php echo __('limr.hero.subtitle'); ?>
                </p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                
                <!-- Who is LIMR -->
                <div class="grid lg:grid-cols-2 gap-12 items-start mb-16">
                    <div>
                        <h2 class="text-4xl font-extrabold text-gray-900 mb-6"><?php echo __('limr.who.title'); ?></h2>
                        <div class="space-y-4 text-gray-700 leading-relaxed text-justify">
                            <p>
                                <?php echo __('limr.who.text1'); ?>
                            </p>
                            <p>
                                <?php echo __('limr.who.text2'); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <img src="/assets/images/base-ubicacion.jpg" alt="LIMR Location" class="w-full rounded-lg shadow-md mb-4">
                        <p class="text-center text-gray-600 font-semibold"><?php echo __('limr.who.tagline'); ?></p>
                    </div>
                </div>

                <!-- Company Values -->
                <div class="mb-16">
                    <h2 class="text-4xl font-extrabold text-gray-900 mb-12 text-center"><?php echo __('limr.values.title'); ?></h2>
                    
                    <div class="grid md:grid-cols-3 gap-8">
                        
                        <!-- Mission -->
                        <div class="bg-white rounded-2xl shadow-xl p-8 hover:shadow-2xl transition">
                            <div class="flex justify-center mb-6">
                                <div class="bg-gradient-to-r from-cyan-500 to-blue-600 w-16 h-16 rounded-full flex items-center justify-center">
                                    <i class="fas fa-compass text-white text-3xl"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center"><?php echo __('limr.values.mission.title'); ?></h3>
                            <ul class="space-y-2 text-gray-700 text-sm">
                                <li class="flex items-start">
                                    <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-2 text-xs"></i>
                                    <span><?php echo __('limr.values.mission.list.1'); ?></span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-2 text-xs"></i>
                                    <span><?php echo __('limr.values.mission.list.2'); ?></span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-2 text-xs"></i>
                                    <span><?php echo __('limr.values.mission.list.3'); ?></span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-2 text-xs"></i>
                                    <span><?php echo __('limr.values.mission.list.4'); ?></span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-2 text-xs"></i>
                                    <span><?php echo __('limr.values.mission.list.5'); ?></span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-chevron-right text-cyan-600 mt-1 mr-2 text-xs"></i>
                                    <span><?php echo __('limr.values.mission.list.6'); ?></span>
                                </li>
                            </ul>
                        </div>

                        <!-- Vision -->
                        <div class="bg-white rounded-2xl shadow-xl p-8 hover:shadow-2xl transition">
                            <div class="flex justify-center mb-6">
                                <div class="bg-gradient-to-r from-blue-600 to-cyan-500 w-16 h-16 rounded-full flex items-center justify-center">
                                    <i class="fas fa-eye text-white text-3xl"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center"><?php echo __('limr.values.vision.title'); ?></h3>
                            <p class="text-gray-700 leading-relaxed text-center text-cyan-700">
                                <?php echo __('limr.values.vision.text'); ?>
                            </p>
                        </div>

                        <!-- Our Purpose -->
                        <div class="bg-white rounded-2xl shadow-xl p-8 hover:shadow-2xl transition">
                            <div class="flex justify-center mb-6">
                                <div class="bg-gradient-to-r from-cyan-500 to-blue-600 w-16 h-16 rounded-full flex items-center justify-center">
                                    <i class="fas fa-gem text-white text-3xl"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center"><?php echo __('limr.values.purpose.title'); ?></h3>
                            <p class="text-gray-700 leading-relaxed text-center">
                                <?php echo __('limr.values.purpose.text'); ?>
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-gradient-to-r from-cyan-500 to-blue-600">
        <div class="container mx-auto px-4 text-center text-white">
            <h2 class="text-4xl font-bold mb-6"><?php echo __('limr.cta.title'); ?></h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                <?php echo __('limr.cta.subtitle'); ?>
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/shop?category=latin" class="inline-block bg-white text-blue-600 px-10 py-4 rounded-xl font-bold hover:bg-blue-50 transition transform hover:scale-105 shadow-xl">
                    <i class="fas fa-graduation-cap mr-2"></i><?php echo __('limr.cta.btn_view'); ?>
                </a>
                <a href="/contact" class="inline-block bg-blue-700 text-white border-2 border-white px-10 py-4 rounded-xl font-bold hover:bg-blue-800 transition transform hover:scale-105 shadow-xl">
                    <i class="fas fa-envelope mr-2"></i><?php echo __('limr.cta.btn_contact'); ?>
                </a>
            </div>
        </div>
    </section>
</div>
