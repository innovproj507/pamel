<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-60 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/slide/SLIDE6.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-700/60 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('gcl_iso.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                    <?php echo __('gcl_iso.hero.subtitle'); ?>
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
            <div class="max-w-5xl mx-auto">
                
                <!-- Main Content -->
                <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
                    <div>
                        <div class="inline-block mb-4">
                            <span class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                                <?php echo __('gcl_iso.main.badge'); ?>
                            </span>
                        </div>
                        <h2 class="text-4xl font-extrabold text-gray-900 mb-6">
                            <?php echo __('gcl_iso.main.title'); ?>
                        </h2>
                        <p class="text-gray-700 text-lg leading-relaxed mb-6">
                            <?php echo __('gcl_iso.main.text1'); ?>
                        </p>
                        <p class="text-gray-700 leading-relaxed">
                            <?php echo __('gcl_iso.main.text2'); ?>
                        </p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl p-12 text-white text-center shadow-2xl">
                        <i class="fas fa-certificate text-8xl mb-6 text-yellow-300"></i>
                        <h3 class="text-3xl font-bold mb-4"><?php echo __('gcl_iso.main.badge'); ?></h3>
                        <p class="text-blue-100 text-lg mb-6"><?php echo __('gcl_iso.main.card.type'); ?></p>
                        <div class="border-t-2 border-white/30 pt-6 mb-6">
                            <p class="text-sm text-blue-100"><?php echo __('gcl_iso.main.card.certified_by'); ?></p>
                            <p class="text-2xl font-bold"><?php echo __('gcl_iso.hero.title'); ?></p>
                        </div>
                        <a href="/assets/documents/PAMEL-2027-02-22-certificate-60Q20885-800304031.pdf" target="_blank" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-xl font-bold hover:bg-blue-50 transition transform hover:scale-105 shadow-lg">
                            <i class="fas fa-download mr-2"></i><?php echo __('gcl_iso.main.card.download'); ?>
                        </a>
                    </div>
                </div>

                <!-- What ISO 9001:2015 Means -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center"><?php echo __('gcl_iso.meaning.title'); ?></h2>
                    
                    <div class="grid md:grid-cols-3 gap-6">
                        
                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition border-t-4 border-cyan-500">
                            <i class="fas fa-star text-4xl text-cyan-600 mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-3"><?php echo __('gcl_iso.meaning.qa.title'); ?></h3>
                            <p class="text-gray-600"><?php echo __('gcl_iso.meaning.qa.text'); ?></p>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition border-t-4 border-blue-500">
                            <i class="fas fa-sync-alt text-4xl text-blue-600 mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-3"><?php echo __('gcl_iso.meaning.improvement.title'); ?></h3>
                            <p class="text-gray-600"><?php echo __('gcl_iso.meaning.improvement.text'); ?></p>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition border-t-4 border-cyan-500">
                            <i class="fas fa-users text-4xl text-cyan-600 mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-3"><?php echo __('gcl_iso.meaning.customer.title'); ?></h3>
                            <p class="text-gray-600"><?php echo __('gcl_iso.meaning.customer.text'); ?></p>
                        </div>

                    </div>
                </div>

                <!-- About GCL International -->
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-10">
                    <div class="flex items-start mb-6">
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 w-16 h-16 rounded-xl flex items-center justify-center mr-6 flex-shrink-0">
                            <i class="fas fa-building text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-4"><?php echo __('gcl_iso.about.title'); ?></h2>
                            <p class="text-gray-700 leading-relaxed mb-4">
                                <?php echo __('gcl_iso.about.text1'); ?>
                            </p>
                            <p class="text-gray-700 leading-relaxed">
                                <?php echo __('gcl_iso.about.text2'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Benefits -->
                <div class="mt-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center"><?php echo __('gcl_iso.benefits.title'); ?></h2>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        
                        <div class="flex items-start bg-white rounded-lg p-6 shadow-md hover:shadow-lg transition">
                            <i class="fas fa-check-circle text-green-500 text-2xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-2"><?php echo __('gcl_iso.benefits.items.guaranteed.title'); ?></h4>
                                <p class="text-gray-600"><?php echo __('gcl_iso.benefits.items.guaranteed.text'); ?></p>
                            </div>
                        </div>

                        <div class="flex items-start bg-white rounded-lg p-6 shadow-md hover:shadow-lg transition">
                            <i class="fas fa-check-circle text-green-500 text-2xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-2"><?php echo __('gcl_iso.benefits.items.consistent.title'); ?></h4>
                                <p class="text-gray-600"><?php echo __('gcl_iso.benefits.items.consistent.text'); ?></p>
                            </div>
                        </div>

                        <div class="flex items-start bg-white rounded-lg p-6 shadow-md hover:shadow-lg transition">
                            <i class="fas fa-check-circle text-green-500 text-2xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-2"><?php echo __('gcl_iso.benefits.items.recognition.title'); ?></h4>
                                <p class="text-gray-600"><?php echo __('gcl_iso.benefits.items.recognition.text'); ?></p>
                            </div>
                        </div>

                        <div class="flex items-start bg-white rounded-lg p-6 shadow-md hover:shadow-lg transition">
                            <i class="fas fa-check-circle text-green-500 text-2xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-gray-900 mb-2"><?php echo __('gcl_iso.benefits.items.updates.title'); ?></h4>
                                <p class="text-gray-600"><?php echo __('gcl_iso.benefits.items.updates.text'); ?></p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-gradient-to-r from-cyan-500 to-blue-600">
        <div class="container mx-auto px-4 text-center text-white">
            <h2 class="text-4xl font-bold mb-4"><?php echo __('gcl_iso.cta.title'); ?></h2>
            <p class="text-xl mb-8 text-blue-100"><?php echo __('gcl_iso.cta.subtitle'); ?></p>
            <a href="/shop" class="inline-block bg-white text-blue-600 px-10 py-4 rounded-xl font-bold hover:bg-blue-50 transition transform hover:scale-105 shadow-xl">
                <i class="fas fa-graduation-cap mr-2"></i><?php echo __('gcl_iso.cta.btn'); ?>
            </a>
        </div>
    </section>
</div>
