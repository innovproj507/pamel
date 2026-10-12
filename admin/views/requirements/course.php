<?php
/**
 * Editor de los requisitos de un curso, por modalidad.
 *
 * Dos fuentes: las reglas del catálogo, que se marcan con una casilla, y las
 * líneas propias de este curso, que se escriben a mano. Al guardar, ambas se
 * copian a course_requirements, que es lo que lee el formulario de admisión.
 *
 * Si el curso aún no tiene nada en una modalidad, vienen premarcadas las
 * reglas de uso general (pasaporte, certificado médico…).
 */
$curso    = $curso    ?? [];
$nuevo    = $nuevo    ?? [];
$refresco = $refresco ?? [];
$reglas   = $reglas   ?? ['new' => [], 'refresher' => []];
$marcadas = $marcadas ?? ['new' => [], 'refresher' => []];
$sueltas  = $sueltas  ?? ['new' => [], 'refresher' => []];

$bloques = [
    'new'       => ['Primera vez', $nuevo,    'blue',
                    'Para quien toma el curso por primera vez.'],
    'refresher' => ['Renovación',  $refresco, 'violet',
                    'Para quien renueva un certificado vencido.'],
];
?>

<div class="mb-6 flex items-start gap-4">
    <a href="/manager/requisitos/cursos" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm flex-shrink-0">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div class="flex-1">
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($curso['name']) ?></h1>
        <p class="text-sm text-gray-500">
            Requisitos de admisión
            <?php if (!empty($curso['course_code'])): ?>
            · código <?= htmlspecialchars($curso['course_code']) ?>
            <?php endif; ?>
        </p>
    </div>
    <a href="/manager/requisitos/reglas"
       class="text-xs font-black text-blue-600 hover:underline whitespace-nowrap pt-2">
        Catálogo de reglas <i class="fas fa-arrow-right text-[9px]"></i>
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
    <?php foreach ($bloques as $modo => [$titulo, $lista, $color, $ayuda]):
        $listaReglas = $reglas[$modo]   ?? [];
        $yaMarcadas  = $marcadas[$modo] ?? [];
        $misSueltas  = $sueltas[$modo]  ?? [];

        // Curso sin nada en esta modalidad: se premarcan las de uso general
        $virgen = empty($lista);
    ?>
    <form method="POST" action="/manager/requisitos/curso/<?= (int) $curso['id'] ?>/guardar"
          class="card flex flex-col" data-modo="<?= $modo ?>">
        <?= \Core\Security::getCsrfField() ?>
        <input type="hidden" name="mode" value="<?= $modo ?>">

        <div class="px-5 py-4 border-b" style="border-color: var(--card-border)">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-<?= $color ?>-500"></span>
                <h2 class="text-sm font-black text-slate-800"><?= $titulo ?></h2>
                <span class="ml-auto text-[11px] font-bold text-gray-400">
                    <span class="contador"><?= count($lista) ?></span> requisitos
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1"><?= $ayuda ?></p>
            <?php if ($virgen): ?>
            <p class="text-[11px] text-amber-600 font-bold mt-2">
                <i class="fas fa-wand-magic-sparkles"></i>
                Curso sin requisitos: vienen premarcados los de uso general.
            </p>
            <?php endif; ?>
        </div>

        <!-- Catálogo -->
        <div class="px-5 pt-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    Del catálogo
                </p>
                <button type="button" class="text-[11px] font-bold text-gray-400 hover:text-rose-500 transition limpiar">
                    Desmarcar todo
                </button>
            </div>
            <div class="relative mb-2">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-300 text-[11px]"></i>
                <input type="search" autocomplete="off" placeholder="Filtrar reglas…"
                       class="filtroReglas w-full pl-8 pr-3 py-1.5 rounded-lg border border-gray-200 text-xs focus:border-blue-500 outline-none transition">
            </div>
            <div class="border rounded-xl max-h-80 overflow-y-auto divide-y listaReglas" style="border-color: var(--card-border)">
                <?php foreach ($listaReglas as $r):
                    $id      = (int) $r['id'];
                    $activa  = $virgen ? !empty($r['is_default']) : isset($yaMarcadas[$id]);
                ?>
                <label class="flex items-start gap-2.5 px-3 py-2.5 hover:bg-slate-50/70 transition cursor-pointer regla"
                       data-buscar="<?= htmlspecialchars(mb_strtolower($r['label'])) ?>">
                    <input type="checkbox" name="rule_id[]" value="<?= $id ?>" <?= $activa ? 'checked' : '' ?>
                           class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 flex-shrink-0">
                    <span class="flex-1 min-w-0">
                        <span class="block text-xs text-slate-700 leading-snug"><?= htmlspecialchars($r['label']) ?></span>
                        <span class="block text-[10px] text-gray-400 mt-0.5">
                            <?php if (!empty($r['requires_file'])): ?>
                            <i class="fas fa-paperclip"></i> pide documento
                            <?php else: ?>
                            <i class="fas fa-circle-info"></i> solo informativo
                            <?php endif; ?>
                            <?php if (!empty($r['is_default'])): ?>
                            &nbsp;·&nbsp; <span class="text-emerald-600 font-bold">uso general</span>
                            <?php endif; ?>
                            <?php if ((int) $r['cursos'] > 0): ?>
                            &nbsp;·&nbsp; en <?= (int) $r['cursos'] ?> curso<?= (int) $r['cursos'] === 1 ? '' : 's' ?>
                            <?php endif; ?>
                        </span>
                    </span>
                </label>
                <?php endforeach; ?>
                <p class="hidden px-3 py-6 text-center text-xs text-gray-400 sinReglas">
                    Ninguna regla coincide.
                </p>
            </div>
        </div>

        <!-- Líneas propias de este curso -->
        <div class="px-5 pt-4 pb-2">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                Solo de este curso
            </p>
            <div class="space-y-2" data-lista="<?= $modo ?>">
                <?php foreach ($misSueltas as $i => $r): ?>
                <div class="flex items-start gap-2 requisito">
                    <textarea name="label[<?= $i ?>]" rows="2"
                              class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-blue-500 outline-none transition resize-y"><?= htmlspecialchars($r['label']) ?></textarea>
                    <label class="flex flex-col items-center gap-1 pt-2 cursor-pointer flex-shrink-0" title="Pedir un documento al solicitante">
                        <input type="hidden" name="requires_file[<?= $i ?>]" value="0">
                        <input type="checkbox" name="requires_file[<?= $i ?>]" value="1"
                               <?= !empty($r['requires_file']) ? 'checked' : '' ?>
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <i class="fas fa-paperclip text-[10px] text-gray-300"></i>
                    </label>
                    <button type="button" onclick="this.closest('.requisito').remove()"
                            class="pt-2 text-gray-300 hover:text-rose-500 transition flex-shrink-0" title="Quitar">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="agregarRequisito('<?= $modo ?>')"
                    class="mt-2 px-3 py-1.5 rounded-lg border border-gray-200 text-slate-500 text-xs font-bold hover:bg-slate-50 transition">
                <i class="fas fa-plus mr-1"></i> Añadir línea propia
            </button>
        </div>

        <div class="px-5 py-3 flex items-center gap-2 border-t mt-auto" style="border-color: var(--card-border)">
            <p class="text-[11px] text-gray-400">
                Guardar reemplaza los requisitos de esta modalidad.
            </p>
            <button type="submit"
                    class="ml-auto bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2 rounded-lg transition">
                Guardar
            </button>
        </div>
    </form>
    <?php endforeach; ?>
</div>

<script>
// Añade una línea propia a la modalidad indicada.
function agregarRequisito(modo) {
    const cont = document.querySelector('[data-lista="' + modo + '"]');

    // Índice único por fila: no se reutiliza aunque se borren filas, así el
    // texto y su casilla nunca se descolocan.
    const idx = 'n' + Date.now().toString(36) + Math.floor(Math.random() * 1000);
    const div = document.createElement('div');
    div.className = 'flex items-start gap-2 requisito';
    div.innerHTML =
        '<textarea name="label[' + idx + ']" rows="2" class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-blue-500 outline-none transition resize-y" placeholder="Texto del requisito"></textarea>' +
        '<label class="flex flex-col items-center gap-1 pt-2 cursor-pointer flex-shrink-0" title="Pedir un documento al solicitante">' +
          '<input type="hidden" name="requires_file[' + idx + ']" value="0">' +
          '<input type="checkbox" name="requires_file[' + idx + ']" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">' +
          '<i class="fas fa-paperclip text-[10px] text-gray-300"></i>' +
        '</label>' +
        '<button type="button" onclick="this.closest(\'.requisito\').remove()" class="pt-2 text-gray-300 hover:text-rose-500 transition flex-shrink-0"><i class="fas fa-times text-xs"></i></button>';
    cont.appendChild(div);
    div.querySelector('textarea').focus();
    actualizarContador(cont.closest('form'));
}

// Cada bloque filtra y cuenta por su cuenta.
document.querySelectorAll('form[data-modo]').forEach(function (form) {
    const filtro  = form.querySelector('.filtroReglas');
    const reglas  = Array.from(form.querySelectorAll('.regla'));
    const sinNada = form.querySelector('.sinReglas');

    filtro.addEventListener('input', function () {
        const texto = filtro.value.trim().toLowerCase();
        let visibles = 0;
        for (const r of reglas) {
            const ok = texto === '' || r.dataset.buscar.includes(texto);
            r.classList.toggle('hidden', !ok);
            if (ok) visibles++;
        }
        sinNada.classList.toggle('hidden', visibles > 0);
    });

    // No envía el formulario si se pulsa Enter en el filtro
    filtro.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); }
    });

    form.querySelector('.limpiar').addEventListener('click', function () {
        reglas.forEach(r => { r.querySelector('input').checked = false; });
        actualizarContador(form);
    });

    form.addEventListener('change', function () { actualizarContador(form); });
});

function actualizarContador(form) {
    const marcadas = form.querySelectorAll('.regla input:checked').length;
    const propias  = form.querySelectorAll('.requisito').length;
    form.querySelector('.contador').textContent = marcadas + propias;
}
</script>
