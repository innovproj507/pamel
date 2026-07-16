<?php
$statusLabel = ['active' => 'Activo', 'completed' => 'Completado', 'cancelled' => 'Cancelado', 'expired' => 'Expirado'];
$statusColor = ['active' => 'bg-blue-100 text-blue-700', 'completed' => 'bg-emerald-100 text-emerald-700', 'cancelled' => 'bg-red-100 text-red-700', 'expired' => 'bg-gray-100 text-gray-500'];
?>

<!-- Header -->
<div class="mb-6 flex items-center gap-4">
    <a href="/manager/lms/courses" class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm">
        <i class="fas fa-arrow-left text-sm"></i>
    </a>
    <div class="min-w-0">
        <h1 class="text-2xl font-bold text-gray-900 truncate"><?= htmlspecialchars($course['title']) ?></h1>
        <p class="text-sm text-gray-500 mt-0.5">
            <span class="font-mono bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs"><?= htmlspecialchars($course['course_code'] ?? '') ?></span>
            — <?= count($enrollments) ?> estudiante<?= count($enrollments) !== 1 ? 's' : '' ?> inscritos
        </p>
    </div>
    <div class="ml-auto flex gap-2">
        <a href="/manager/lms/students/create?course_id=<?= $course['id'] ?>"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2 rounded-lg flex items-center gap-2 transition shadow">
            <i class="fas fa-user-plus"></i> Inscribir Estudiante
        </a>
    </div>
</div>

<?php if (empty($enrollments)): ?>
    <div class="text-center py-20 text-gray-400">
        <i class="fas fa-user-graduate text-5xl mb-4"></i>
        <p class="text-base font-medium">Ningún estudiante inscrito en este curso.</p>
        <a href="/manager/lms/students/create?course_id=<?= $course['id'] ?>"
           class="mt-4 inline-block text-emerald-600 hover:underline text-sm font-semibold">
            <i class="fas fa-plus mr-1"></i>Inscribir el primero
        </a>
    </div>
<?php else: ?>
    <table class="w-full table-fixed border-collapse text-sm">
        <thead>
            <tr class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-xs uppercase tracking-wider">
                <th class="px-4 py-3 text-left">Estudiante</th>
                <th class="w-44 px-4 py-3 text-left">Email</th>
                <th class="w-28 px-4 py-3 text-left">Inscrito</th>
                <th class="w-36 px-4 py-3 text-center">Progreso</th>
                <th class="w-24 px-4 py-3 text-center">Estado</th>
                <th class="w-28 px-4 py-3 text-center">Certificado</th>
                <th class="w-24 px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($enrollments as $e): ?>
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                <td class="px-4 py-3 font-semibold text-gray-900 truncate">
                    <?= htmlspecialchars($e['student_name']) ?>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs truncate">
                    <?= htmlspecialchars($e['student_email']) ?>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    <?= date('d/m/Y', strtotime($e['enrolled_at'])) ?>
                </td>
                <td class="px-4 py-3">
                    <?php $pct = (float)$e['progress_percentage']; ?>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full <?= $pct >= 100 ? 'bg-emerald-500' : 'bg-blue-500' ?>"
                                 style="width:<?= min(100, $pct) ?>%"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600 w-8 text-right"><?= (int)$pct ?>%</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    <?php $st = $e['status']; ?>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $statusColor[$st] ?? 'bg-gray-100 text-gray-500' ?>">
                        <?= $statusLabel[$st] ?? $st ?>
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <?php if ($e['certificate_issued']): ?>
                        <span class="text-xs font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded" title="<?= htmlspecialchars($e['certificate_code']) ?>">
                            <i class="fas fa-certificate mr-1"></i><?= htmlspecialchars($e['certificate_code']) ?>
                        </span>
                    <?php else: ?>
                        <span class="text-gray-300 text-xs">—</span>
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3 text-center">
                    <form action="/manager/lms/courses/<?= $course['id'] ?>/students/unenroll" method="POST"
                          onsubmit="return confirm('¿Desinscribir a <?= htmlspecialchars(addslashes($e['student_name'])) ?> de este curso?')">
                        <?php echo \Core\Security::getCsrfField(); ?>
                        <input type="hidden" name="enrollment_id" value="<?= $e['id'] ?>">
                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-semibold flex items-center gap-1 mx-auto">
                            <i class="fas fa-user-minus"></i> Quitar
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
