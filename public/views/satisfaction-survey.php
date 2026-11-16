<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['errors'], $_SESSION['old_input'], $_SESSION['success'], $_SESSION['error']);
?>

<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= __('survey_form.title') ?></h1>
        <p class="text-gray-600 mb-8"><?= __('survey_form.subtitle') ?></p>

        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/satisfaction-survey/submit" method="POST" id="satisfactionForm">
            <?= \Core\Security::getCsrfField() ?>

            <!-- Datos Personales -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4"><?= __('survey_form.personal') ?> <span class="text-red-500">*</span></h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 mb-2"><?= __('survey_form.first_name') ?></label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        <?php if (isset($errors['first_name'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['first_name'] ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2"><?= __('survey_form.last_name') ?></label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        <?php if (isset($errors['last_name'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errors['last_name'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           required>
                    <?php if (isset($errors['email'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['email'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2"><?= __('survey_form.course') ?> <span class="text-red-500">*</span></label>
                    <select name="course_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value=""><?= __('survey_form.select') ?></option>
                        
                        <?php if (isset($courses) && !empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= htmlspecialchars($course['name']) ?>" <?= ($old['course_name'] ?? '') === $course['name'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($course['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback static options if no courses found -->
                            <option value="Personal Safety and Social Responsibilities" <?= ($old['course_name'] ?? '') === 'Personal Safety and Social Responsibilities' ? 'selected' : '' ?>>Personal Safety and Social Responsibilities</option>
                            <option value="Fire Prevention and Fire Fighting" <?= ($old['course_name'] ?? '') === 'Fire Prevention and Fire Fighting' ? 'selected' : '' ?>>Fire Prevention and Fire Fighting</option>
                            <option value="Elementary First Aid" <?= ($old['course_name'] ?? '') === 'Elementary First Aid' ? 'selected' : '' ?>>Elementary First Aid</option>
                            <option value="Survival Craft and Rescue Boats" <?= ($old['course_name'] ?? '') === 'Survival Craft and Rescue Boats' ? 'selected' : '' ?>>Survival Craft and Rescue Boats</option>
                        <?php endif; ?>
                        
                        <option value="Otro" <?= ($old['course_name'] ?? '') === 'Otro' ? 'selected' : '' ?>><?= __('survey_form.other') ?></option>
                    </select>
                    <?php if (isset($errors['course_name'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['course_name'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pregunta 1: Atención del Personal -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <?= __('survey_form.q1') ?> <span class="text-red-500">*</span>
                </h3>
                
                <div class="space-y-2 mb-4">
                    <label class="flex items-center">
                        <input type="radio" name="staff_attention_rating" value="A-Buena" <?= ($old['staff_attention_rating'] ?? '') === 'A-Buena' ? 'checked' : '' ?> class="mr-2" required>
                        <span><?= __('survey_form.good') ?></span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="staff_attention_rating" value="B-Regular" <?= ($old['staff_attention_rating'] ?? '') === 'B-Regular' ? 'checked' : '' ?> class="mr-2">
                        <span><?= __('survey_form.fair') ?></span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="staff_attention_rating" value="C-Mala" <?= ($old['staff_attention_rating'] ?? '') === 'C-Mala' ? 'checked' : '' ?> class="mr-2">
                        <span><?= __('survey_form.poor') ?></span>
                    </label>
                </div>
                <?php if (isset($errors['staff_attention_rating'])): ?>
                    <p class="text-red-500 text-sm mb-2"><?= $errors['staff_attention_rating'] ?></p>
                <?php endif; ?>

                <label class="block text-gray-700 mb-2"><?= __('survey_form.expand') ?></label>
                <textarea name="staff_attention_comments" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($old['staff_attention_comments'] ?? '') ?></textarea>
            </div>

            <!-- Pregunta 2: Calidad del Entrenamiento -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <?= __('survey_form.q2') ?> <span class="text-red-500">*</span>
                </h3>
                
                <div class="space-y-2 mb-4">
                    <label class="flex items-center">
                        <input type="radio" name="training_quality_rating" value="A-Buena" <?= ($old['training_quality_rating'] ?? '') === 'A-Buena' ? 'checked' : '' ?> class="mr-2" required>
                        <span><?= __('survey_form.good') ?></span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="training_quality_rating" value="B-Regular" <?= ($old['training_quality_rating'] ?? '') === 'B-Regular' ? 'checked' : '' ?> class="mr-2">
                        <span><?= __('survey_form.fair') ?></span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="training_quality_rating" value="C-Mala" <?= ($old['training_quality_rating'] ?? '') === 'C-Mala' ? 'checked' : '' ?> class="mr-2">
                        <span><?= __('survey_form.poor') ?></span>
                    </label>
                </div>
                <?php if (isset($errors['training_quality_rating'])): ?>
                    <p class="text-red-500 text-sm mb-2"><?= $errors['training_quality_rating'] ?></p>
                <?php endif; ?>

                <label class="block text-gray-700 mb-2"><?= __('survey_form.expand') ?></label>
                <textarea name="training_quality_comments" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($old['training_quality_comments'] ?? '') ?></textarea>
            </div>

            <!-- Pregunta 3: Desempeño del Instructor -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <?= __('survey_form.q3') ?> <span class="text-red-500">*</span>
                </h3>
                
                <div class="space-y-2 mb-4">
                    <label class="flex items-center">
                        <input type="radio" name="instructor_performance_rating" value="A-Buena" <?= ($old['instructor_performance_rating'] ?? '') === 'A-Buena' ? 'checked' : '' ?> class="mr-2" required>
                        <span><?= __('survey_form.good') ?></span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="instructor_performance_rating" value="B-Regular" <?= ($old['instructor_performance_rating'] ?? '') === 'B-Regular' ? 'checked' : '' ?> class="mr-2">
                        <span><?= __('survey_form.fair') ?></span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="instructor_performance_rating" value="C-Mala" <?= ($old['instructor_performance_rating'] ?? '') === 'C-Mala' ? 'checked' : '' ?> class="mr-2">
                        <span><?= __('survey_form.poor') ?></span>
                    </label>
                </div>
                <?php if (isset($errors['instructor_performance_rating'])): ?>
                    <p class="text-red-500 text-sm mb-2"><?= $errors['instructor_performance_rating'] ?></p>
                <?php endif; ?>

                <label class="block text-gray-700 mb-2"><?= __('survey_form.expand') ?></label>
                <textarea name="instructor_performance_comments" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($old['instructor_performance_comments'] ?? '') ?></textarea>
            </div>

            <!-- Pregunta 4: Infraestructura y Equipos -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <?= __('survey_form.q4') ?> <span class="text-red-500">*</span>
                </h3>
                
                <div class="space-y-2 mb-4">
                    <label class="flex items-center">
                        <input type="radio" name="infrastructure_rating" value="A-Buena" <?= ($old['infrastructure_rating'] ?? '') === 'A-Buena' ? 'checked' : '' ?> class="mr-2" required>
                        <span><?= __('survey_form.good') ?></span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="infrastructure_rating" value="B-Regular" <?= ($old['infrastructure_rating'] ?? '') === 'B-Regular' ? 'checked' : '' ?> class="mr-2">
                        <span><?= __('survey_form.fair') ?></span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="infrastructure_rating" value="C-Mala" <?= ($old['infrastructure_rating'] ?? '') === 'C-Mala' ? 'checked' : '' ?> class="mr-2">
                        <span><?= __('survey_form.poor') ?></span>
                    </label>
                </div>
                <?php if (isset($errors['infrastructure_rating'])): ?>
                    <p class="text-red-500 text-sm mb-2"><?= $errors['infrastructure_rating'] ?></p>
                <?php endif; ?>

                <label class="block text-gray-700 mb-2"><?= __('survey_form.expand') ?></label>
                <textarea name="infrastructure_comments" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= htmlspecialchars($old['infrastructure_comments'] ?? '') ?></textarea>
            </div>

            <!-- Fecha -->
            <div class="mb-8">
                <label class="block text-gray-700 mb-2"><?= __('survey_form.date') ?> <span class="text-red-500">*</span></label>
                <input type="date" name="survey_date" value="<?= htmlspecialchars($old['survey_date'] ?? date('Y-m-d')) ?>" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       required>
                <p class="text-sm text-gray-500 mt-1"><?= __('survey_form.example') ?> <?= htmlspecialchars(\Core\Language::getLocale() === 'es'
                    ? date('j') . ' de ' . __('survey_form.months.' . date('n')) . ' de ' . date('Y')
                    : __('survey_form.months.' . date('n')) . ' ' . date('j') . ', ' . date('Y')) ?></p>
                <?php if (isset($errors['survey_date'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['survey_date'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition duration-200 shadow-lg">
                    <i class="fas fa-paper-plane mr-2"></i><?= __('survey_form.submit') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('satisfactionForm').addEventListener('submit', function(e) {
    const requiredRadios = ['staff_attention_rating', 'training_quality_rating', 'instructor_performance_rating', 'infrastructure_rating'];
    let valid = true;
    
    requiredRadios.forEach(name => {
        const checked = document.querySelector(`input[name="${name}"]:checked`);
        if (!checked) {
            valid = false;
            alert(<?= json_encode(__('survey_form.alert_missing'), JSON_UNESCAPED_UNICODE) ?>);
        }
    });
    
    if (!valid) {
        e.preventDefault();
    }
});
</script>
