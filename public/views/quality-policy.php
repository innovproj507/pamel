<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-60 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/slide/SLIDE9.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-700/60 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('quality_policy.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                    <?php echo __('quality_policy.hero.subtitle'); ?>
                </p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-24 bg-gray-50/50">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                
                <!-- Company Policy - Combined Layout -->
                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-20 border border-gray-100">
                    <div class="grid lg:grid-cols-2 gap-0">
                        <!-- Image Side -->
                        <div class="relative min-h-[500px] lg:h-auto overflow-hidden group">
                            <img src="/assets/images/company-policy1.jpg" alt="Maritime Control Panel" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-cyan-900/60 via-transparent to-transparent"></div>
                            <div class="absolute bottom-8 left-8 text-white">
                                <p class="text-sm font-bold uppercase tracking-widest opacity-80 mb-2">PAMEL S.A.</p>
                                <h3 class="text-2xl font-bold">Commitment to Excellence</h3>
                            </div>
                        </div>

                        <!-- Letter Side -->
                        <div class="relative bg-white p-10 md:p-16 flex flex-col justify-center overflow-hidden">
                            <!-- Decorative Corners -->
                            <div class="absolute top-0 right-0 w-24 h-24 overflow-hidden pointer-events-none">
                                <div class="absolute top-0 right-0 w-full h-full bg-cyan-600 origin-top-right rotate-45 translate-x-1/2 -translate-y-1/2"></div>
                            </div>
                            <div class="absolute bottom-0 left-0 w-24 h-24 overflow-hidden pointer-events-none">
                                <div class="absolute bottom-0 left-0 w-full h-full bg-yellow-400 origin-bottom-left -rotate-45 -translate-x-1/2 translate-y-1/2"></div>
                            </div>

                            <!-- Letter Content -->
                            <div class="relative z-10">
                                <!-- Logo Centered -->
                                <div class="mb-10 flex justify-center">
                                    <img src="/assets/images/logo.png" alt="PAMEL Logo" class="h-24">
                                </div>

                                <h2 class="text-3xl font-extrabold text-blue-700 uppercase tracking-wider mb-8 pb-2 border-b-2 border-blue-50 text-center">
                                    <?php echo __('quality_policy.policy.title'); ?>
                                </h2>

                                <div class="space-y-6 text-gray-700 text-lg leading-relaxed italic text-center">
                                    <?php for($i=1; $i<=4; $i++): 
                                        $para = __('quality_policy.policy.para'.$i);
                                        if(!empty($para) && $para !== 'quality_policy.policy.para'.$i): ?>
                                        <p class="max-w-md mx-auto">
                                            "<?php echo $para; ?>"
                                        </p>
                                    <?php endif; endfor; ?>
                                </div>

                                <!-- Signature Centered -->
                                <div class="mt-12 text-center">
                                    <div class="w-48 h-0.5 bg-blue-200 mb-3 mx-auto"></div>
                                    <p class="font-bold text-gray-900">Jorge Alexander Vergara Benítez</p>
                                    <p class="text-xs text-gray-500 uppercase tracking-widest">General Manager</p>
                                </div>

                                <!-- Doc Info Right Aligned -->
                                <div class="mt-10 text-[10px] text-gray-400 font-mono text-right flex flex-col">
                                    <span>P-01</span>
                                    <span>V.02</span>
                                    <span>15/01/2026</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quality Objectives Grid -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-extrabold text-gray-900 mb-4"><?php echo __('quality_policy.objectives.title'); ?></h2>
                    <div class="w-24 h-1.5 bg-gradient-to-r from-cyan-500 to-blue-600 mx-auto rounded-full"></div>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php 
                    $icons = [
                        '1' => 'fa-smile-beam',
                        '2' => 'fa-file-contract',
                        '3' => 'fa-shield-halved',
                        '4' => 'fa-check-double',
                        '5' => 'fa-headset',
                        '6' => 'fa-life-ring'
                    ];
                    foreach (__('quality_policy.objectives.list') as $key => $objective): 
                    ?>
                    <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 card-hover group">
                        <div class="w-14 h-14 bg-cyan-50 rounded-xl flex items-center justify-center mb-6 group-hover:bg-cyan-500 transition-colors duration-300">
                            <i class="fas <?php echo $icons[$key]; ?> text-2xl text-cyan-600 group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                            <span class="text-cyan-500 mr-2"><?php echo $key; ?>.</span>
                            <?php 
                                // Split text for better visual hierarchy if needed, or just show it
                                echo $objective; 
                            ?>
                        </h3>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- ISO Certification Highlight Footer -->
                <div class="mt-20 bg-gradient-to-br from-gray-900 to-blue-900 rounded-3xl p-12 text-white relative overflow-hidden shadow-2xl">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-cyan-500/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="max-w-2xl">
                            <h3 class="text-3xl font-bold mb-4">ISO 9001:2015 Certified Quality</h3>
                            <p class="text-blue-100 text-lg">
                                Our commitment to quality is backed by international standards, ensuring that every student receives training that meets global maritime excellence.
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <img src="/assets/images/ISO-9001_160x128px.png" alt="ISO 9001" class="bg-white p-4 rounded-2xl shadow-xl h-32">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
