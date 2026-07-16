<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-60 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/slide/SLIDE5.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-700/60 to-cyan-700/80"></div>
        
        <!-- Animated Circles -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-40 h-40 border-4 border-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-60 h-60 border-4 border-white rounded-full animate-pulse"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6"><?php echo __('contact.hero.title'); ?></h1>
                <p class="text-xl md:text-2xl text-blue-100">
                    <?php echo __('contact.hero.subtitle'); ?>
                </p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid md:grid-cols-2 gap-12">
                    
                    <!-- Contact Form -->
                    <div>
                        <div class="mb-8">
                            <div class="inline-block mb-4">
                                <span class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-6 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                                    <?php echo __('contact.form.badge'); ?>
                                </span>
                            </div>
                            <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
                                <?php echo __('contact.form.title'); ?>
                            </h2>
                            <p class="text-gray-600 text-lg">
                                <?php echo __('contact.form.text'); ?>
                            </p>
                        </div>

                        <form id="contactForm" class="space-y-6">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2"><?php echo __('contact.form.name'); ?></label>
                                <input type="text" name="name" required 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2"><?php echo __('contact.form.email'); ?></label>
                                <input type="email" name="email" required 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2"><?php echo __('contact.form.phone'); ?></label>
                                <input type="tel" name="phone" 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2"><?php echo __('contact.form.subject'); ?></label>
                                <select name="subject" required 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                                    <option value=""><?php echo __('contact.form.subject_default'); ?></option>
                                    <option value="course-inquiry"><?php echo __('contact.form.subjects.course'); ?></option>
                                    <option value="certification"><?php echo __('contact.form.subjects.cert'); ?></option>
                                    <option value="enrollment"><?php echo __('contact.form.subjects.enroll'); ?></option>
                                    <option value="general"><?php echo __('contact.form.subjects.general'); ?></option>
                                    <option value="other"><?php echo __('contact.form.subjects.other'); ?></option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-semibold mb-2"><?php echo __('contact.form.message'); ?></label>
                                <textarea name="message" rows="5" required 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-cyan-500 focus:outline-none transition"></textarea>
                            </div>

                            <button type="submit" 
                                class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 text-white px-8 py-4 rounded-xl font-bold hover:from-cyan-600 hover:to-blue-700 transition transform hover:scale-105 shadow-xl shine-effect">
                                <i class="fas fa-paper-plane mr-2"></i><?php echo __('contact.form.btn'); ?>
                            </button>
                        </form>
                    </div>

                    <!-- Contact Information -->
                    <div>
                        <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl p-8 text-white mb-8 shadow-2xl">
                            <h3 class="text-2xl font-bold mb-6"><?php echo __('contact.info.title'); ?></h3>
                            
                            <div class="space-y-6">
                                <div class="flex items-start">
                                    <div class="bg-white/20 rounded-lg p-3 mr-4 flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold mb-1"><?php echo __('footer.address'); ?></h4>
                                        <p class="text-blue-100">Panama City, Parque Lefevre, Panamá</p>
                                        <p class="text-blue-100">St. 102 E, Panama Viejo Business Center</p>
                                        <p class="text-blue-100">G15-3(B), Panama City, Panama, PTY</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="bg-white/20 rounded-lg p-3 mr-4 flex-shrink-0">
                                        <i class="fas fa-phone text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold mb-1"><?php echo __('footer.phone'); ?></h4>
                                        <p class="text-blue-100">(507) 391-7492</p>
                                        <p class="text-blue-100">(507) 6298-8137</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="bg-white/20 rounded-lg p-3 mr-4 flex-shrink-0">
                                        <i class="fas fa-envelope text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold mb-1"><?php echo __('footer.email'); ?></h4>
                                        <p class="text-blue-100">info@pamel.edu.pa</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <div class="bg-white/20 rounded-lg p-3 mr-4 flex-shrink-0">
                                        <i class="fas fa-clock text-2xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold mb-1"><?php echo __('contact.info.hours'); ?></h4>
                                        <p class="text-blue-100"><?php echo __('contact.info.hours_text'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="border-t border-white/20 pt-4 mt-4">
                                    <p class="text-blue-100 text-sm"><i class="fas fa-graduation-cap mr-2"></i>Training Center • Courses</p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="bg-gray-50 rounded-2xl p-8">
                            <h3 class="text-2xl font-bold text-gray-900 mb-4"><?php echo __('contact.info.social_title'); ?></h3>
                            <p class="text-gray-600 mb-6"><?php echo __('contact.info.social_text'); ?></p>
                            
                            <div class="flex space-x-4">
                                <a href="https://www.instagram.com/pamel_elearning/" target="_blank" class="bg-gradient-to-br from-cyan-500 to-blue-600 text-white w-12 h-12 rounded-full flex items-center justify-center hover:scale-110 transition transform shadow-lg">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="mt-8 bg-white rounded-2xl p-8 shadow-lg">
                            <h3 class="text-xl font-bold text-gray-900 mb-4"><?php echo __('contact.quick_links.title'); ?></h3>
                            <ul class="space-y-3">
                                <li>
                                    <a href="/shop" class="text-gray-600 hover:text-cyan-600 transition flex items-center">
                                        <i class="fas fa-chevron-right text-cyan-500 mr-2 text-sm"></i>
                                        <?php echo __('contact.quick_links.browse'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="/our-company" class="text-gray-600 hover:text-cyan-600 transition flex items-center">
                                        <i class="fas fa-chevron-right text-cyan-500 mr-2 text-sm"></i>
                                        <?php echo __('contact.quick_links.about'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://rucmapamel.com/consult/validate" target="_blank" class="text-gray-600 hover:text-cyan-600 transition flex items-center">
                                        <i class="fas fa-chevron-right text-cyan-500 mr-2 text-sm"></i>
                                        <?php echo __('contact.quick_links.validation'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Offices and Location Section -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center"><?php echo __('contact.offices_title'); ?></h2>
                
                <!-- Office Image -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl mb-8">
                    <img src="/assets/images/location.webp" alt="PAMEL Office" class="w-full h-auto">
                </div>
                
                <!-- Google Maps -->
                <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border-8 border-white">
                    <div class="relative h-[450px]">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.5738696381395!2d-79.488374!3d9.0112995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8faca9650f6aaae5%3A0x5afcb580ad86eda5!2sPanama%20Maritime%20E-learning%20(PAMEL)%20S.A.!5e0!3m2!1ses-419!2spa!4v1776706292449!5m2!1ses-419!2spa" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full h-full">
                        </iframe>
                        
                        <!-- Floating Action Badge -->
                        <div class="absolute bottom-6 left-6 right-6 md:left-auto md:right-8">
                            <a href="https://maps.app.goo.gl/9R6YQnZ2j9A5b8G7A" target="_blank" 
                               class="flex items-center justify-center gap-3 bg-white/90 backdrop-blur-md px-8 py-4 rounded-2xl shadow-xl text-cyan-700 font-bold hover:bg-white transition group">
                                <i class="fas fa-directions text-xl group-hover:rotate-12 transition"></i>
                                <span><?php echo __('branches.map.btn'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><?php echo __('contact.form.sending'); ?>';
    
    const formData = new FormData(this);
    
    fetch('/contact/submit', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            this.reset();
        } else {
            alert('❌ Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
