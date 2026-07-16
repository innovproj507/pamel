<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

function getRatingBadge($rating) {
    $colors = [
        'A-Buena' => 'bg-green-100 text-green-800',
        'B-Regular' => 'bg-yellow-100 text-yellow-800',
        'C-Mala' => 'bg-red-100 text-red-800'
    ];
    $color = $colors[$rating] ?? 'bg-gray-100 text-gray-800';
    return "<span class='px-2 py-1 rounded text-xs font-semibold $color'>$rating</span>";
}
?>

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-poll text-blue-600 mr-2"></i>Encuestas de Satisfacción
        </h1>
    </div>

    <?php if ($success): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="/manager/satisfaction-surveys" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Curso</label>
                <select name="course" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Todos los cursos</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= htmlspecialchars($course['course_name']) ?>" 
                                <?= $filters['course'] === $course['course_name'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Calificación</label>
                <select name="rating" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">Todas</option>
                    <option value="A-Buena" <?= $filters['rating'] === 'A-Buena' ? 'selected' : '' ?>>A - Buena</option>
                    <option value="B-Regular" <?= $filters['rating'] === 'B-Regular' ? 'selected' : '' ?>>B - Regular</option>
                    <option value="C-Mala" <?= $filters['rating'] === 'C-Mala' ? 'selected' : '' ?>>C - Mala</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($filters['date_from']) ?>" 
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($filters['date_to']) ?>" 
                       class="w-full border border-gray-300 rounded px-3 py-2">
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
                <a href="/manager/satisfaction-surveys" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                    <i class="fas fa-times mr-2"></i>Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Results Summary -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <p class="text-gray-600">
            Mostrando <strong><?= count($surveys) ?></strong> de <strong><?= $total ?></strong> encuestas
        </p>
    </div>

    <!-- Surveys Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Curso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Personal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Calidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Infraestructura</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($surveys)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron encuestas
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($surveys as $survey): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?= date('d/m/Y', strtotime($survey['survey_date'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($survey['first_name'] . ' ' . $survey['last_name']) ?>
                                </div>
                                <div class="text-sm text-gray-500"><?= htmlspecialchars($survey['email']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?= htmlspecialchars($survey['course_name']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= getRatingBadge($survey['staff_attention_rating']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= getRatingBadge($survey['training_quality_rating']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= getRatingBadge($survey['instructor_performance_rating']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?= getRatingBadge($survey['infrastructure_rating']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="/manager/satisfaction-surveys/<?= $survey['id'] ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3"
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="/manager/satisfaction-surveys/<?= $survey['id'] ?>/download-word" 
                                   class="text-green-600 hover:text-green-900 mr-3"
                                   title="Descargar Word">
                                    <i class="fas fa-file-word"></i>
                                </a>
                                <button onclick="deleteSurvey(<?= $survey['id'] ?>)" 
                                        class="text-red-600 hover:text-red-900"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="mt-6 flex justify-center">
            <nav class="flex gap-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?><?= $filters['course'] ? '&course=' . urlencode($filters['course']) : '' ?><?= $filters['rating'] ? '&rating=' . urlencode($filters['rating']) : '' ?><?= $filters['date_from'] ? '&date_from=' . urlencode($filters['date_from']) : '' ?><?= $filters['date_to'] ? '&date_to=' . urlencode($filters['date_to']) : '' ?>"
                       class="px-4 py-2 rounded <?= $i === $page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    <?= \Core\Security::getCsrfField() ?>
</form>

<script>
function deleteSurvey(id) {
    if (confirm('¿Está seguro de que desea eliminar esta encuesta?')) {
        const form = document.getElementById('deleteForm');
        form.action = '/manager/satisfaction-surveys/' + id + '/delete';
        form.submit();
    }
}
</script>
