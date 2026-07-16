<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-60 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/slide/SLIDE11.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-700/60 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('our_company.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                    <?php echo __('our_company.hero.subtitle'); ?>
                </p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Company Overview -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
                    <div>
                        <div class="inline-block mb-4">
                            <span class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                               <?php echo __('our_company.overview.badge'); ?>
                            </span>
                        </div>
                        <h2 class="text-4xl font-extrabold text-gray-900 mb-6">
                            <?php echo __('our_company.overview.title'); ?>
                        </h2>
                        <p class="text-gray-700 text-lg mb-6 leading-relaxed">
                             <?php echo __('our_company.overview.text_1'); ?>
                        </p>
                        <p class="text-gray-700 text-lg leading-relaxed">
                            <?php echo __('our_company.overview.text_2'); ?>
                        </p>
                    </div>
                    <div class="relative">
                        <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl p-6 shadow-2xl transform rotate-3 hover:rotate-0 transition duration-300">
                            <div class="bg-white rounded-xl overflow-hidden">
                                <img src="/assets/images/location.webp" 
                                     alt="PAMEL Training Center" 
                                     class="w-full h-52 object-cover">
                                <div class="p-5 text-center">
                                    <img src="/assets/images/logo.png" 
                                         alt="PAMEL Logo" 
                                         class="h-14 mx-auto mb-3">
                                    <p class="text-gray-600 text-sm font-medium">Panama Maritime E-Learning (PAMEL), S.A.</p>
                                    <p class="text-gray-400 text-xs mt-1">Parque Lefevre, Panama City</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mission & Vision -->
                <div class="grid md:grid-cols-2 gap-8 mb-20">
                    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                            <i class="fas fa-bullseye text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('our_company.mission.title'); ?></h3>
                        <p class="text-gray-700 leading-relaxed">
                            <?php echo __('our_company.mission.text'); ?>
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
                        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 w-16 h-16 rounded-xl flex items-center justify-center mb-6">
                            <i class="fas fa-eye text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('our_company.vision.title'); ?></h3>
                        <p class="text-gray-700 leading-relaxed">
                            <?php echo __('our_company.vision.text'); ?>
                        </p>
                    </div>
                </div>

                <div class="grid md:grid-cols gap-8 mb-20">
                    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mb-6 mx-auto">
                            <i class="fas fa-compass text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center"><?php echo __('our_company.purpose.title'); ?></h3>
                        <p class="text-gray-700 leading-relaxed">
                            <?php echo __('our_company.purpose.text'); ?>
                        </p>
                    </div>

                    
                </div>

                <!-- Core Values -->
                

                <!-- Why Choose PAMEL -->
                <div class="bg-gradient-to-r from-cyan-500 to-blue-600 rounded-2xl p-12 text-white">
                    <div class="text-center mb-10">
                        <h2 class="text-4xl font-extrabold mb-4 text-center"><?php echo __('our_company.cta.title'); ?></h2>
                        <p class="text-xl text-blue-100"><?php echo __('our_company.cta.subtitle'); ?>

<?php echo __('our_company.cta.address'); ?></p>
                    </div>

                    <!-- Images Section -->
                    <div class="grid md:grid-cols-2 gap-8 mb-12">
                        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4 border border-white/20 hover:shadow-2xl transition duration-300">
                            <img src="/assets/images/location.webp" alt="PAMEL Training Facility" class="w-full h-64 object-cover rounded-lg mb-4">
                        </div>
                        <div class="bg-white/10 backdrop-blur-lg rounded-xl p-4 border border-white/20 hover:shadow-2xl transition duration-300">
                            <img src="/assets/images/location-pamel-section1.jpg" alt="Global Reach - Education without Borders" class="w-full h-64 object-cover rounded-lg mb-4">
                            <h3 class="text-xl font-bold text-center">PAMEL - HEADQUARTERS</h3>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </section>

 
</div>
