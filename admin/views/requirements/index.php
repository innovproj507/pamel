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
    'pending' => ['Por revisar', $conteos['pending'], 'p-ambar'],
    'mapped'  => ['Publicados',  $conteos['mapped'],  'p-verde'],
    'ignored' => ['Descartados', $conteos['ignored'], 'p-gris'],
];

$etiquetaModo = static fn(string $carpeta): array => match ($carpeta) {
    'R-RF'  => ['Refresco', 'p-violeta'],
    'R-AS'  => ['Ascenso',  'p-cian'],
    default => ['Curso regular', 'p-azul'],
};
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Requisitos de admisión</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            Documentos importados de <code class="cod">Requisitos/</code> · asigna cada uno a su curso para publicarlos
        </p>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Pestañas y buscador -->
    <div class="px-4 py-3.5 flex flex-wrap items-end gap-2.5 border-b" style="border-color: var(--card-border)">

        <div>
            <label class="filtro-rot">Bandeja</label>
            <div class="flex items-center gap-1.5 flex-wrap">
                <?php foreach ($pestanas as $clave => [$texto, $n, $clase]): ?>
                <a href="/manager/requisitos?estado=<?= $clave ?>"
                   class="acc <?= $estado === $clave ? 'acc-azul' : 'acc-gris' ?>"
                   <?= $estado === $clave ? 'style="border-color:#2563eb; background:#2563eb; color:#fff"' : '' ?>>
                    <?= $texto ?>
                    <span class="pastilla <?= $estado === $clave ? '' : $clase ?>" style="padding:1px 6px; font-size:9.5px<?= $estado === $clave ? '; background:rgba(255,255,255,.22); color:#fff' : '' ?>">
                        <?= (int) $n ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($docs)): ?>
        <div>
            <label class="filtro-rot" for="buscador">Buscar</label>
            <div class="relative">
                <i data-lucide="search" class="ic ic-14 absolute left-3 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="search" id="buscador" autofocus autocomplete="off"
                       placeholder="Nombre del curso o parte de un requisito…"
                       class="campo" style="padding-left:32px; width:300px; border-radius:999px">
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Resumen -->
    <div class="px-4 py-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="kpi">
            <div class="kpi-label">Documentos</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Cursos cubiertos</div>
            <div class="kpi-value"><?= number_format((int) $resumen['cursos']) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Requisitos publicados</div>
            <div class="kpi-value"><?= number_format((int) $resumen['requisitos']) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Por revisar</div>
            <div class="kpi-value"><?= number_format((int) $conteos['pending']) ?></div>
        </div>
    </div>
</div>

<?php if (empty($docs)): ?>
<div class="card mt-4">
    <div class="vacio">
        <i data-lucide="circle-check" class="ic"></i>
        <p><?= $estado === 'pending' ? 'No queda ningún documento por revisar.' : 'Nada que mostrar aquí.' ?></p>
    </div>
</div>
<?php else: ?>

<p class="text-[11.5px] font-semibold text-slate-400 mt-4 mb-2 px-1">
    <span id="contadorVisibles"><?= count($docs) ?></span> de <?= $total ?> documento<?= $total === 1 ? '' : 's' ?>
</p>

<div class="space-y-2" id="listaDocs">
    <?php foreach ($docs as $d):
        $sinAsignar = empty($d['product_id']);
        [$modoTexto, $modoClase] = $etiquetaModo($d['folder']);
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
                class="w-full flex flex-wrap items-center gap-3 px-4 py-3 text-left hover:bg-slate-50 transition cabecera">
            <i data-lucide="chevron-right" class="ic ic-14 text-slate-300 transition-transform flecha flex-shrink-0"></i>

            <span class="pastilla <?= $modoClase ?> flex-shrink-0"><?= $modoTexto ?></span>

            <span class="font-bold text-slate-800 text-[12.5px] flex-1 min-w-0 truncate"
                  title="<?= htmlspecialchars($d['doc_title'], ENT_QUOTES) ?>">
                <?= htmlspecialchars($d['doc_title']) ?>
            </span>

            <span class="text-[10.5px] text-slate-400 font-semibold whitespace-nowrap flex-shrink-0">
                <?= count($lista) ?> requisitos
            </span>

            <?php if (!$sinAsignar): ?>
            <span class="pastilla p-verde flex-shrink-0" style="max-width:16rem"
                  title="<?= htmlspecialchars((string) $d['product_name'], ENT_QUOTES) ?>">
                <i data-lucide="check" class="ic" style="width:10px;height:10px"></i>
                <span class="truncate"><?= htmlspecialchars(mb_strimwidth((string) $d['product_name'], 0, 34, '…')) ?></span>
            </span>
            <?php else: ?>
            <span class="pastilla p-ambar flex-shrink-0">Sin curso</span>
            <?php endif; ?>
        </button>

        <!-- Detalle -->
        <div class="detalle hidden border-t" style="border-color: var(--card-border)">

            <ul class="px-5 py-3 space-y-1.5 bg-slate-50/60">
                <?php foreach ($lista as $r): ?>
                <li class="flex items-start gap-2 text-[12px] text-slate-600">
                    <i data-lucide="<?= !empty($r['requires_file']) ? 'paperclip' : 'info' ?>"
                       class="ic flex-shrink-0 mt-0.5 <?= !empty($r['requires_file']) ? 'text-blue-400' : 'text-slate-300' ?>"
                       style="width:12px;height:12px"></i>
                    <span><?= htmlspecialchars($r['label']) ?></span>
                </li>
                <?php endforeach; ?>
                <li class="text-[10px] font-semibold text-slate-400 pt-1 flex items-center gap-1.5">
                    <i data-lucide="paperclip" class="ic" style="width:10px;height:10px"></i> pide documento
                    <span class="text-slate-300">·</span>
                    <i data-lucide="info" class="ic" style="width:10px;height:10px"></i> solo informativo
                </li>
            </ul>

            <div class="px-5 py-3 flex flex-col lg:flex-row gap-2.5 lg:items-center border-t" style="border-color: var(--card-border)">
                <form method="POST" action="/manager/requisitos/<?= (int) $d['id'] ?>/asignar"
                      class="flex flex-1 flex-col sm:flex-row gap-2"
                      onsubmit="return this.product_id.value ? true : (alert('Selecciona primero el curso.'), false);">
                    <?= \Core\Security::getCsrfField() ?>
                    <input type="hidden" name="volver_estado" value="<?= htmlspecialchars($estado, ENT_QUOTES) ?>">
                    <span class="flex-1 ranura-curso" data-elegido="<?= (int) ($d['product_id'] ?? 0) ?>"></span>
                    <button type="submit" class="btn-verde">
                        <i data-lucide="check" class="ic ic-14"></i> Asignar y publicar
                    </button>
                </form>

                <?php if (!$sinAsignar): ?>
                <form method="POST" action="/manager/requisitos/<?= (int) $d['id'] ?>/publicar">
                    <?= \Core\Security::getCsrfField() ?>
                    <input type="hidden" name="volver_estado" value="<?= htmlspecialchars($estado, ENT_QUOTES) ?>">
                    <button type="submit" class="btn-sec w-full lg:w-auto justify-center"
                            title="Vuelve a copiar la lista del documento al curso">
                        <i data-lucide="refresh-cw" class="ic ic-14"></i> Republicar
                    </button>
                </form>
                <?php endif; ?>

                <?php if ($estado !== 'ignored'): ?>
                <form method="POST" action="/manager/requisitos/<?= (int) $d['id'] ?>/descartar"
                      onsubmit="return confirm('¿Descartar este documento? No se borra nada, solo deja de aparecer como pendiente.');">
                    <?= \Core\Security::getCsrfField() ?>
                    <input type="hidden" name="volver_estado" value="<?= htmlspecialchars($estado, ENT_QUOTES) ?>">
                    <button type="submit" class="btn-sec w-full lg:w-auto justify-center">
                        <i data-lucide="x" class="ic ic-14"></i> Descartar
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div id="sinResultados" class="hidden card mt-2">
    <div class="vacio">
        <i data-lucide="search-x" class="ic"></i>
        <p>Ningún documento coincide con la búsqueda.</p>
    </div>
</div>

<!--
    El selector de curso se escribe una sola vez y se clona en la fila que se
    abre. Repetirlo en cada documento serían miles de <option> por página.
-->
<template id="plantillaCurso">
    <select name="product_id" class="campo w-full">
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
