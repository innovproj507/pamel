<?php
/**
 * Catálogo de reglas de requisitos: cada texto se escribe una vez y se marca
 * en los cursos que lo exigen.
 *
 * Al corregir el texto de una regla, los cursos que ya la copiaron NO cambian
 * solos: aparece el aviso de versión antigua con el botón para actualizarlos.
 */
$reglas          = $reglas          ?? [];
$desactualizadas = $desactualizadas ?? [];

$porId = [];
foreach ($desactualizadas as $d) {
    $porId[(int) $d['id']] = (int) $d['cursos'];
}

$alcances = ['any' => 'Las dos', 'new' => 'Primera vez', 'refresher' => 'Renovación'];
?>

<div class="mb-6 flex flex-col lg:flex-row lg:items-start justify-between gap-4">
    <div class="flex items-start gap-4">
        <a href="/manager/requisitos/cursos" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm flex-shrink-0">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Catálogo de requisitos</h1>
            <p class="text-sm text-gray-500 max-w-3xl">
                Cada texto se escribe una vez aquí y se marca en los cursos que lo exigen.
                Los de <strong>uso general</strong> vienen premarcados al definir un curso nuevo.
            </p>
        </div>
    </div>
    <button type="button" onclick="abrirRegla()"
            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition whitespace-nowrap flex-shrink-0">
        <i class="fas fa-plus mr-1"></i> Nueva regla
    </button>
</div>

<?php if ($desactualizadas): ?>
<div class="card p-4 mb-4 border-l-4 border-l-amber-400">
    <p class="text-sm font-bold text-slate-800 mb-2">
        <i class="fas fa-triangle-exclamation text-amber-500"></i>
        Hay reglas cuyo texto cambió después de publicarse
    </p>
    <p class="text-xs text-gray-500 mb-3">
        Los cursos siguen mostrando la versión que copiaron. Actualízalos cuando lo revises.
    </p>
    <div class="space-y-1.5">
        <?php foreach ($desactualizadas as $d): ?>
        <form method="POST" action="/manager/requisitos/reglas/<?= (int) $d['id'] ?>/propagar"
              class="flex items-center gap-3 text-xs">
            <?= \Core\Security::getCsrfField() ?>
            <span class="flex-1 min-w-0 truncate text-slate-600"><?= htmlspecialchars($d['label']) ?></span>
            <span class="font-black text-amber-600 whitespace-nowrap"><?= (int) $d['cursos'] ?> curso<?= (int) $d['cursos'] === 1 ? '' : 's' ?></span>
            <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold transition whitespace-nowrap">
                Actualizar
            </button>
        </form>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="card p-3 mb-4 flex flex-col sm:flex-row sm:items-center gap-3">
    <div class="relative flex-1">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
        <input type="search" id="buscador" autofocus autocomplete="off"
               placeholder="Buscar en el catálogo…"
               class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
    </div>
    <p class="text-xs font-bold text-gray-400 whitespace-nowrap px-1">
        <span id="contadorVisibles"><?= count($reglas) ?></span> de <?= count($reglas) ?> reglas
    </p>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[10px] font-black text-gray-400 uppercase tracking-widest border-b" style="border-color: var(--card-border)">
                <th class="px-5 py-3">Requisito</th>
                <th class="px-3 py-3 w-28">Modalidad</th>
                <th class="px-3 py-3 text-center w-24">Cursos</th>
                <th class="px-5 py-3 w-24"></th>
            </tr>
        </thead>
        <tbody id="listaReglas">
            <?php foreach ($reglas as $r): ?>
            <tr class="regla border-b last:border-0 hover:bg-slate-50/70 transition"
                style="border-color: var(--card-border)"
                data-buscar="<?= htmlspecialchars(mb_strtolower($r['label'])) ?>">
                <td class="px-5 py-3">
                    <p class="text-xs text-slate-700 leading-snug"><?= htmlspecialchars($r['label']) ?></p>
                    <p class="text-[10px] text-gray-400 mt-1">
                        <?php if (!empty($r['requires_file'])): ?>
                        <i class="fas fa-paperclip"></i> pide documento
                        <?php else: ?>
                        <i class="fas fa-circle-info"></i> solo informativo
                        <?php endif; ?>
                        <?php if (!empty($r['is_default'])): ?>
                        &nbsp;·&nbsp; <span class="text-emerald-600 font-bold">uso general</span>
                        <?php endif; ?>
                        <?php if (!empty($porId[(int) $r['id']])): ?>
                        &nbsp;·&nbsp; <span class="text-amber-600 font-bold"><?= $porId[(int) $r['id']] ?> con versión antigua</span>
                        <?php endif; ?>
                    </p>
                </td>
                <td class="px-3 py-3">
                    <span class="text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded-full <?=
                        $r['scope'] === 'refresher' ? 'bg-violet-50 text-violet-600 border border-violet-200'
                        : ($r['scope'] === 'new' ? 'bg-blue-50 text-blue-600 border border-blue-200'
                        : 'bg-gray-50 text-gray-500 border border-gray-200') ?>">
                        <?= $alcances[$r['scope']] ?? $r['scope'] ?>
                    </span>
                </td>
                <td class="px-3 py-3 text-center">
                    <span class="text-xs font-black text-slate-600"><?= (int) $r['cursos'] ?></span>
                </td>
                <td class="px-5 py-3 text-right whitespace-nowrap">
                    <button type="button"
                            onclick='abrirRegla(<?= json_encode([
                                "id"            => (int) $r["id"],
                                "label"         => $r["label"],
                                "requires_file" => (int) $r["requires_file"],
                                "scope"         => $r["scope"],
                                "is_default"    => (int) $r["is_default"],
                            ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?>)'
                            class="text-xs font-black text-blue-600 hover:underline">Editar</button>
                    <form method="POST" action="/manager/requisitos/reglas/<?= (int) $r['id'] ?>/borrar" class="inline"
                          onsubmit="return confirm('¿Retirar esta regla del catálogo?\n\nLos requisitos ya publicados en los cursos se conservan.');">
                        <?= \Core\Security::getCsrfField() ?>
                        <button type="submit" class="ml-2 text-xs font-black text-gray-300 hover:text-rose-500 transition">Retirar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<p id="sinResultados" class="hidden card p-10 text-center text-sm font-bold text-gray-400 mt-4">
    Ninguna regla coincide con la búsqueda.
</p>

<!-- Alta y edición -->
<div id="dlgRegla" class="hidden fixed inset-0 z-50 bg-slate-900/40 flex items-center justify-center p-4">
    <form method="POST" action="/manager/requisitos/reglas/guardar" class="bg-white rounded-2xl w-full max-w-xl shadow-xl">
        <?= \Core\Security::getCsrfField() ?>
        <input type="hidden" name="id" id="rId" value="">

        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-black text-slate-800" id="rTitulo">Nueva regla</h2>
        </div>

        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Texto del requisito</label>
                <textarea name="label" id="rLabel" rows="3" required
                          class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition resize-y"
                          placeholder="Ej.: Certificado médico vigente"></textarea>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Se ofrece en</label>
                <select name="scope" id="rScope" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
                    <?php foreach ($alcances as $v => $t): ?>
                    <option value="<?= $v ?>"><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <label class="flex items-start gap-2.5 cursor-pointer">
                <input type="checkbox" name="requires_file" value="1" id="rFile"
                       class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span>
                    <span class="block text-sm font-bold text-slate-700">Pide un documento</span>
                    <span class="block text-xs text-gray-400">El solicitante deberá adjuntar un archivo para este requisito.</span>
                </span>
            </label>

            <label class="flex items-start gap-2.5 cursor-pointer">
                <input type="checkbox" name="is_default" value="1" id="rDefault"
                       class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span>
                    <span class="block text-sm font-bold text-slate-700">Uso general</span>
                    <span class="block text-xs text-gray-400">Viene premarcada al definir los requisitos de un curso que aún no tiene ninguno.</span>
                </span>
            </label>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2">
            <p class="text-[11px] text-gray-400" id="rAviso"></p>
            <button type="button" onclick="document.getElementById('dlgRegla').classList.add('hidden')"
                    class="ml-auto px-4 py-2 rounded-lg border border-gray-200 text-slate-500 text-sm font-bold hover:bg-slate-50 transition">
                Cancelar
            </button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2 rounded-lg transition">
                Guardar
            </button>
        </div>
    </form>
</div>

<script>
function abrirRegla(r) {
    const esNueva = !r;
    document.getElementById('rTitulo').textContent = esNueva ? 'Nueva regla' : 'Editar regla';
    document.getElementById('rId').value      = esNueva ? '' : r.id;
    document.getElementById('rLabel').value   = esNueva ? '' : r.label;
    document.getElementById('rScope').value   = esNueva ? 'any' : r.scope;
    document.getElementById('rFile').checked  = esNueva ? true : !!r.requires_file;
    document.getElementById('rDefault').checked = esNueva ? false : !!r.is_default;
    document.getElementById('rAviso').textContent = esNueva
        ? ''
        : 'Los cursos que ya la usan conservan su texto hasta que los actualices.';

    document.getElementById('dlgRegla').classList.remove('hidden');
    document.getElementById('rLabel').focus();
}

(function () {
    const buscador = document.getElementById('buscador');
    const contador = document.getElementById('contadorVisibles');
    const sinNada  = document.getElementById('sinResultados');
    const filas    = Array.from(document.querySelectorAll('#listaReglas .regla'));

    let temporizador;
    buscador.addEventListener('input', function () {
        clearTimeout(temporizador);
        temporizador = setTimeout(function () {
            const texto = buscador.value.trim().toLowerCase();
            let visibles = 0;
            for (const f of filas) {
                const ok = texto === '' || f.dataset.buscar.includes(texto);
                f.classList.toggle('hidden', !ok);
                if (ok) visibles++;
            }
            contador.textContent = visibles;
            sinNada.classList.toggle('hidden', visibles > 0);
        }, 120);
    });

    // Escape cierra el diálogo
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.getElementById('dlgRegla').classList.add('hidden');
        }
    });
})();
</script>
