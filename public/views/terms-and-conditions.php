<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->

    <section class="hero-gradient text-white py-60 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/slide/SLIDE1.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-700/60 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('terms.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                     <?php echo __('terms.hero.subtitle'); ?>
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
    <section class="py-20 relative z-20">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl p-8 md:p-16 border border-gray-100">
                
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    <!-- Intro -->
                    <p class="text-lg font-medium text-gray-900 bg-cyan-50 p-6 rounded-2xl border-l-4 border-cyan-500 mb-12">
                        <?php echo __('terms.content.intro'); ?>
                    </p>

                    <hr class="my-10 border-gray-100">

                    <!-- Use of Platform -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-laptop text-cyan-600 mr-3"></i>
                            <?php echo __('terms.content.usage.title'); ?>
                        </h2>
                        <p class="mb-4"><?php echo __('terms.content.usage.text'); ?></p>
                        <p class="font-bold mb-3"><?php echo __('terms.content.usage.user_commits'); ?></p>
                        <ul class="space-y-2">
                            <?php foreach(__('terms.content.usage.items') as $item): ?>
                            <li class="flex items-start">
                                <i class="fas fa-check text-cyan-500 mt-1.5 mr-3 text-sm"></i>
                                <span><?php echo $item; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Registration -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-id-card text-cyan-600 mr-3"></i>
                            <?php echo __('terms.content.registration.title'); ?>
                        </h2>
                        <p class="mb-4"><?php echo __('terms.content.registration.text'); ?></p>
                        <p class="font-bold mb-3"><?php echo __('terms.content.registration.responsibility'); ?></p>
                        <ul class="space-y-2 mb-6">
                            <?php foreach(__('terms.content.registration.items') as $item): ?>
                            <li class="flex items-start">
                                <i class="fas fa-user-check text-cyan-500 mt-1.5 mr-3 text-sm"></i>
                                <span><?php echo $item; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="bg-blue-50 p-4 rounded-xl text-blue-800 text-sm border-l-4 border-blue-400">
                            <i class="fas fa-shield-alt mr-2"></i><?php echo __('terms.content.registration.verification'); ?>
                        </div>
                    </div>

                    <!-- Identity Control -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-fingerprint text-cyan-600 mr-3"></i>
                            <?php echo __('terms.content.identity_control.title'); ?>
                        </h2>
                        <p class="mb-4"><?php echo __('terms.content.identity_control.text'); ?></p>
                        <ul class="space-y-2 mb-4">
                            <?php foreach(__('terms.content.identity_control.items') as $item): ?>
                            <li class="flex items-center text-gray-600">
                                <span class="w-1.5 h-1.5 bg-cyan-500 rounded-full mr-3"></span>
                                <?php echo $item; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="text-sm italic text-gray-500"><?php echo __('terms.content.identity_control.validity'); ?></p>
                    </div>

                    <!-- Academic Management -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-university text-cyan-600 mr-3"></i>
                            <?php echo __('terms.content.management.title'); ?>
                        </h2>
                        <p class="mb-4"><?php echo __('terms.content.management.text'); ?></p>
                        <p class="font-bold mb-3"><?php echo __('terms.content.management.rights'); ?></p>
                        <ul class="space-y-2">
                            <?php foreach(__('terms.content.management.items') as $item): ?>
                            <li class="flex items-start">
                                <i class="fas fa-gavel text-cyan-500 mt-1.5 mr-3 text-sm"></i>
                                <span><?php echo $item; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Certifications -->
                    <div class="mb-12 bg-gradient-to-br from-white to-cyan-50 p-8 rounded-3xl border border-cyan-100 shadow-inner">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-certificate text-cyan-600 mr-3"></i>
                            <?php echo __('terms.content.certifications.title'); ?>
                        </h2>
                        <p class="mb-4"><?php echo __('terms.content.certifications.text'); ?></p>
                        <p class="font-bold mb-3"><?php echo __('terms.content.certifications.conditions'); ?></p>
                        <ul class="space-y-2 mb-6">
                            <?php foreach(__('terms.content.certifications.items') as $item): ?>
                            <li class="flex items-start">
                                <i class="fas fa-award text-yellow-500 mt-1.5 mr-3 text-sm"></i>
                                <span><?php echo $item; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="bg-white p-4 rounded-xl shadow-sm text-sm border border-gray-100">
                            <i class="fas fa-file-alt text-cyan-500 mr-2"></i><?php echo __('terms.content.certifications.evidence'); ?>
                        </p>
                    </div>

                    <!-- Fraud Policy -->
                    <div class="mb-12 border-2 border-red-50 p-8 rounded-3xl">
                        <h2 class="text-2xl font-bold text-red-600 mb-4 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-3"></i>
                            <?php echo __('terms.content.fraud_policy.title'); ?>
                        </h2>
                        <p class="font-bold text-gray-900 mb-3"><?php echo __('terms.content.fraud_policy.prohibited'); ?></p>
                        <ul class="grid md:grid-cols-2 gap-3 mb-6">
                            <?php foreach(__('terms.content.fraud_policy.items') as $item): ?>
                            <li class="flex items-center text-red-700 bg-red-50/50 px-3 py-2 rounded-lg text-sm">
                                <i class="fas fa-times-circle mr-2 opacity-50"></i>
                                <?php echo $item; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="font-bold text-gray-900 mb-3"><?php echo __('terms.content.fraud_policy.actions'); ?></p>
                        <ul class="space-y-2">
                            <?php foreach(__('terms.content.fraud_policy.action_items') as $item): ?>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-ban text-red-500 mr-3"></i>
                                <?php echo $item; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Intellectual Property -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-copyright text-cyan-600 mr-3"></i>
                            <?php echo __('terms.content.intellectual_property.title'); ?>
                        </h2>
                        <p class="mb-4"><?php echo __('terms.content.intellectual_property.text'); ?></p>
                        <p class="font-bold mb-3"><?php echo __('terms.content.intellectual_property.prohibited'); ?></p>
                        <ul class="space-y-2">
                            <?php foreach(__('terms.content.intellectual_property.items') as $item): ?>
                            <li class="flex items-start">
                                <i class="fas fa-lock text-gray-400 mt-1.5 mr-3 text-sm"></i>
                                <span><?php echo $item; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Availability & Liability -->
                    <div class="grid md:grid-cols-2 gap-8 mb-12">
                        <div class="bg-gray-50 p-6 rounded-2xl">
                            <h3 class="font-bold text-gray-900 mb-2 flex items-center">
                                <i class="fas fa-clock text-cyan-600 mr-2"></i>
                                <?php echo __('terms.content.availability.title'); ?>
                            </h3>
                            <p class="text-sm text-gray-600"><?php echo __('terms.content.availability.text'); ?></p>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-2xl">
                            <h3 class="font-bold text-gray-900 mb-2 flex items-center">
                                <i class="fas fa-balance-scale text-cyan-600 mr-2"></i>
                                <?php echo __('terms.content.limitation.title'); ?>
                            </h3>
                            <ul class="text-xs text-gray-600 space-y-1">
                                <?php foreach(__('terms.content.limitation.items') as $item): ?>
                                <li>• <?php echo $item; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Termination & Law -->
                    <div class="mb-12 flex flex-col md:flex-row gap-8">
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-gray-900 mb-4"><?php echo __('terms.content.termination.title'); ?></h2>
                            <ul class="space-y-1 text-sm text-gray-600">
                                <?php foreach(__('terms.content.termination.items') as $item): ?>
                                <li class="flex items-center"><i class="fas fa-caret-right text-cyan-500 mr-2"></i><?php echo $item; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="flex-1 bg-cyan-900 text-white p-6 rounded-2xl shadow-xl">
                            <h2 class="text-xl font-bold mb-4 flex items-center">
                                <i class="fas fa-gavel text-cyan-400 mr-3"></i>
                                <?php echo __('terms.content.law.title'); ?>
                            </h2>
                            <p class="text-sm opacity-90"><?php echo __('terms.content.law.text'); ?></p>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="text-center py-10 border-t border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('terms.content.contact.title'); ?></h2>
                        <p class="mb-6"><?php echo __('terms.content.contact.text'); ?></p>
                        <a href="/contact" class="inline-block bg-white border-2 border-cyan-500 p-6 rounded-2xl shadow-lg hover:shadow-cyan-200 transition transform hover:scale-105">
                            <p class="text-xl font-bold text-cyan-700"><?php echo __('terms.content.contact.name'); ?></p>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
