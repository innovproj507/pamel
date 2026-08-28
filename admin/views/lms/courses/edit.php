<div class="mb-8">
    <div class="flex items-center gap-4">
        <a href="/manager/lms/courses" class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-blue-600 transition shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black text-slate-800">Editar Curso</h1>
            <p class="text-sm text-slate-500 font-medium">Modifica los detalles generales de <?= htmlspecialchars($course['title']) ?></p>
        </div>
    </div>
</div>

<form action="/manager/lms/courses/<?= $course['id'] ?>/update" method="POST" enctype="multipart/form-data" class="max-w-4xl mx-auto">
    <?php echo \Core\Security::getCsrfField(); ?>
    <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-50 overflow-hidden">
        <div class="p-10 space-y-8">
            <!-- Título -->
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Título del Curso <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-base font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all" required>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Descripción</label>
                <textarea name="description" rows="8" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-base font-medium text-slate-600 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
            </div>

            <!-- Código del Curso -->
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Código del Curso</label>
                <?php if (!empty($course['product_id'])): ?>
                    <div class="flex items-center gap-3 px-6 py-4 rounded-2xl border border-blue-100 bg-blue-50/40">
                        <span class="font-black text-blue-700 text-base tracking-widest">
                            <?= htmlspecialchars($course['course_code'] ?? '—') ?>
                        </span>
                        <span class="text-xs text-slate-400 font-medium">
                            (viene del producto vinculado —
                            <a href="/manager/products/<?= $course['product_id'] ?>/edit" class="text-blue-500 hover:underline">editar producto</a>)
                        </span>
                    </div>
                    <input type="hidden" name="course_code" value="">
                <?php else: ?>
                    <input type="text" name="course_code"
                           value="<?= htmlspecialchars($course['course_code'] ?? '') ?>"
                           placeholder="Ej: FPFF, PST, EFA…"
                           class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-base font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all uppercase">
                <?php endif; ?>
            </div>

            <!-- Instructor -->
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Instructor</label>
                <?php if (!empty($canAssignTeacher)): ?>
                    <select name="teacher_id" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-base font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                        <option value="">— Sin asignar —</option>
                        <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (int)($course['teacher_id'] ?? 0) === (int)$t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <?php
                        $currentTeacher = '— Sin asignar —';
                        foreach ($teachers as $t) {
                            if ((int) $t['id'] === (int) ($course['teacher_id'] ?? 0)) {
                                $currentTeacher = $t['name'];
                            }
                        }
                    ?>
                    <div class="px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-base font-bold text-slate-500">
                        <?= htmlspecialchars($currentTeacher) ?>
                        <span class="text-xs font-medium text-slate-400 ml-2">(solo un administrador puede reasignar el instructor)</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Categoría -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Categoría</label>
                    <select name="category_id" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-base font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (int)$course['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Nivel -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Nivel</label>
                    <select name="level" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-base font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                        <option value="beginner" <?= ($course['level'] ?? '') === 'beginner' ? 'selected' : '' ?>>Principiante</option>
                        <option value="intermediate" <?= ($course['level'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermedio</option>
                        <option value="advanced" <?= ($course['level'] ?? '') === 'advanced' ? 'selected' : '' ?>>Avanzado</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Precio -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Precio (USD)</label>
                    <div class="relative">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                        <input type="number" name="price" step="0.01" value="<?= $course['price'] ?? '0.00' ?>" class="w-full pl-10 pr-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-base font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                    </div>
                </div>
                <!-- Estado -->
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3 px-1">Estado</label>
                    <select name="status" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-slate-50 text-base font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                        <option value="draft" <?= $course['status'] === 'draft' ? 'selected' : '' ?>>Borrador</option>
                        <option value="published" <?= $course['status'] === 'published' ? 'selected' : '' ?>>Publicado</option>
                        <option value="archived" <?= $course['status'] === 'archived' ? 'selected' : '' ?>>Archivado</option>
                    </select>
                </div>
            </div>

            <!-- Producto vinculado -->
            <div class="p-6 rounded-2xl border-2 border-blue-100 bg-blue-50/40">
                <label class="block text-xs font-black text-blue-600 uppercase tracking-widest mb-2 px-1">
                    Vincular con Producto del Ecommerce
                </label>
                <p class="text-xs text-slate-500 mb-3 px-1">El precio y la imagen se tomarán del producto seleccionado. Si el producto se desactiva, el curso dejará de mostrarse en elearning.</p>
                <select name="product_id" class="w-full px-6 py-4 rounded-2xl border border-slate-100 bg-white text-base font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all">
                    <option value="">— Sin producto vinculado (usar precio del curso) —</option>
                    <?php foreach ($products as $prod): ?>
                    <option value="<?= $prod['id'] ?>"
                        <?= (int)($course['product_id'] ?? 0) === (int)$prod['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars("[{$prod['course_code']}] {$prod['name']}") ?>
                        <?php if ($prod['status'] === 'inactive'): ?> ⚠ inactivo<?php endif; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($course['product_id']) && !empty($course['product_name'])): ?>
                <div class="mt-3 flex items-center gap-2 text-xs font-semibold text-blue-700">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    Actualmente vinculado: <?= htmlspecialchars($course['product_name']) ?>
                    <?= isset($course['product_status']) && $course['product_status'] === 'active'
                        ? '<span class="text-emerald-600">(activo)</span>'
                        : '<span class="text-rose-600">(inactivo)</span>' ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Otros Toggles -->
            <div class="space-y-4">
                <label class="flex items-center gap-4 p-5 rounded-2xl bg-emerald-50 border border-emerald-100 cursor-pointer group transition-all hover:bg-emerald-100/50">
                    <input type="checkbox" name="is_free" class="w-5 h-5 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500/20" <?= !empty($course['is_free']) ? 'checked' : '' ?>>
                    <span class="text-sm font-bold text-emerald-700">Marcar como curso gratuito (ignora el precio al inscribirse)</span>
                </label>

                <label class="flex items-center gap-4 p-5 rounded-2xl bg-violet-50 border border-violet-100 cursor-pointer group transition-all hover:bg-violet-100/50">
                    <input type="checkbox" name="has_survey" class="w-5 h-5 rounded border-violet-300 text-violet-600 focus:ring-violet-500/20" <?= !empty($course['satisfaction_enabled']) ? 'checked' : '' ?>>
                    <span class="text-sm font-bold text-violet-700">Habilitar formulario de satisfacción al finalizar el curso</span>
                </label>
            </div>

            <!-- Imagen de Portada -->
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-4 px-1">Imagen de Portada</label>

                <?php
                $productImg = $course['product_image'] ?? '';
                $displayImg = $productImg ?: ($course['image'] ?? '');
                ?>

                <?php if (!empty($course['product_id'])): ?>
                <!-- Imagen viene del producto — solo lectura -->
                <div class="rounded-2xl border border-blue-100 bg-blue-50/30 p-5 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-blue-600">
                        <i class="fas fa-link text-[10px]"></i>
                        Imagen del producto vinculado — para cambiarla edita el producto
                    </div>
                    <?php if ($displayImg): ?>
                    <div class="w-full h-48 rounded-xl overflow-hidden border border-blue-100">
                        <img src="<?= htmlspecialchars($displayImg) ?>" class="w-full h-full object-cover"
                             onerror="this.closest('div').style.display='none'">
                    </div>
                    <?php else: ?>
                    <p class="text-xs text-slate-400">El producto no tiene imagen aún. Súbela desde
                        <a href="/manager/products/<?= $course['product_id'] ?>/edit" class="text-blue-500 underline">editar producto</a>.
                    </p>
                    <?php endif; ?>
                    <input type="hidden" name="image" value="<?= htmlspecialchars($course['image'] ?? '') ?>">
                </div>

                <?php else: ?>
                <!-- Sin producto — permite subir imagen propia al curso -->
                <div class="space-y-4">
                    <div id="img-preview-wrap" class="<?= $displayImg ? '' : 'hidden' ?> relative w-full h-48 rounded-2xl overflow-hidden border border-slate-100 shadow-sm">
                        <img id="img-preview" src="<?= htmlspecialchars($displayImg) ?>" class="w-full h-full object-cover"
                             onerror="this.closest('#img-preview-wrap').style.display='none'">
                    </div>
                    <input type="hidden" name="image" id="image-path" value="<?= htmlspecialchars($course['image'] ?? '') ?>">
                    <label class="flex flex-col items-center gap-3 border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer bg-slate-50/50">
                        <i class="fas fa-cloud-upload-alt text-3xl text-slate-300"></i>
                        <div>
                            <p class="text-sm font-bold text-slate-500">Haz clic para subir una imagen</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">JPG, PNG o WebP — máx. 5 MB</p>
                        </div>
                        <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewCourseImage(this)">
                    </label>
                    <p class="text-[10px] text-slate-400 px-1">
                        O vincula este curso a un producto (arriba) para usar la imagen del producto automáticamente.
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="p-10 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between gap-4">
            <button type="button" onclick="history.back()" class="px-8 py-4 rounded-2xl bg-white border border-slate-100 text-slate-500 font-black uppercase tracking-widest text-xs hover:bg-slate-100 transition-all">
                Cancelar
            </button>
            <button type="submit" class="px-12 py-4 rounded-2xl bg-blue-600 text-white font-black uppercase tracking-widest text-xs shadow-xl shadow-blue-500/30 hover:bg-blue-700 hover:-translate-y-0.5 transition-all">
                Guardar Cambios
            </button>
        </div>
    </div>
</form>
<script>
function previewCourseImage(input) {
    if (!input.files || !input.files[0]) return;
    const wrap = document.getElementById('img-preview-wrap');
    const img  = document.getElementById('img-preview');
    const reader = new FileReader();
    reader.onload = e => {
        img.src = e.target.result;
        wrap.classList.remove('hidden');
        wrap.style.display = '';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
