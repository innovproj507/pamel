<?php
function getRatingBadge($rating) {
    $colors = [
        'A-Buena' => 'bg-green-100 text-green-800 border-green-200',
        'B-Regular' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'C-Mala' => 'bg-red-100 text-red-800 border-red-200'
    ];
    $icons = [
        'A-Buena' => 'fa-smile',
        'B-Regular' => 'fa-meh',
        'C-Mala' => 'fa-frown'
    ];
    $color = $colors[$rating] ?? 'bg-gray-100 text-gray-800 border-gray-200';
    $icon = $icons[$rating] ?? 'fa-circle';
    return "<span class='inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border $color'><i class='fas $icon mr-2'></i>$rating</span>";
}
?>

<div class="p-6">
    <div class="mb-6">
        <a href="/manager/satisfaction-surveys" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Volver a la lista
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">
                <i class="fas fa-file-alt mr-2"></i>Detalle de Encuesta
            </h1>
            <p class="text-blue-100 mt-1">
                Enviada el <?= date('d/m/Y', strtotime($survey['created_at'])) ?> a las <?= date('H:i', strtotime($survey['created_at'])) ?>
            </p>
        </div>

        <div class="p-6">
            <!-- Datos Personales -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user text-blue-600 mr-2"></i>Datos Personales
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Nombre Completo</label>
                        <p class="text-lg text-gray-900"><?= htmlspecialchars($survey['first_name'] . ' ' . $survey['last_name']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email</label>
                        <p class="text-lg text-gray-900">
                            <a href="mailto:<?= htmlspecialchars($survey['email']) ?>" class="text-blue-600 hover:underline">
                                <?= htmlspecialchars($survey['email']) ?>
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Curso</label>
                        <p class="text-lg text-gray-900"><?= htmlspecialchars($survey['course_name']) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Fecha del Curso</label>
                        <p class="text-lg text-gray-900"><?= date('d/m/Y', strtotime($survey['survey_date'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Calificaciones -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-2"></i>Calificaciones
                </h2>

                <!-- Atención del Personal -->
                <div class="mb-6 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Atención del Personal</h3>
                            <p class="text-sm text-gray-600">Proceso administrativo</p>
                        </div>
                        <?= getRatingBadge($survey['staff_attention_rating']) ?>
                    </div>
                    <?php if (!empty($survey['staff_attention_comments'])): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mt-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Comentarios:</p>
                            <p class="text-gray-800"><?= nl2br(htmlspecialchars($survey['staff_attention_comments'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Calidad del Entrenamiento -->
                <div class="mb-6 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Calidad del Entrenamiento</h3>
                            <p class="text-sm text-gray-600">Curso impartido</p>
                        </div>
                        <?= getRatingBadge($survey['training_quality_rating']) ?>
                    </div>
                    <?php if (!empty($survey['training_quality_comments'])): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mt-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Comentarios:</p>
                            <p class="text-gray-800"><?= nl2br(htmlspecialchars($survey['training_quality_comments'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Desempeño del Instructor -->
                <div class="mb-6 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Desempeño del Instructor</h3>
                            <p class="text-sm text-gray-600">Desarrollo del instructor</p>
                        </div>
                        <?= getRatingBadge($survey['instructor_performance_rating']) ?>
                    </div>
                    <?php if (!empty($survey['instructor_performance_comments'])): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mt-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Comentarios:</p>
                            <p class="text-gray-800"><?= nl2br(htmlspecialchars($survey['instructor_performance_comments'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Infraestructura y Equipos -->
                <div class="mb-6 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-1">Infraestructura, Equipos y Simulador</h3>
                            <p class="text-sm text-gray-600">Condiciones generales</p>
                        </div>
                        <?= getRatingBadge($survey['infrastructure_rating']) ?>
                    </div>
                    <?php if (!empty($survey['infrastructure_comments'])): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3 mt-3">
                            <p class="text-sm font-medium text-gray-700 mb-1">Comentarios:</p>
                            <p class="text-gray-800"><?= nl2br(htmlspecialchars($survey['infrastructure_comments'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t">
                <a href="/manager/satisfaction-surveys" class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="/manager/satisfaction-surveys/<?= $survey['id'] ?>/download-word" 
                   class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                    <i class="fas fa-file-word mr-2"></i>Descargar Word
                </a>
                <button onclick="deleteSurvey(<?= $survey['id'] ?>)" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Eliminar
                </button>
                <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    <?= \Core\Security::getCsrfField() ?>
</form>

<script>
function deleteSurvey(id) {
    if (confirm('¿Está seguro de que desea eliminar esta encuesta? Esta acción no se puede deshacer.')) {
        const form = document.getElementById('deleteForm');
        form.action = '/manager/satisfaction-surveys/' + id + '/delete';
        form.submit();
    }
}
</script>

<style>
@media print {
    .bg-gradient-to-r { background: #2563eb !important; }
    button, a[href="/manager/satisfaction-surveys"] { display: none !important; }
}
</style>
