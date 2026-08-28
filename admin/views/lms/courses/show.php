<?php
$teacherName = 'N/A';
foreach($teachers as $t) {
    if($t['id'] == $course['teacher_id']) {
        $teacherName = $t['name'];
        break;
    }
}

$categoryName = 'General';
foreach($categories as $c) {
    if($c['id'] == $course['category_id']) {
        $categoryName = $c['name'];
        break;
    }
}
?>

<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
        <a href="/manager/lms/courses" class="hover:text-blue-600 font-semibold transition-colors">Cursos LMS</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-slate-800 font-semibold truncate"><?= htmlspecialchars($course['title']) ?></span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Column -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Course Header (Style similar to elearning version) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="h-60 relative overflow-hidden" style="background:linear-gradient(135deg,#0a1628 0%,#1a3a6b 60%,#1a56db 100%);">
                    <?php if ($course['image']): ?>
                    <img src="<?= htmlspecialchars($course['image']) ?>" class="w-full h-full object-cover opacity-40">
                    <?php endif; ?>
                    <div class="absolute inset-0 flex flex-col justify-end p-8">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black bg-white/20 text-white border border-white/30 px-3 py-1 rounded-full w-fit mb-4 backdrop-blur-sm uppercase tracking-widest">
                            <?= htmlspecialchars($categoryName) ?>
                        </span>
                        <h1 class="text-3xl font-black text-white leading-tight">
                            <?= htmlspecialchars($course['title']) ?>
                        </h1>
                    </div>
                </div>

                <div class="px-8 py-5 flex flex-wrap items-center gap-6 border-b border-gray-100 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm font-black">
                            <?= mb_strtoupper(mb_substr($teacherName, 0, 1)) ?>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Instructor</span>
                            <span class="text-sm font-bold text-slate-800"><?= htmlspecialchars($teacherName) ?></span>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Nivel</span>
                        <span class="text-sm font-bold text-slate-800"><?= ucfirst($course['level']) ?></span>
                    </div>
                    <div class="flex flex-col ml-auto text-right">
                        <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Estadísticas</span>
                        <span class="text-sm font-bold text-slate-800"><?= $lessonCount ?> lecciones · <?= $quizCount ?> evaluaciones</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-black text-slate-800 text-lg flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">📄</span>
                        Acerca del Curso
                    </h2>
                </div>
                <div class="text-slate-500 text-sm leading-relaxed whitespace-pre-line">
                    <?= htmlspecialchars($course['description'] ?? 'No hay descripción disponible.') ?>
                </div>
            </div>

            <!-- Content List -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-black text-slate-800 text-lg flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">📚</span>
                        Contenido del Curso
                    </h2>
<?php if (can('lms.lessons.manage')): ?>
                    <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/create" class="text-xs font-black text-blue-600 hover:text-blue-700 uppercase tracking-widest">
                        + Añadir Lección
                    </a>
<?php endif; ?>
                </div>

                <div class="space-y-2">
                    <?php foreach ($lessons as $i => $lesson): ?>
                    <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-50 hover:border-blue-100 hover:bg-blue-50/30 transition-all group">
                        <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-400 text-xs font-black flex items-center justify-center flex-shrink-0 group-hover:bg-white group-hover:text-blue-600 transition-all">
                            <?= $i + 1 ?>
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-700 truncate"><?= htmlspecialchars($lesson['title']) ?></p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase mt-1"><?= $lesson['type'] ?> · <?= $lesson['duration'] ?> min</p>
                        </div>
                        <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/<?= $lesson['id'] ?>/edit" class="p-2 text-slate-300 hover:text-blue-600 transition-colors opacity-0 group-hover:opacity-100">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($lessons)): ?>
                    <div class="py-12 text-center text-slate-300 italic text-sm">Este curso aún no tiene lecciones.</div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($quizzes)): ?>
                <div class="mt-8 pt-8 border-t border-gray-50">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Evaluaciones</h3>
                    <div class="space-y-2">
                        <?php foreach ($quizzes as $quiz): ?>
                        <div class="flex items-center gap-4 p-4 rounded-xl border border-gray-50 hover:border-amber-100 hover:bg-amber-50/30 transition-all group">
                            <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-500 text-xs font-black flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-vial"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-700 truncate"><?= htmlspecialchars($quiz['title']) ?></p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Evaluación</p>
                            </div>
<?php if (can('lms.quizzes.manage')): ?>
                            <a href="/manager/lms/quizzes/<?= $quiz['id'] ?>/edit" class="p-2 text-slate-300 hover:text-amber-600 transition-colors opacity-0 group-hover:opacity-100">
                                <i class="fas fa-edit"></i>
                            </a>
<?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Edit Form Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-lg p-8">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6">Información General</h3>
                
                <form action="/manager/lms/courses/<?= $course['id'] ?>/update" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <?php echo \Core\Security::getCsrfField(); ?>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Título</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-slate-50 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Slug</label>
                        <input type="text" name="slug" value="<?= htmlspecialchars($course['slug']) ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-slate-50 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Instructor</label>
                        <select name="teacher_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-slate-50 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                            <?php foreach ($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $t['id'] == $course['teacher_id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nivel</label>
                        <select name="level" class="w-full px-4 py-2.5 rounded-xl border border-gray-100 bg-slate-50 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                            <option value="beginner" <?= $course['level'] == 'beginner' ? 'selected' : '' ?>>Principiante</option>
                            <option value="intermediate" <?= $course['level'] == 'intermediate' ? 'selected' : '' ?>>Intermedio</option>
                            <option value="advanced" <?= $course['level'] == 'advanced' ? 'selected' : '' ?>>Avanzado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Imagen</label>
                        <input type="hidden" name="image" value="<?= htmlspecialchars($course['image'] ?? '') ?>">
                        <?php if (!empty($course['image'])): ?>
                        <img src="<?= htmlspecialchars($course['image']) ?>" alt="Portada" class="h-20 w-auto rounded-xl object-cover mb-2 border border-gray-100">
                        <?php endif; ?>
                        <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp"
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3.5 rounded-xl bg-blue-600 text-white font-black uppercase tracking-widest text-[10px] shadow-lg shadow-blue-500/25 hover:bg-blue-700 transition-all hover:-translate-y-0.5">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            <!-- Actions Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6">Gestión</h3>
                <div class="space-y-3">
<?php if (can('lms.lessons.manage')): ?>
                    <a href="/manager/lms/courses/<?= $course['id'] ?>/lessons/create" class="flex items-center gap-3 p-4 rounded-xl bg-blue-50 text-blue-600 font-bold hover:bg-blue-100 transition-all">
                        <i class="fas fa-plus text-blue-400"></i>
                        <span class="text-xs">Nueva Lección</span>
                    </a>
<?php endif; ?>
<?php if (can('lms.quizzes.manage')): ?>
                    <a href="/manager/lms/quizzes/create?course_id=<?= $course['id'] ?>" class="flex items-center gap-3 p-4 rounded-xl bg-amber-50 text-amber-600 font-bold hover:bg-amber-100 transition-all">
                        <i class="fas fa-vial text-amber-400"></i>
                        <span class="text-xs">Nueva Evaluación</span>
                    </a>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
