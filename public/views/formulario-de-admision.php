<div class="bg-white">
    <!-- Hero Section -->
    <section class="hero-gradient text-white py-32 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/assets/images/barco-ourcompany.jpg');"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/80 via-blue-600/80 to-cyan-700/80"></div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-4"><?php echo __('admission.hero.title'); ?></h1>
                <p class="text-xl text-blue-100"><?php echo __('admission.hero.subtitle'); ?></p>
            </div>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
                <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                
                <!-- Header -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border-t-4 border-cyan-500">
                    <div class="flex items-center justify-between mb-6">
                        <img src="/assets/images/logo.png" alt="PAMEL Logo" class="h-20">
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-sm text-gray-600">F-46</p>
                                <p class="text-sm text-gray-600">V.02</p>
                            </div>
                            <img src="/assets/images/ssss.png" alt="PAMEL Logo" class="h-20">
                        </div>
                    </div>
                    <div class="text-center">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">PANAMA MARITIME E-LEARNING (PAMEL), S.A.</h2>
                        <p class="text-sm text-gray-700 mb-1"><?php echo __('admission.form.quality_system'); ?></p>
                        <p class="text-base font-semibold text-gray-800"><?php echo __('admission.form.header'); ?></p>
                    </div>
                </div>

                <!-- Form -->
                <form id="admissionForm" class="bg-white rounded-2xl shadow-xl p-8" method="POST">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6 pb-4 border-b-2 border-gray-200">
                        <?php echo __('admission.form.personal_info'); ?>
                    </h3>

                    <div class="space-y-6">
                        
                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.full_name'); ?>
                            </label>
                            <input type="text" name="full_name" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- ID or Passport -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.id_passport'); ?>
                            </label>
                            <input type="text" name="id_passport" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.dob'); ?>
                            </label>
                            <input type="text" name="dob" placeholder="DD/MM/YYYY" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Nationality -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.nationality'); ?>
                            </label>
                            <input type="text" name="nationality" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.email'); ?>
                            </label>
                            <input type="email" name="email" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.phone'); ?>
                            </label>
                            <input type="tel" name="phone" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.address'); ?>
                            </label>
                            <input type="text" name="address" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-6 pt-4 pb-4 border-b-2 border-gray-200">
                            <?php echo __('admission.form.course_info'); ?>
                        </h3>

                        <!-- Select Course -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.select_course'); ?>
                            </label>
                            <select name="course" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition bg-white">
                                <option value=""><?php echo __('admission.form.select_placeholder'); ?></option>
                                
                                <?php if (isset($courses) && !empty($courses)): ?>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= htmlspecialchars($course['name']) ?>">
                                            <?= htmlspecialchars($course['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Fallback static options -->
                                    <option value="formacion-basica-petroleros">Formación Básica para Operaciones de Carga en Petroleros y Quimiqueros</option>
                                    <option value="personal-safety">Personal Safety and Social Responsibilities</option>
                                    <option value="safety-training">Safety Training For Personnel</option>
                                    <option value="crowd-management">Passenger Ship Crowd Management</option>
                                    <option value="leadership">Use of Leadership and Managerial Skills</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-6 pt-4 pb-4 border-b-2 border-gray-200">
                            <?php echo __('admission.form.documents'); ?>
                        </h3>

                        <!-- ID / Cedula Copy -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.id_doc'); ?>
                            </label>
                            <input type="file" name="cedula" accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>

                        <!-- Health Certificate -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php echo __('admission.form.health_doc'); ?>
                            </label>
                            <input type="file" name="certificado_salud" accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-cyan-500 focus:outline-none transition">
                        </div>
                        <!-- Consent Checkbox -->
                        <div class="bg-cyan-50 p-6 rounded-xl border-2 border-cyan-100 mt-8 mb-4">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="consent" name="consent" type="checkbox" required
                                        class="focus:ring-cyan-500 h-6 w-6 text-cyan-600 border-gray-300 rounded cursor-pointer transition">
                                </div>
                                <div class="ml-4 text-sm">
                                    <label for="consent" class="font-bold text-gray-900 cursor-pointer select-none text-base">
                                        <?php echo __('admission.form.consent_label'); ?>
                                    </label>
                                    <p class="text-gray-600 text-sm mt-1">
                                        <?php echo __('admission.form.consent_text'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6">
                            <button type="submit" id="submitBtn" disabled
                                class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-4 rounded-full font-bold uppercase tracking-wider transition transform shadow-lg text-lg opacity-50 cursor-not-allowed">
                                <?php echo __('admission.form.submit'); ?>
                            </button>
                        </div>

                    </div>
                </form>

                <!-- Privacy Policy -->
                <div class="bg-gray-50 rounded-2xl p-6 mt-8 border-l-4 border-cyan-500">
                    <p class="text-xs text-gray-600 leading-relaxed text-justify">
                        <?php echo __('admission.form.privacy_info'); ?>
                    </p>
                </div>

            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('admissionForm');
    const consentCheckbox = document.getElementById('consent');
    const submitBtn = document.getElementById('submitBtn');

    if (consentCheckbox && submitBtn) {
        consentCheckbox.addEventListener('change', function() {
            submitBtn.disabled = !this.checked;
            if (this.checked) {
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.add('hover:scale-105');
            } else {
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.remove('hover:scale-105');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (!consentCheckbox.checked) {
                alert('Debe aceptar los términos para continuar.');
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> <?php echo __('admission.form.sending'); ?>';
            
            // Prepare form data
            const formData = new FormData(this);
            
            // Submit via AJAX
            fetch('/admission/submit', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('✅ ' + data.message);
                    // Reset form
                    this.reset();
                    // Reset submit button state
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.classList.remove('hover:scale-105');
                } else {
                    // Show error message
                    alert('❌ ' + (data.message || 'Error'));
                }
            })
            .catch(error => {
                console.error('Submission error:', error);
                alert('❌ Error: ' + (error.message || 'Error'));
            })
            .finally(() => {
                // Reset button text
                submitBtn.innerHTML = originalText;
                if(consentCheckbox.checked) {
                    submitBtn.disabled = false;
                }
            });
        });
    }
});
</script>
