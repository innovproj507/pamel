<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->
    

    <section class="hero-gradient text-white py-60 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/slide/SLIDE4.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-700/60 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('payment_methods.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                     <?php echo __('payment_methods.hero.subtitle'); ?>
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
    <section class="py-20 -mt-10 relative z-20">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl p-8 md:p-16 border border-gray-100">
                
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    <!-- Intro -->
                    <p class="text-lg font-medium text-gray-900 bg-blue-50 p-6 rounded-2xl border-l-4 border-blue-500 mb-12">
                        <?php echo __('payment_methods.content.intro'); ?>
                    </p>

                    <hr class="my-10 border-gray-100">

                    <!-- Credit Cards -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-cyan-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-credit-card text-cyan-600 text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 m-0">
                                <?php echo __('payment_methods.content.cards.title'); ?>
                            </h2>
                        </div>
                        <p class="mb-6"><?php echo __('payment_methods.content.cards.text'); ?></p>
                        
                        <div class="flex flex-wrap gap-4 mb-8">
                            <?php foreach(__('payment_methods.content.cards.items') as $card): ?>
                            <div class="flex items-center bg-gray-50 px-6 py-3 rounded-xl border border-gray-100 shadow-sm">
                                <i class="fab fa-cc-<?php echo strtolower(str_replace(' ', '-', $card)); ?> text-2xl text-gray-400 mr-3"></i>
                                <span class="font-bold text-gray-700"><?php echo $card; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Bank Transfer -->
                    <div class="mb-12 bg-gradient-to-br from-gray-50 to-blue-50 p-8 rounded-3xl border border-blue-100 shadow-inner">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                <i class="fas fa-university text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 m-0">
                                <?php echo __('payment_methods.content.transfer.title'); ?>
                            </h2>
                        </div>
                        <p class="mb-4 font-medium"><?php echo __('payment_methods.content.transfer.text'); ?></p>
                        <div class="bg-blue-600 text-white p-4 rounded-xl text-sm font-bold flex items-center">
                            <i class="fas fa-info-circle mr-3"></i>
                            <?php echo __('payment_methods.content.transfer.note'); ?>
                        </div>
                    </div>

                    <!-- PayPal -->
                    <div class="mb-12 bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-3xl border border-indigo-100 shadow-inner">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-[#003087] rounded-xl flex items-center justify-center mr-4 shadow-lg">
                                <i class="fab fa-paypal text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 m-0"><?php echo __('payment_methods.content.paypal.title', 'PayPal'); ?></h2>
                        </div>
                        <p class="mb-4 font-medium text-gray-700">
                            <?php echo __('payment_methods.content.paypal.text'); ?>
                        </p>
                        <div class="flex items-center gap-4">
                            <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg" alt="PayPal Accepted" class="h-10 rounded-lg shadow-sm">
                            <div class="bg-[#0070ba] text-white px-5 py-2 rounded-xl text-sm font-bold flex items-center">
                                <i class="fab fa-paypal mr-2"></i> PayPal
                            </div>
                        </div>
                    </div>

                    <!-- Security -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-shield-alt text-green-600 text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 m-0">
                                <?php echo __('payment_methods.content.security.title'); ?>
                            </h2>
                        </div>
                        <div class="bg-green-50 p-6 rounded-2xl border-l-4 border-green-500 text-green-900">
                            <p class="m-0 leading-relaxed font-medium"><?php echo __('payment_methods.content.security.text'); ?></p>
                        </div>
                    </div>

                    <hr class="my-10 border-gray-100">

                    <!-- Contact -->
                    <div class="text-center py-10">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('payment_methods.content.contact.title'); ?></h2>
                        <p class="mb-8"><?php echo __('payment_methods.content.contact.text'); ?></p>
                        
                        <a href="/contact" class="inline-block bg-white border-2 border-cyan-500 p-6 rounded-2xl shadow-lg hover:shadow-cyan-200 transition transform hover:scale-105">
                            <p class="text-xl font-bold text-cyan-700"><?php echo __('payment_methods.content.contact.name'); ?></p>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
