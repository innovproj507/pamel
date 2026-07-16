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
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('certificates.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                    <?php echo __('certificates.hero.subtitle'); ?>
                </p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Certifications Section -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-7xl mx-auto">
                
                <!-- ISO Certificate Display -->
                <div class="bg-white rounded-2xl shadow-2xl p-8 mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center"><?php echo __('certificates.iso_section.title'); ?></h2>
                    <div class="flex justify-center mb-6">
                        <img src="/assets/images/certificado_ISOexp2027.jpg" alt="ISO 9001:2015 Certificate" class=" h-auto rounded-lg shadow-lg">
                    </div>
                    <p class="text-center text-gray-600 italic"><?php echo __('certificates.iso_section.expiry'); ?></p>
                </div>

                <!-- PAMEL Approvals -->
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl shadow-xl p-10 mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">PAMEL:</h2>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-anchor text-cyan-600 mt-1 mr-3 text-lg"></i>
                            <span class="text-gray-700 leading-relaxed"><?php echo __('certificates.approvals.training'); ?></span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-laptop text-cyan-600 mt-1 mr-3 text-lg"></i>
                            <span class="text-gray-700 leading-relaxed"><?php echo __('certificates.approvals.elearning'); ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Certification Highlights Grid -->
                <div class="grid md:grid-cols-2 gap-8 mb-12">
                    
                    <!-- STCW Certification -->
                    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden hover:shadow-3xl transition">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-8 text-white">
                            <i class="fas fa-certificate text-6xl mb-4"></i>
                            <h3 class="text-3xl font-bold"><?php echo __('certificates.stcw.title'); ?></h3>
                            <p class="text-blue-100 mt-2"><?php echo __('certificates.stcw.subtitle'); ?></p>
                        </div>
                        <div class="p-8">
                            <p class="text-gray-700 mb-4 leading-relaxed">
                                <?php echo __('certificates.stcw.text'); ?>
                            </p>
                            <ul class="space-y-2">
                                <li class="flex items-start text-gray-600">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                    <span><?php echo __('certificates.stcw.list.compliant'); ?></span>
                                </li>
                                <li class="flex items-start text-gray-600">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                    <span><?php echo __('certificates.stcw.list.recognized'); ?></span>
                                </li>
                                <li class="flex items-start text-gray-600">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                    <span><?php echo __('certificates.stcw.list.global'); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- ISO 9001:2015 -->
                    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden hover:shadow-3xl transition">
                        <div class="bg-gradient-to-r from-blue-600 to-cyan-500 p-8 text-white">
                            <i class="fas fa-award text-6xl mb-4"></i>
                            <h3 class="text-3xl font-bold"><?php echo __('certificates.iso_9001.title'); ?></h3>
                            <p class="text-blue-100 mt-2"><?php echo __('certificates.iso_9001.subtitle'); ?></p>
                        </div>
                        <div class="p-8">
                            <p class="text-gray-700 mb-4 leading-relaxed">
                                <?php echo __('certificates.iso_9001.text'); ?>
                            </p>
                            <ul class="space-y-2">
                                <li class="flex items-start text-gray-600">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                    <span><?php echo __('certificates.iso_9001.list.excellence'); ?></span>
                                </li>
                                <li class="flex items-start text-gray-600">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                    <span><?php echo __('certificates.iso_9001.list.satisfaction'); ?></span>
                                </li>
                                <li class="flex items-start text-gray-600">
                                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                    <span><?php echo __('certificates.iso_9001.list.improvement'); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Additional Certifications -->
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-6 text-center hover:shadow-xl transition">
                        <i class="fas fa-shield-alt text-4xl text-blue-600 mb-4"></i>
                        <h4 class="text-xl font-bold text-gray-900 mb-2"><?php echo __('certificates.highlights.safety.title'); ?></h4>
                        <p class="text-gray-600"><?php echo __('certificates.highlights.safety.text'); ?></p>
                    </div>

                    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl p-6 text-center hover:shadow-xl transition">
                        <i class="fas fa-graduation-cap text-4xl text-cyan-600 mb-4"></i>
                        <h4 class="text-xl font-bold text-gray-900 mb-2"><?php echo __('certificates.highlights.accredited.title'); ?></h4>
                        <p class="text-gray-600"><?php echo __('certificates.highlights.accredited.text'); ?></p>
                    </div>

                </div>

                <!-- Certification Process -->
                <div class="mt-16 bg-white rounded-2xl shadow-lg p-10">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center"><?php echo __('certificates.process.title'); ?></h2>
                    
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="bg-cyan-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-cyan-600">1</span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2"><?php echo __('certificates.process.steps.1.title'); ?></h4>
                            <p class="text-sm text-gray-600"><?php echo __('certificates.process.steps.1.text'); ?></p>
                        </div>

                        <div class="text-center">
                            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-blue-600">2</span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2"><?php echo __('certificates.process.steps.2.title'); ?></h4>
                            <p class="text-sm text-gray-600"><?php echo __('certificates.process.steps.2.text'); ?></p>
                        </div>

                        <div class="text-center">
                            <div class="bg-cyan-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-cyan-600">3</span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2"><?php echo __('certificates.process.steps.3.title'); ?></h4>
                            <p class="text-sm text-gray-600"><?php echo __('certificates.process.steps.3.text'); ?></p>
                        </div>

                        <div class="text-center">
                            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-2xl font-bold text-blue-600">4</span>
                            </div>
                            <h4 class="font-bold text-gray-900 mb-2"><?php echo __('certificates.process.steps.4.title'); ?></h4>
                            <p class="text-sm text-gray-600"><?php echo __('certificates.process.steps.4.text'); ?></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-gradient-to-r from-cyan-500 to-blue-600">
        <div class="container mx-auto px-4 text-center text-white">
            <h2 class="text-4xl font-bold mb-4"><?php echo __('certificates.cta.title'); ?></h2>
            <p class="text-xl mb-8 text-blue-100"><?php echo __('certificates.cta.subtitle'); ?></p>
            <a href="/shop" class="inline-block bg-white text-blue-600 px-10 py-4 rounded-xl font-bold hover:bg-blue-50 transition transform hover:scale-105 shadow-xl">
                <i class="fas fa-graduation-cap mr-2"></i><?php echo __('certificates.cta.btn'); ?>
            </a>
        </div>
    </section>
</div>
