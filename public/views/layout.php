<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) . ' - ' : ''; ?>PAMEL - Centro de Capacitación Marítima</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%);
            position: relative;
            overflow: hidden;
        }
        
        .hero-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            animation: moveBackground 20s linear infinite;
        }
        
        @keyframes moveBackground {
            0% { background-position: 0 0; }
            100% { background-position: 100px 100px; }
        }
        
        .card-hover {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .card-hover:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .navbar-glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }
        
        .maritime-border {
            border: 3px solid transparent;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, #0ea5e9, #0284c7, #0369a1) border-box;
        }
        
        .shine-effect {
            position: relative;
            overflow: hidden;
        }
        
        .shine-effect::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .shine-effect:hover::after {
            left: 100%;
        }
        
        .price-badge {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header / Navbar -->
    <nav class="navbar-glass shadow-xl sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-3">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3 hover:opacity-80 transition">
                    <div class="relative group">
                        <img src="/assets/images/logo.png" alt="PAMEL Logo" class="h-16">
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-cyan-600 to-blue-700 bg-clip-text text-transparent">
                            PAMEL
                        </h1>
                        <p class="text-xs text-gray-600 font-medium"><?php echo __('nav.maritime_center'); ?></p>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-1">
                    <!-- Store Courses with Submenu -->
                    <div class="relative group">
                        <button class="px-4 py-2 text-gray-700 hover:text-cyan-600 font-medium transition rounded-lg hover:bg-cyan-50 relative flex items-center">
                            <span><?php echo __('nav.store_courses'); ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-500 to-blue-600 group-hover:w-full transition-all duration-300"></span>
                        </button>
                        <div class="absolute left-0 mt-0 w-64 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <!-- Pamel Section -->
                            <div class="border-b border-gray-200">
                                <div class="px-4 py-2 bg-gradient-to-r from-cyan-50 to-blue-50">
                                    <p class="text-xs font-bold text-cyan-700 uppercase tracking-wide">Pamel</p>
                                </div>
                                <div class="relative group/omi">
                                    <a href="/shop?category=pamel&subcategory=omi" class="flex items-center justify-between px-6 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition">
                                        <span><i class="fas fa-ship mr-2 text-cyan-500"></i>OMI</span>
                                        <i class="fas fa-chevron-right text-xs"></i>
                                    </a>
                                    <!-- OMI Submenu -->
                                    <div class="absolute left-full top-0 w-48 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover/omi:opacity-100 group-hover/omi:visible transition-all duration-300 ml-1">
                                        <a href="/shop?category=pamel&subcategory=omi&modality=B-learning" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition rounded-t-lg">
                                            <i class="fas fa-laptop mr-2 text-blue-500"></i>B-learning
                                        </a>
                                        <a href="/shop?category=pamel&subcategory=omi&modality=E-learning" class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition rounded-b-lg">
                                            <i class="fas fa-desktop mr-2 text-blue-500"></i>E-learning
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Latin Indo Section -->
                            <div>
                                <div class="px-4 py-2 bg-gradient-to-r from-green-50 to-emerald-50">
                                    <p class="text-xs font-bold text-green-700 uppercase tracking-wide">Latin Indo</p>
                                </div>
                                <a href="/shop?category=latin&subcategory=omi" class="block px-6 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition rounded-b-lg">
                                    <i class="fas fa-anchor mr-2 text-green-500"></i>OMI
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- About Us with Submenu -->
                    <div class="relative group">
                        <button class="px-4 py-2 text-gray-700 hover:text-cyan-600 font-medium transition rounded-lg hover:bg-cyan-50 relative flex items-center">
                            <span><?php echo __('nav.about_us'); ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-500 to-blue-600 group-hover:w-full transition-all duration-300"></span>
                        </button>
                        <div class="absolute left-0 mt-0 w-64 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <a href="/our-company" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition rounded-t-lg"><?php echo __('nav.about.company'); ?></a>
                            <a href="/quality-policy" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition"><?php echo __('nav.about.quality'); ?></a>
                            <!-- Branches with nested submenu -->
                            <div class="relative group/branches">
                                <button class="w-full text-left px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition flex items-center justify-between">
                                    <span><?php echo __('nav.about.branches'); ?></span>
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                                <div class="absolute left-full top-0 ml-1 w-64 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover/branches:opacity-100 group-hover/branches:visible transition-all duration-300">
                                    <a href="/branches" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition rounded-t-lg"><?php echo __('nav.about.all_branches'); ?></a>
                                    <a href="/limr" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition rounded-b-lg"><?php echo __('nav.about.limr'); ?></a>
                                </div>
                            </div>
                            
                            <a href="/gcl-iso" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition rounded-b-lg"><?php echo __('nav.about.iso'); ?></a>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="px-4 py-2 text-gray-700 hover:text-cyan-600 font-medium transition rounded-lg hover:bg-cyan-50 relative flex items-center">
                            <span><?php echo __('nav.services'); ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-500 to-blue-600 group-hover:w-full transition-all duration-300"></span>
                        </button>
                        <div class="absolute left-0 mt-0 w-56 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <a href="/services" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition rounded-t-lg"><?php echo __('nav.competence_courses'); ?></a>
                        </div>
                    </div>

                    <!-- E-Platform -->
                     <div class="relative group">
                        <button class="px-4 py-2 text-gray-700 hover:text-cyan-600 font-medium transition rounded-lg hover:bg-cyan-50 relative flex items-center">
                            <span><?php echo __('nav.e_platform'); ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-500 to-blue-600 group-hover:w-full transition-all duration-300"></span>
                        </button>
                        <div class="absolute left-0 mt-0 w-56 bg-white rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <a href="https://elearning.pamel.edu.pa" target="_blank" class="block px-4 py-3 text-gray-700 hover:bg-cyan-50 hover:text-cyan-600 transition rounded-t-lg"><?php echo __('nav.elearning'); ?></a>
                        </div>
                    </div>


                    <!-- Certificate Validation -->
                    <a href="https://rucmapamel.com/consult/validate" target="_blank" class="px-4 py-2 text-gray-700 hover:text-cyan-600 font-medium transition rounded-lg hover:bg-cyan-50 relative group">
                        <span><?php echo __('nav.cert_validation'); ?></span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-500 to-blue-600 group-hover:w-full transition-all duration-300"></span>
                    </a>

                    <!-- Contact Us -->
                    <a href="/contact" class="px-4 py-2 text-gray-700 hover:text-cyan-600 font-medium transition rounded-lg hover:bg-cyan-50 relative group">
                        <span><?php echo __('nav.contact'); ?></span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-500 to-blue-600 group-hover:w-full transition-all duration-300"></span>
                    </a>
                </div>

                <!-- Cart & User -->
                <div class="flex items-center space-x-3">
                    <!-- Language Switcher -->
                    <div class="flex items-center space-x-2 mr-2 border-r pr-4 border-gray-200">
                        <a href="?lang=en" class="text-xs font-extrabold <?php echo \Core\Language::getLocale() === 'en' ? 'text-cyan-600 underline' : 'text-gray-400 hover:text-cyan-600'; ?> transition">EN</a>
                        <span class="text-gray-300 text-xs">|</span>
                        <a href="?lang=es" class="text-xs font-extrabold <?php echo \Core\Language::getLocale() === 'es' ? 'text-cyan-600 underline' : 'text-gray-400 hover:text-cyan-600'; ?> transition">ES</a>
                    </div>

                    <a href="/cart" class="relative text-gray-700 hover:text-cyan-600 transition p-2 hover:bg-cyan-50 rounded-full group">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold shadow-lg group-hover:scale-110 transition">0</span>
                    </a>
                    
                   

                    <a href="/my-account" class="px-4 py-2 text-gray-700 hover:text-cyan-600 font-medium transition rounded-lg hover:bg-cyan-50 relative group">
                         <i class="fas fa-user text-xl"></i>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-cyan-500 to-blue-600 group-hover:w-full transition-all duration-300"></span>
                    </a>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="lg:hidden text-gray-700 p-2 hover:bg-cyan-50 rounded-lg">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black/70 z-[100] hidden lg:hidden opacity-0 transition-opacity duration-300"></div>

        <!-- Mobile Menu Sidebar -->
        <div id="mobile-menu" class="fixed top-0 right-0 h-full w-[300px] bg-white z-[101] lg:hidden transform translate-x-full transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
            <!-- Header -->
            <div class="p-6 border-b flex justify-between items-center bg-gradient-to-r from-cyan-600 to-blue-700 text-white shadow-lg">
                <div class="flex items-center space-x-3">
                    <img src="/assets/images/logo.png" alt="Logo" class="h-10 bg-white p-1 rounded-lg">
                    <div>
                        <h2 class="text-xl font-bold leading-none">PAMEL</h2>
                        <p class="text-[9px] uppercase tracking-widest opacity-80 mt-1"><?php echo __('nav.maritime_center'); ?></p>
                    </div>
                </div>
                <button id="close-mobile-menu" class="text-white hover:bg-white/20 w-10 h-10 rounded-full flex items-center justify-center transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="flex-1 overflow-y-auto py-6">
                <!-- Navigation Groups -->
                <div class="px-4 space-y-8">
                    <!-- Store Courses -->
                    <div>
                        <p class="text-[11px] font-black text-cyan-600 uppercase tracking-[0.2em] mb-4 px-2 border-l-4 border-cyan-500"><?php echo __('nav.store_courses'); ?></p>
                        <div class="space-y-2">
                            <a href="/shop?category=pamel&subcategory=omi" class="flex items-center px-4 py-4 bg-gray-50 hover:bg-cyan-50 text-gray-900 font-bold rounded-2xl transition group">
                                <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mr-3 group-hover:scale-110 transition">
                                    <i class="fas fa-ship text-cyan-500"></i>
                                </div>
                                <span>Pamel OMI</span>
                            </a>
                            <a href="/shop?category=latin&subcategory=omi" class="flex items-center px-4 py-4 bg-gray-50 hover:bg-emerald-50 text-gray-900 font-bold rounded-2xl transition group">
                                <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center mr-3 group-hover:scale-110 transition">
                                    <i class="fas fa-anchor text-emerald-500"></i>
                                </div>
                                <span>Latin Indo OMI</span>
                            </a>
                        </div>
                    </div>

                    <!-- About Us -->
                    <div>
                        <p class="text-[11px] font-black text-cyan-600 uppercase tracking-[0.2em] mb-4 px-2 border-l-4 border-cyan-500"><?php echo __('nav.about_us'); ?></p>
                        <div class="space-y-1">
                            <a href="/our-company" class="flex items-center px-4 py-3 text-gray-700 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition font-medium">
                                <i class="fas fa-building w-8 text-gray-400 group-hover:text-cyan-500"></i>
                                <span><?php echo __('nav.about.company'); ?></span>
                            </a>
                            <a href="/quality-policy" class="flex items-center px-4 py-3 text-gray-700 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition font-medium">
                                <i class="fas fa-clipboard-check w-8 text-gray-400 group-hover:text-cyan-500"></i>
                                <span><?php echo __('nav.about.quality'); ?></span>
                            </a>
                            <a href="/branches" class="flex items-center px-4 py-3 text-gray-700 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition font-medium">
                                <i class="fas fa-map-marked-alt w-8 text-gray-400 group-hover:text-cyan-500"></i>
                                <span><?php echo __('nav.about.branches'); ?></span>
                            </a>
                            <a href="/gcl-iso" class="flex items-center px-4 py-3 text-gray-700 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition font-medium">
                                <i class="fas fa-certificate w-8 text-gray-400 group-hover:text-cyan-500"></i>
                                <span><?php echo __('nav.about.iso'); ?></span>
                            </a>
                        </div>
                    </div>

                    <!-- Others -->
                    <div>
                        <p class="text-[11px] font-black text-cyan-600 uppercase tracking-[0.2em] mb-4 px-2 border-l-4 border-cyan-500">Platform & Services</p>
                        <div class="space-y-1">
                            <a href="/services" class="flex items-center px-4 py-3 text-gray-700 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition font-medium">
                                <i class="fas fa-graduation-cap w-8 text-gray-400"></i>
                                <span><?php echo __('nav.competence_courses'); ?></span>
                            </a>
                            <a href="https://elearning.pamel.edu.pa/login/index.php" target="_blank" class="flex items-center px-4 py-3 text-gray-700 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition font-medium">
                                <i class="fas fa-laptop-code w-8 text-gray-400"></i>
                                <span><?php echo __('nav.elearning'); ?></span>
                            </a>
                            <a href="/contact" class="flex items-center px-4 py-3 text-gray-700 hover:text-cyan-600 hover:bg-cyan-50 rounded-xl transition font-medium">
                                <i class="fas fa-envelope w-8 text-gray-400"></i>
                                <span><?php echo __('nav.contact'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Language & Account Section -->
            <div class="p-6 bg-gray-50 border-t space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Language</span>
                    <div class="flex bg-white p-1 rounded-lg shadow-sm border border-gray-100">
                        <a href="?lang=en" class="text-xs font-bold px-4 py-1.5 rounded-md transition <?php echo \Core\Language::getLocale() === 'en' ? 'bg-cyan-600 text-white shadow-md' : 'text-gray-500 hover:text-cyan-600'; ?>">EN</a>
                        <a href="?lang=es" class="text-xs font-bold px-4 py-1.5 rounded-md transition <?php echo \Core\Language::getLocale() === 'es' ? 'bg-cyan-600 text-white shadow-md' : 'text-gray-500 hover:text-cyan-600'; ?>">ES</a>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="/my-account" class="flex flex-col items-center p-4 bg-white border border-gray-100 rounded-2xl text-gray-700 hover:border-cyan-200 hover:bg-cyan-50 transition shadow-sm">
                        <i class="fas fa-user text-cyan-600 mb-2"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Account</span>
                    </a>
                    <a href="/cart" class="flex flex-col items-center p-4 bg-white border border-gray-100 rounded-2xl text-gray-700 hover:border-cyan-200 hover:bg-cyan-50 transition shadow-sm">
                        <i class="fas fa-shopping-cart text-cyan-600 mb-2"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Cart</span>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Main Content -->
    <main>
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 items-start">
                
                <!-- PAMEL Logo & Social -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <img src="/assets/images/LogoSelloPamel-EditedWhite.png" alt="PAMEL Logo" class="h-48">
                    </div>
                    <h3 class="text-xl font-bold mb-2"><?php echo __('footer.training_center'); ?></h3>
                    <div class="flex space-x-3 mt-4">
                        <a href="https://www.instagram.com/pamel_elearning/" target="_blank" class="bg-gray-700 hover:bg-gray-600 w-9 h-9 rounded flex items-center justify-center transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>

                <!-- Information -->
                <div>
                    <h3 class="text-xl font-bold mb-4"><?php echo __('footer.information'); ?></h3>
                    <ul class="space-y-2">
                        <li><a href="/shop" class="text-white hover:text-cyan-200 transition"><?php echo __('footer.courses'); ?></a></li>
                        <li><a href="/payment-methods" class="text-white hover:text-cyan-200 transition"><?php echo __('footer.payment'); ?></a></li>
                        <li><a href="/our-company" class="text-white hover:text-cyan-200 transition"><?php echo __('nav.about_us'); ?></a></li>
                        <li><a href="/contact" class="text-white hover:text-cyan-200 transition"><?php echo __('nav.contact'); ?></a></li>
                    </ul>
                </div>

                <!-- Legal Links -->
                <div>
                    <h3 class="text-xl font-bold mb-4"><?php echo __('footer.legal'); ?></h3>
                    <ul class="space-y-2 mb-4">
                        <li><a href="/privacy-policy" class="text-white hover:text-cyan-200 transition"><?php echo __('footer.privacy'); ?></a></li>
                        <li><a href="/cookie-policy" class="text-white hover:text-cyan-200 transition"><?php echo __('footer.cookies'); ?></a></li>
                        <li><a href="/terms-and-conditions" class="text-white hover:text-cyan-200 transition"><?php echo __('footer.terms'); ?></a></li>
                    </ul>
                    <!-- ISO Certification Logo -->
                    <div class="mt-4">
                        <img src="/assets/images/ISO-9001_160x128px.png" alt="ISO 9001 Certification" class="bg-white p-2 rounded">
                    </div>
                </div>

                <!-- Maritime Authority Approval -->
                <div>
                    <p class="text-sm leading-relaxed mb-4">
                        <?php echo __('footer.approval'); ?>
                    </p>
                    <div class="mt-4">
                        <img src="/assets/images/AMP-LOGO-2-01-resized-trans.png" alt="Autoridad Marítima de Panamá">
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-8 right-8 bg-gradient-to-r from-cyan-500 to-blue-600 text-white w-14 h-14 rounded-full shadow-2xl hover:from-cyan-600 hover:to-blue-700 transition-all transform hover:scale-110 hidden z-50">
        <i class="fas fa-arrow-up text-xl"></i>
    </button>

    <!-- Cookie Consent Banner -->
    <div id="cookie-banner" class="fixed bottom-0 left-0 right-0 z-[200] p-4 transform translate-y-full transition-transform duration-500 ease-in-out hidden">
        <div class="container mx-auto max-w-5xl">
            <div class="bg-white/95 backdrop-blur-md p-6 md:p-8 rounded-3xl shadow-[0_-15px_50px_-15px_rgba(0,0,0,0.1)] border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    <div class="hidden md:flex w-16 h-16 bg-cyan-50 rounded-2xl items-center justify-center flex-shrink-0">
                        <i class="fas fa-cookie-bite text-3xl text-cyan-600 floating"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1"><?php echo __('cookies.hero.title'); ?></h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            <?php echo __('cookies.banner.text'); ?>
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    <button id="accept-cookies" class="flex-1 md:flex-none px-8 py-3 bg-gradient-to-r from-cyan-600 to-blue-700 text-white font-bold rounded-xl hover:shadow-lg hover:scale-105 transition transform active:scale-95">
                        <?php echo __('cookies.banner.accept'); ?>
                    </button>
                    <a href="/cookie-policy" class="flex-1 md:flex-none px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 text-center transition">
                        <i class="fas fa-cog mr-2"></i><?php echo __('cookies.banner.configure'); ?>
                    </a>
                    <button id="reject-cookies" class="px-6 py-3 text-gray-400 hover:text-red-500 font-medium transition text-sm">
                        <?php echo __('cookies.banner.reject'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Back to top button
        const backToTop = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.remove('hidden');
            } else {
                backToTop.classList.add('hidden');
            }
        });

        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Mobile Menu Logic
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const closeMobileMenu = document.getElementById('close-mobile-menu');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

        function openMenu() {
            mobileMenuOverlay.classList.remove('hidden');
            setTimeout(() => {
                mobileMenuOverlay.classList.add('opacity-100');
                mobileMenu.classList.remove('translate-x-full');
            }, 10);
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeMenu() {
            mobileMenuOverlay.classList.remove('opacity-100');
            mobileMenu.classList.add('translate-x-full');
            setTimeout(() => {
                mobileMenuOverlay.classList.add('hidden');
            }, 300);
            document.body.style.overflow = ''; // Restore scrolling
        }

        mobileMenuButton.addEventListener('click', openMenu);
        closeMobileMenu.addEventListener('click', closeMenu);
        mobileMenuOverlay.addEventListener('click', closeMenu);

        // Cookie Banner Logic
        const cookieBanner = document.getElementById('cookie-banner');
        const acceptCookies = document.getElementById('accept-cookies');
        const rejectCookies = document.getElementById('reject-cookies');

        if (!localStorage.getItem('cookies_accepted')) {
            setTimeout(() => {
                cookieBanner.classList.remove('hidden');
                setTimeout(() => {
                    cookieBanner.classList.remove('translate-y-full');
                }, 100);
            }, 2000); // Show after 2 seconds
        }

        function hideCookieBanner() {
            cookieBanner.classList.add('translate-y-full');
            setTimeout(() => {
                cookieBanner.classList.add('hidden');
            }, 500);
        }

        acceptCookies.addEventListener('click', () => {
            localStorage.setItem('cookies_accepted', 'true');
            hideCookieBanner();
        });

        rejectCookies.addEventListener('click', () => {
            localStorage.setItem('cookies_accepted', 'false');
            hideCookieBanner();
        });
    </script>

</body>
</html>
