<?php
/**
 * Alta y edición de un producto (curso). Datos comunes arriba y, debajo, un
 * bloque por país con lo que cambia (precio, horas, modalidad, estado,
 * descripción propia). Cada país marcado es una fila de `products`.
 */
$sheet      = $sheet      ?? null;
$offers     = $offers     ?? [];
$errors     = $errors     ?? [];
$branches   = $branches   ?? [];
$categories = $categories ?? [];
$modalities = $modalities ?? [];
$esNueva    = empty($sheet['id']);
$productId  = (int) ($productId ?? 0);

$v = static fn($k, $d = '') => htmlspecialchars((string) ($sheet[$k] ?? $d), ENT_QUOTES);
?>

<div class="max-w-4xl mx-auto">

<div class="flex items-center gap-4 mb-5">
    <a href="/manager/products" class="btn-atras" title="Volver a los productos">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>
    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">
            <?= $esNueva ? 'Nuevo producto' : 'Editar producto' ?>
        </h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            El curso se escribe una vez y se marcan los países en que se ofrece; el precio y las horas se ajustan por país.
        </p>
    </div>
</div>

<?php if ($errors): ?>
<div class="card card-pad mb-4" style="border-color:#fecaca;background:#fef2f2">
    <ul class="text-[13px] font-semibold text-red-700 list-disc pl-5">
        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data"
      action="<?= $esNueva ? '/manager/products/store' : '/manager/products/' . $productId . '/update' ?>">
    <?= \Core\Security::getCsrfField() ?>

    <!-- Datos comunes -->
    <div class="card card-pad mb-4">
        <h2 class="text-[15px] font-black text-slate-800 mb-3">Datos comunes</h2>

        <div class="mb-3">
            <label class="filtro-rot" for="name">Nombre del curso *</label>
            <input type="text" id="name" name="name" required class="campo w-full" value="<?= $v('name') ?>">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
            <div>
                <label class="filtro-rot" for="course_code">Código del curso</label>
                <input type="text" id="course_code" name="course_code" class="campo w-full"
                       placeholder="PAM-M-21" value="<?= $v('course_code') ?>">
            </div>
            <div>
                <label class="filtro-rot" for="imo">N.º de curso modelo IMO</label>
                <input type="text" id="imo" name="imo_model_course_no" class="campo w-full"
                       placeholder="1.02" value="<?= $v('imo_model_course_no') ?>">
            </div>
            <div>
                <label class="filtro-rot" for="category_id">Categoría</label>
                <select id="category_id" name="category_id" class="campo w-full">
                    <option value="">— Sin categoría —</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= (int) ($sheet['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="filtro-rot" for="description">Descripción común</label>
            <textarea id="description" name="description" rows="5" class="campo w-full"><?= htmlspecialchars((string) ($sheet['description'] ?? '')) ?></textarea>
            <p class="text-[11.5px] text-slate-400 mt-1">
                La usan todos los países que no tengan una descripción propia.
            </p>
        </div>

        <div>
            <label class="filtro-rot">Imagen</label>
            <div class="flex items-center gap-3 flex-wrap">
                <?php if (!empty($sheet['image'])): ?>
                <img src="<?= $v('image') ?>" alt="" class="miniatura" style="width:56px;height:56px">
                <input type="hidden" name="current_image" value="<?= $v('image') ?>">
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" class="text-[12px]">
                <input type="text" name="image_url" class="campo" style="flex:1;min-width:220px"
                       placeholder="o la URL de una imagen" value="<?= $v('image') ?>">
            </div>
        </div>
    </div>

    <!-- Países -->
    <div class="card card-pad mb-4">
        <h2 class="text-[15px] font-black text-slate-800 mb-1">Países en que se ofrece</h2>
        <p class="text-[12px] text-slate-400 mb-3">
            Marca cada país y ajusta sus datos. Desmarcar un país no borra su oferta
            (puede haber pedidos): la deja inactiva.
        </p>

        <?php foreach ($branches as $b):
            $bid = (int) $b['id'];
            $o   = $offers[$bid] ?? null;
            $on  = $o !== null;
            $n   = "paises[{$bid}]";
            $ov  = static fn($k, $d = '') => htmlspecialchars((string) ($o[$k] ?? $d), ENT_QUOTES);
            $propia = $on && !empty($o['description_custom']);
        ?>
        <div class="pais" data-pais="<?= $bid ?>"
             style="border:1px solid var(--card-border);border-radius:12px;padding:14px 16px;margin-bottom:10px">

            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="<?= $n ?>[ofrece]" value="1" class="pais-check" <?= $on ? 'checked' : '' ?>>
                <span class="font-black text-[14px] text-slate-800"><?= htmlspecialchars($b['name']) ?></span>
                <?php if ($on && !empty($o['id'])): ?>
                <span class="pastilla p-gris">oferta #<?= (int) $o['id'] ?></span>
                <?php endif; ?>
            </label>

            <div class="pais-campos mt-3" <?= $on ? '' : 'style="display:none"' ?>>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div>
                        <label class="filtro-rot">Precio *</label>
                        <input type="number" step="0.01" min="0" name="<?= $n ?>[price]" class="campo w-full"
                               value="<?= $ov('price') ?>">
                    </div>
                    <div>
                        <label class="filtro-rot">Renovación</label>
                        <input type="number" step="0.01" min="0" name="<?= $n ?>[renewal_price]" class="campo w-full"
                               value="<?= $ov('renewal_price', '0') ?>">
                    </div>
                    <div>
                        <label class="filtro-rot">Horas</label>
                        <input type="number" step="0.5" min="0" name="<?= $n ?>[duration_hours]" class="campo w-full"
                               value="<?= $ov('duration_hours', '0') ?>">
                    </div>
                    <div>
                        <label class="filtro-rot">Stock</label>
                        <input type="number" min="0" name="<?= $n ?>[stock]" class="campo w-full"
                               value="<?= $ov('stock', '99') ?>">
                    </div>
                    <div>
                        <label class="filtro-rot">Estado</label>
                        <select name="<?= $n ?>[status]" class="campo w-full">
                            <option value="active"   <?= (!$on || ($o['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Activo</option>
                            <option value="inactive" <?= ($on && ($o['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3" style="max-width:320px">
                    <label class="filtro-rot">Modalidad *</label>
                    <select name="<?= $n ?>[modality_id]" class="campo w-full">
                        <option value="">— Elige —</option>
                        <?php foreach ($modalities as $m):
                            // Una modalidad exclusiva de otra sucursal no se ofrece aquí.
                            if (!empty($m['branch_id']) && (int) $m['branch_id'] !== $bid) { continue; } ?>
                        <option value="<?= (int) $m['id'] ?>" <?= (int) ($o['modality_id'] ?? 0) === (int) $m['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <details class="mt-3" <?= $propia ? 'open' : '' ?>>
                    <summary class="text-[12px] font-bold text-slate-500 cursor-pointer">
                        Descripción propia de este país
                        <?= $propia ? '<span class="pastilla p-ambar" style="margin-left:6px">distinta de la común</span>' : '' ?>
                    </summary>
                    <textarea name="<?= $n ?>[description]" rows="4" class="campo w-full mt-2"
                              placeholder="Vacío = usa la descripción común"><?= $propia ? htmlspecialchars((string) $o['description']) : '' ?></textarea>
                </details>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="flex items-center justify-between">
        <a href="/manager/products" class="btn-sec">Cancelar</a>
        <button type="submit" class="btn-primario">
            <i data-lucide="save" class="ic ic-16"></i> <?= $esNueva ? 'Crear producto' : 'Guardar cambios' ?>
        </button>
    </div>
</form>
</div>

<script>
// Muestra los campos de un país sólo mientras está marcado.
document.querySelectorAll('.pais-check').forEach(function (chk) {
    chk.addEventListener('change', function () {
        chk.closest('.pais').querySelector('.pais-campos').style.display = chk.checked ? '' : 'none';
    });
});
</script>
