<div class="bg-gray-50 min-h-screen">
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
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('privacy.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                     <?php echo __('privacy.hero.subtitle'); ?>
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
                    <p class="text-lg font-medium text-gray-900 bg-cyan-50 p-6 rounded-2xl border-l-4 border-cyan-500 mb-12">
                        <?php echo __('privacy.content.intro'); ?>
                    </p>

                    <hr class="my-10 border-gray-100">

                    <!-- Personal Info -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user-shield text-cyan-600 mr-3"></i>
                            <?php echo __('privacy.content.personal_info.title'); ?>
                        </h2>
                        <p class="mb-4"><?php echo __('privacy.content.personal_info.text'); ?></p>
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 italic text-gray-600 mb-4">
                            <?php echo __('privacy.content.personal_info.list'); ?>
                        </div>
                        <p><?php echo __('privacy.content.personal_info.anonymous'); ?></p>
                    </div>

                    <!-- Non-Personal Info -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-laptop-code text-cyan-600 mr-3"></i>
                            <?php echo __('privacy.content.non_personal_info.title'); ?>
                        </h2>
                        <p><?php echo __('privacy.content.non_personal_info.text'); ?></p>
                    </div>

                    <!-- Cookies -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-cookie-bite text-cyan-600 mr-3"></i>
                            <?php echo __('privacy.content.cookies.title'); ?>
                        </h2>
                        <p class="mb-4"><?php echo __('privacy.content.cookies.text'); ?></p>
                        <div class="flex items-start bg-blue-50 p-4 rounded-xl text-blue-800 text-sm">
                            <i class="fas fa-info-circle mt-1 mr-3 text-blue-500"></i>
                            <p><?php echo __('privacy.content.cookies.opt_out'); ?></p>
                        </div>
                    </div>

                    <hr class="my-10 border-gray-100">

                    <!-- Usage -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-cogs text-cyan-600 mr-3"></i>
                            <?php echo __('privacy.content.usage.title'); ?>
                        </h2>
                        <p class="mb-6"><?php echo __('privacy.content.usage.intro'); ?></p>
                        <ul class="grid md:grid-cols-1 gap-4">
                            <?php foreach(__('privacy.content.usage.items') as $item): ?>
                            <li class="flex items-start bg-white border border-gray-100 p-4 rounded-xl shadow-sm hover:shadow-md transition">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span><?php echo $item; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Protection -->
                    <div class="mb-12 bg-gradient-to-r from-gray-900 to-blue-900 text-white p-8 rounded-3xl shadow-xl">
                        <h2 class="text-2xl font-bold mb-4 flex items-center">
                            <i class="fas fa-lock text-cyan-400 mr-3"></i>
                            <?php echo __('privacy.content.protection.title'); ?>
                        </h2>
                        <p class="opacity-90 leading-relaxed"><?php echo __('privacy.content.protection.text'); ?></p>
                    </div>

                    <!-- Sharing -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-share-alt text-cyan-600 mr-3"></i>
                            <?php echo __('privacy.content.sharing.title'); ?>
                        </h2>
                        <p class="mb-4"><?php echo __('privacy.content.sharing.text'); ?></p>
                        <p class="font-bold text-gray-800 mb-3"><?php echo __('privacy.content.sharing.cases_intro'); ?></p>
                        <ul class="space-y-2 mb-4">
                            <?php foreach(__('privacy.content.sharing.cases') as $case): ?>
                            <li class="flex items-center text-gray-600">
                                <span class="w-2 h-2 bg-cyan-500 rounded-full mr-3"></span>
                                <?php echo $case; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Newsletters -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-paper-plane text-cyan-600 mr-3"></i>
                            <?php echo __('privacy.content.newsletters.title'); ?>
                        </h2>
                        <p class="mb-2"><?php echo __('privacy.content.newsletters.text'); ?></p>
                        <p class="text-sm text-gray-500 italic"><?php echo __('privacy.content.newsletters.unsubscribe'); ?></p>
                    </div>

                    <!-- Third Party -->
                    <div class="mb-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-external-link-alt text-cyan-600 mr-3"></i>
                            <?php echo __('privacy.content.third_party.title'); ?>
                        </h2>
                        <p><?php echo __('privacy.content.third_party.text'); ?></p>
                    </div>

                    <hr class="my-10 border-gray-100">

                    <!-- Changes & Acceptance -->
                    <div class="grid md:grid-cols-2 gap-8 mb-12">
                        <div class="bg-orange-50 p-6 rounded-2xl border-l-4 border-orange-400">
                            <h3 class="font-bold text-orange-900 mb-2"><?php echo __('privacy.content.changes.title'); ?></h3>
                            <p class="text-sm text-orange-800"><?php echo __('privacy.content.changes.text'); ?></p>
                        </div>
                        <div class="bg-green-50 p-6 rounded-2xl border-l-4 border-green-400">
                            <h3 class="font-bold text-green-900 mb-2"><?php echo __('privacy.content.acceptance.title'); ?></h3>
                            <p class="text-sm text-green-800"><?php echo __('privacy.content.acceptance.text'); ?></p>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div class="text-center py-10 border-t border-gray-100">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('privacy.content.contact.title'); ?></h2>
                        <p class="mb-6"><?php echo __('privacy.content.contact.text'); ?></p>
                        <a href="/contact" class="inline-block bg-white border-2 border-cyan-500 p-6 rounded-2xl shadow-lg hover:shadow-cyan-200 transition transform hover:scale-105">
                            <p class="text-xl font-bold text-cyan-700"><?php echo __('privacy.content.contact.name'); ?></p>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
