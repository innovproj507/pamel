<?php
/**
 * Editor de los requisitos publicados de un curso.
 * Dos listas independientes: curso regular y refresco.
 */
$curso    = $curso    ?? [];
$nuevo    = $nuevo    ?? [];
$refresco = $refresco ?? [];

$bloques = [
    'new'       => ['Curso regular', $nuevo,    'blue',
                    'Requisitos para quien toma el curso por primera vez.'],
    'refresher' => ['Refresco',      $refresco, 'violet',
                    'Requisitos para quien renueva un certificado vencido.'],
];
?>

<div class="mb-6 flex items-start gap-4">
    <a href="/manager/requisitos" class="w-10 h-10 rounded-full bg-white border border-gray-100 flex items-center justify-center text-gray-400 hover:text-blue-600 transition shadow-sm flex-shrink-0">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($curso['name']) ?></h1>
        <p class="text-sm text-gray-500">
            Requisitos de admisión
            <?php if (!empty($curso['course_code'])): ?>
            · código <?= htmlspecialchars($curso['course_code']) ?>
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
    <?php foreach ($bloques as $modo => [$titulo, $lista, $color, $ayuda]): ?>
    <form method="POST" action="/manager/requisitos/curso/<?= (int) $curso['id'] ?>/guardar" class="card">
        <?= \Core\Security::getCsrfField() ?>
        <input type="hidden" name="mode" value="<?= $modo ?>">

        <div class="px-5 py-4 border-b" style="border-color: var(--card-border)">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-<?= $color ?>-500"></span>
                <h2 class="text-sm font-black text-slate-800"><?= $titulo ?></h2>
                <span class="ml-auto text-[11px] font-bold text-gray-400"><?= count($lista) ?> requisitos</span>
            </div>
            <p class="text-xs text-gray-400 mt-1"><?= $ayuda ?></p>
        </div>

        <div class="px-5 py-3 space-y-2" data-lista="<?= $modo ?>">
            <?php foreach ($lista as $i => $r): ?>
            <div class="flex items-start gap-2 requisito">
                <span class="w-6 h-6 rounded-lg bg-slate-50 text-slate-400 text-[10px] font-black flex items-center justify-center flex-shrink-0 mt-2"><?= $i + 1 ?></span>
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

            <?php if (empty($lista)): ?>
            <p class="text-xs text-gray-400 py-6 text-center vacio">Sin requisitos para este modo.</p>
            <?php endif; ?>
        </div>

        <div class="px-5 py-3 flex items-center gap-2 border-t" style="border-color: var(--card-border)">
            <button type="button" onclick="agregarRequisito('<?= $modo ?>')"
                    class="px-3 py-2 rounded-lg border border-gray-200 text-slate-500 text-xs font-bold hover:bg-slate-50 transition">
                <i class="fas fa-plus mr-1"></i> Añadir
            </button>
            <button type="submit"
                    class="ml-auto bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2 rounded-lg transition">
                Guardar
            </button>
        </div>
    </form>
    <?php endforeach; ?>
</div>

<p class="text-xs text-gray-400 mt-5">
    <i class="fas fa-paperclip"></i> Marca la casilla cuando el requisito exija que el solicitante
    adjunte un documento. Los que no la llevan se muestran como información.
</p>

<script>
    // Añade una fila vacía a la lista del modo indicado.
    function agregarRequisito(modo) {
        const cont  = document.querySelector('[data-lista="' + modo + '"]');
        const vacio = cont.querySelector('.vacio');
        if (vacio) { vacio.remove(); }

        // Índice único por fila: no se reutiliza aunque se borren filas,
        // así el texto y su casilla nunca se descolocan.
        const n   = cont.querySelectorAll('.requisito').length;
        const idx = 'n' + (Date.now().toString(36)) + Math.floor(Math.random() * 1000);
        const div = document.createElement('div');
        div.className = 'flex items-start gap-2 requisito';
        div.innerHTML =
            '<span class="w-6 h-6 rounded-lg bg-slate-50 text-slate-400 text-[10px] font-black flex items-center justify-center flex-shrink-0 mt-2">' + (n + 1) + '</span>' +
            '<textarea name="label[' + idx + ']" rows="2" class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-xs focus:border-blue-500 outline-none transition resize-y" placeholder="Texto del requisito"></textarea>' +
            '<label class="flex flex-col items-center gap-1 pt-2 cursor-pointer flex-shrink-0" title="Pedir un documento al solicitante">' +
              '<input type="hidden" name="requires_file[' + idx + ']" value="0">' +
              '<input type="checkbox" name="requires_file[' + idx + ']" value="1" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">' +
              '<i class="fas fa-paperclip text-[10px] text-gray-300"></i>' +
            '</label>' +
            '<button type="button" onclick="this.closest(\'.requisito\').remove()" class="pt-2 text-gray-300 hover:text-rose-500 transition flex-shrink-0"><i class="fas fa-times text-xs"></i></button>';
        cont.appendChild(div);
    }
</script>
