<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-48 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/barco-ourcompany.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-600/80 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center space-x-2 bg-white/10 backdrop-blur-lg border border-white/20 px-6 py-3 rounded-full mb-8">
                    <i class="fas fa-user-graduate text-yellow-300"></i>
                    <span class="text-sm font-semibold"><?php echo __('admission.hero.badge'); ?></span>
                </div>
                
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('admission.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100"><?php echo __('admission.hero.subtitle'); ?></p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Admission Steps -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto">
                
                <!-- Step 1: Admission Requirements -->
                <div class="mb-12 bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-start">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl mr-6 flex-shrink-0">
                            1
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('admission.steps.1.title'); ?></h2>
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo __('admission.steps.1.text'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Admission Form -->
                <div class="mb-12 bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-start">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl mr-6 flex-shrink-0">
                            2
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('admission.steps.2.title'); ?></h2>
                            <p class="text-gray-600 leading-relaxed mb-6">
                                <?php echo __('admission.steps.2.text'); ?>
                            </p>
                            <a href="/formulario-de-admision" class="inline-block bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-3 rounded-full font-bold uppercase text-sm tracking-wider transition transform hover:scale-105 shadow-lg">
                                <?php echo __('admission.steps.2.btn'); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Admission Confirmation -->
                <div class="mb-12 bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-start">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl mr-6 flex-shrink-0">
                            3
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('admission.steps.3.title'); ?></h2>
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo __('admission.steps.3.text'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Course Purchase -->
                <div class="mb-12 bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-start">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl mr-6 flex-shrink-0">
                            4
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('admission.steps.4.title'); ?></h2>
                            <p class="text-gray-600 leading-relaxed mb-4">
                                <?php echo __('admission.steps.4.text'); ?>
                            </p>
                            <a href="/shop" class="text-cyan-600 font-semibold hover:text-cyan-700 transition inline-flex items-center">
                                <i class="fas fa-store mr-2"></i>
                                <?php echo __('admission.steps.4.btn'); ?> →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Payment Confirmation -->
                <div class="mb-12 bg-gradient-to-br from-gray-50 to-white rounded-2xl p-8 shadow-lg border border-gray-100">
                    <div class="flex items-start">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl mr-6 flex-shrink-0">
                            5
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('admission.steps.5.title'); ?></h2>
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo __('admission.steps.5.text'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Course Start -->
                <div class="bg-gradient-to-br from-green-50 to-white rounded-2xl p-8 shadow-lg border-2 border-green-200">
                    <div class="flex items-start">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl mr-6 flex-shrink-0">
                            6
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                                <?php echo __('admission.steps.6.title'); ?>
                                <i class="fas fa-check-circle text-green-500 ml-3"></i>
                            </h2>
                            <p class="text-gray-600 leading-relaxed">
                                <?php echo __('admission.steps.6.text'); ?>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-cyan-500 to-blue-600">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center text-white">
                <h2 class="text-4xl font-extrabold mb-4"><?php echo __('admission.cta.title'); ?></h2>
                <p class="text-xl mb-8 text-blue-100"><?php echo __('admission.cta.subtitle'); ?></p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/contact" class="inline-block bg-white text-blue-600 px-10 py-4 rounded-full font-bold hover:bg-blue-50 transition transform hover:scale-105 shadow-2xl">
                        <i class="fas fa-file-alt mr-2"></i>
                        <?php echo __('admission.cta.btn_apply'); ?>
                    </a>
                    <a href="/shop" class="inline-block bg-transparent border-2 border-white text-white px-10 py-4 rounded-full font-bold hover:bg-white hover:text-blue-600 transition transform hover:scale-105">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        <?php echo __('admission.cta.btn_courses'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
