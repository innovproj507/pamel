<?php
/**
 * Bandeja de documentos de requisitos importados de la carpeta Requisitos/.
 *
 * Una fila por documento, todas en la misma página y filtrables al escribir.
 * Los requisitos y el formulario de asignación se despliegan solo en la fila
 * que se abre: con las tarjetas desplegadas había que paginar a ciegas para
 * encontrar un curso.
 */
$docs    = $docs    ?? [];
$cursos  = $cursos  ?? [];
$conteos = $conteos ?? ['pending' => 0, 'mapped' => 0, 'ignored' => 0];
$resumen = $resumen ?? ['cursos' => 0, 'requisitos' => 0];
$estado  = $estado  ?? 'pending';
$total   = (int) ($total ?? count($docs));

$pestanas = [
    'pending' => ['Por revisar', $conteos['pending'], 'amber'],
    'mapped'  => ['Publicados',  $conteos['mapped'],  'emerald'],
    'ignored' => ['Descartados', $conteos['ignored'], 'slate'],
];

$etiquetaModo = static fn(string $carpeta): array => match ($carpeta) {
    'R-RF'  => ['Refresco', 'violet'],
    'R-AS'  => ['Ascenso',  'sky'],
    default => ['Curso regular', 'blue'],
};
?>

<div class="mb-6 flex flex-col lg:flex-row lg:items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Requisitos de admisión</h1>
        <p class="text-sm text-gray-500 max-w-3xl">
            Documentos importados de la carpeta <code>Requisitos/</code>. Asigna cada uno al curso
            que le corresponde: al hacerlo, sus requisitos quedan publicados y disponibles para el
            formulario de admisión.
        </p>
    </div>
    <div class="flex gap-3 flex-shrink-0">
        <div class="card px-4 py-3 text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Cursos cubiertos</p>
            <p class="text-xl font-black text-slate-800 mt-0.5"><?= (int) $resumen['cursos'] ?></p>
        </div>
        <div class="card px-4 py-3 text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Requisitos</p>
            <p class="text-xl font-black text-slate-800 mt-0.5"><?= (int) $resumen['requisitos'] ?></p>
        </div>
    </div>
</div>

<!-- Pestañas -->
<div class="flex items-center gap-2 mb-4 flex-wrap">
    <?php foreach ($pestanas as $clave => [$texto, $n, $color]): ?>
    <a href="/manager/requisitos?estado=<?= $clave ?>"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition border <?=
           $estado === $clave
               ? 'bg-slate-800 text-white border-slate-800'
               : 'bg-white text-slate-500 border-gray-100 hover:bg-slate-50' ?>">
        <?= $texto ?>
        <span class="text-[10px] font-black px-1.5 py-0.5 rounded-full <?=
            $estado === $clave ? 'bg-white/20' : 'bg-' . $color . '-50 text-' . $color . '-600' ?>">
            <?= (int) $n ?>
        </span>
    </a>
    <?php endforeach; ?>
</div>

<?php if (empty($docs)): ?>
<div class="card p-14 text-center">
    <i class="fas fa-circle-check text-4xl text-emerald-300 mb-4"></i>
    <p class="text-lg font-bold text-slate-700">
        <?= $estado === 'pending' ? 'No queda ningún documento por revisar' : 'Nada que mostrar aquí' ?>
    </p>
</div>
<?php else: ?>

<!-- Buscador -->
<div class="card p-3 mb-4 flex flex-col sm:flex-row sm:items-center gap-3">
    <div class="relative flex-1">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
        <input type="search" id="buscador" autofocus autocomplete="off"
               placeholder="Escribe el nombre del curso o parte de un requisito…"
               class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
    </div>
    <p class="text-xs font-bold text-gray-400 whitespace-nowrap px-1">
        <span id="contadorVisibles"><?= count($docs) ?></span> de <?= $total ?> documento<?= $total === 1 ? '' : 's' ?>
    </p>
</div>

<div class="space-y-2" id="listaDocs">
    <?php foreach ($docs as $d):
        $sinAsignar = empty($d['product_id']);
        [$modoTexto, $modoColor] = $etiquetaModo($d['folder']);
        $lista = $d['lista'];

        // Texto sobre el que filtra el buscador: título, curso y requisitos
        $buscable = mb_strtolower(
            $d['doc_title'] . ' ' . ($d['product_name'] ?? '') . ' ' . $modoTexto . ' '
            . implode(' ', array_column($lista, 'label'))
        );
    ?>
    <div class="card overflow-hidden doc" data-buscar="<?= htmlspecialchars($buscable) ?>">

        <!-- Cabecera: clic para desplegar -->
        <button type="button"
                class="w-full flex flex-wrap items-center gap-3 px-4 py-3 text-left hover:bg-slate-50/70 transition cabecera">
            <i class="fas fa-chevron-right text-gray-300 text-xs transition-transform flecha"></i>
            <span class="text-[10px] font-black uppercase tracking-wider bg-<?= $modoColor ?>-50 text-<?= $modoColor ?>-600 border border-<?= $modoColor ?>-200 px-2 py-1 rounded-full flex-shrink-0">
                <?= $modoTexto ?>
            </span>
            <span class="font-bold text-slate-800 text-sm flex-1 min-w-0 truncate" title="<?= htmlspecialchars($d['doc_title']) ?>">
                <?= htmlspecialchars($d['doc_title']) ?>
            </span>
            <span class="text-[11px] text-gray-400 font-semibold whitespace-nowrap flex-shrink-0">
                <?= count($lista) ?> requisitos
            </span>
            <?php if (!$sinAsignar): ?>
            <span class="text-[11px] font-black text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-1 rounded-full max-w-[16rem] truncate flex-shrink-0"
                  title="<?= htmlspecialchars((string) $d['product_name']) ?>">
                <i class="fas fa-check text-[9px] mr-0.5"></i>
                <?= htmlspecialchars(mb_strimwidth((string) $d['product_name'], 0, 34, '…')) ?>
            </span>
            <?php else: ?>
            <span class="text-[11px] font-black text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1 rounded-full flex-shrink-0">
                Sin curso
            </span>
            <?php endif; ?>
        </button>

        <!-- Detalle -->
        <div class="detalle hidden border-t" style="border-color: var(--card-border)">

            <ul class="px-5 py-3 space-y-1 bg-slate-50/40">
                <?php foreach ($lista as $r): ?>
                <li class="flex items-start gap-2 text-xs text-slate-600">
                    <i class="fas <?= !empty($r['requires_file']) ? 'fa-paperclip text-blue-400' : 'fa-circle-info text-gray-300' ?> text-[10px] mt-0.5 flex-shrink-0"></i>
                    <span><?= htmlspecialchars($r['label']) ?></span>
                </li>
                <?php endforeach; ?>
                <li class="text-[10px] text-gray-400 pt-1">
                    <i class="fas fa-paperclip"></i> pide documento &nbsp;·&nbsp;
                    <i class="fas fa-circle-info"></i> solo informativo
                </li>
            </ul>

            <div class="px-5 py-3 flex flex-col lg:flex-row gap-3 lg:items-center border-t" style="border-color: var(--card-border)">
                <form method="POST" action="/manager/requisitos/<?= (int) $d['id'] ?>/asignar"
                      class="flex flex-1 flex-col sm:flex-row gap-2"
                      onsubmit="return this.product_id.value ? true : (alert('Selecciona primero el curso.'), false);">
                    <?= \Core\Security::getCsrfField() ?>
                    <input type="hidden" name="volver_estado" value="<?= htmlspecialchars($estado) ?>">
                    <span class="flex-1 ranura-curso" data-elegido="<?= (int) ($d['product_id'] ?? 0) ?>"></span>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2 rounded-lg transition whitespace-nowrap">
                        <i class="fas fa-check mr-1"></i> Asignar y publicar
                    </button>
                </form>

                <?php if (!$sinAsignar): ?>
                <form method="POST" action="/manager/requisitos/<?= (int) $d['id'] ?>/publicar">
                    <?= \Core\Security::getCsrfField() ?>
                    <input type="hidden" name="volver_estado" value="<?= htmlspecialchars($estado) ?>">
                    <button type="submit" class="w-full lg:w-auto px-4 py-2 rounded-lg border border-gray-200 text-slate-500 text-sm font-bold hover:bg-slate-50 transition whitespace-nowrap"
                            title="Vuelve a copiar la lista del documento al curso">
                        <i class="fas fa-rotate mr-1"></i> Republicar
                    </button>
                </form>
                <?php endif; ?>

                <?php if ($estado !== 'ignored'): ?>
                <form method="POST" action="/manager/requisitos/<?= (int) $d['id'] ?>/descartar"
                      onsubmit="return confirm('¿Descartar este documento? No se borra nada, solo deja de aparecer como pendiente.');">
                    <?= \Core\Security::getCsrfField() ?>
                    <input type="hidden" name="volver_estado" value="<?= htmlspecialchars($estado) ?>">
                    <button type="submit" class="w-full lg:w-auto px-4 py-2 rounded-lg border border-gray-200 text-gray-400 text-sm font-bold hover:border-rose-200 hover:text-rose-600 transition whitespace-nowrap">
                        Descartar
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<p id="sinResultados" class="hidden card p-10 text-center text-sm font-bold text-gray-400">
    Ningún documento coincide con la búsqueda.
</p>

<!--
    El selector de curso se escribe una sola vez y se clona en la fila que se
    abre. Repetirlo en cada documento serían miles de <option> por página.
-->
<template id="plantillaCurso">
    <select name="product_id" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
        <option value="">Selecciona el curso…</option>
        <?php foreach ($cursos as $c): ?>
        <option value="<?= (int) $c['id'] ?>">
            <?= htmlspecialchars($c['name']) ?><?= $c['course_code'] ? ' (' . htmlspecialchars($c['course_code']) . ')' : '' ?><?= (int) $c['requisitos'] > 0 ? ' — ya tiene ' . (int) $c['requisitos'] : '' ?>
        </option>
        <?php endforeach; ?>
    </select>
</template>

<script>
(function () {
    const lista       = document.getElementById('listaDocs');
    const buscador    = document.getElementById('buscador');
    const contador    = document.getElementById('contadorVisibles');
    const sinNada     = document.getElementById('sinResultados');
    const plantilla   = document.getElementById('plantillaCurso');
    const documentos  = Array.from(lista.querySelectorAll('.doc'));

    // ── Desplegar una fila ──────────────────────────────────────────────
    lista.addEventListener('click', function (e) {
        const cabecera = e.target.closest('.cabecera');
        if (!cabecera) return;

        const doc     = cabecera.closest('.doc');
        const detalle = doc.querySelector('.detalle');
        const abierto = !detalle.classList.contains('hidden');

        detalle.classList.toggle('hidden', abierto);
        cabecera.querySelector('.flecha').style.transform = abierto ? '' : 'rotate(90deg)';

        // El selector de curso se clona la primera vez que se abre la fila
        const ranura = detalle.querySelector('.ranura-curso');
        if (!abierto && ranura && !ranura.firstElementChild) {
            const select = plantilla.content.firstElementChild.cloneNode(true);
            const elegido = ranura.dataset.elegido;
            if (elegido && elegido !== '0') {
                select.value = elegido;
            }
            ranura.appendChild(select);
        }
    });

    // ── Filtrar al escribir ─────────────────────────────────────────────
    let temporizador;
    buscador.addEventListener('input', function () {
        clearTimeout(temporizador);
        temporizador = setTimeout(filtrar, 120);
    });

    function filtrar() {
        const texto = buscador.value.trim().toLowerCase();
        let visibles = 0;

        for (const doc of documentos) {
            const coincide = texto === '' || doc.dataset.buscar.includes(texto);
            doc.classList.toggle('hidden', !coincide);
            if (coincide) visibles++;
        }

        contador.textContent = visibles;
        sinNada.classList.toggle('hidden', visibles > 0);
    }

    // Enter en el buscador abre el único resultado, si queda uno solo
    buscador.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const visibles = documentos.filter(d => !d.classList.contains('hidden'));
        if (visibles.length === 1) {
            visibles[0].querySelector('.cabecera').click();
            visibles[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
})();
</script>

<?php endif; ?>
