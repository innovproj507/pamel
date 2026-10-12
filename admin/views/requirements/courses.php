<?php
/**
 * Requisitos por curso: todos los cursos activos, tengan o no documento.
 *
 * La bandeja de importación lista documentos Word, así que un curso para el
 * que nadie escribió uno no aparecía en ninguna parte. Esta pantalla entra
 * por el curso, que es como se piensa el problema.
 */
$cursos  = $cursos  ?? [];
$conteos = $conteos ?? ['pending' => 0];

$sinRequisitos = 0;
foreach ($cursos as $c) {
    if ((int) $c['n_nuevo'] + (int) $c['n_refresco'] === 0) {
        $sinRequisitos++;
    }
}
?>

<div class="mb-6 flex flex-col lg:flex-row lg:items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Requisitos por curso</h1>
        <p class="text-sm text-gray-500 max-w-3xl">
            Todos los cursos activos. Entra en uno para marcar los requisitos que le aplican
            desde el catálogo, o escribir los suyos propios.
        </p>
    </div>
    <div class="flex gap-3 flex-shrink-0">
        <a href="/manager/requisitos/reglas"
           class="card px-4 py-3 text-center hover:border-blue-300 transition block">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Catálogo</p>
            <p class="text-xs font-black text-blue-600 mt-1">Ver reglas <i class="fas fa-arrow-right text-[9px]"></i></p>
        </a>
        <a href="/manager/requisitos"
           class="card px-4 py-3 text-center hover:border-amber-300 transition block">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Documentos</p>
            <p class="text-xs font-black text-amber-600 mt-1"><?= (int) $conteos['pending'] ?> por revisar</p>
        </a>
    </div>
</div>

<?php if ($sinRequisitos > 0): ?>
<div class="card p-4 mb-4 border-l-4 border-l-amber-400 flex items-start gap-3">
    <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5"></i>
    <p class="text-sm text-slate-600">
        <strong class="text-slate-800"><?= $sinRequisitos ?> curso<?= $sinRequisitos === 1 ? '' : 's' ?></strong>
        sin ningún requisito publicado. El formulario de admisión no pedirá documentos para
        <?= $sinRequisitos === 1 ? 'él' : 'ellos' ?>. Aparecen primero en la lista.
    </p>
</div>
<?php endif; ?>

<!-- Buscador -->
<div class="card p-3 mb-4 flex flex-col sm:flex-row sm:items-center gap-3">
    <div class="relative flex-1">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 text-sm"></i>
        <input type="search" id="buscador" autofocus autocomplete="off"
               placeholder="Escribe el nombre o el código del curso…"
               class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
    </div>
    <label class="flex items-center gap-2 text-xs font-bold text-slate-500 whitespace-nowrap px-1 cursor-pointer">
        <input type="checkbox" id="soloSin" class="rounded border-gray-300 text-blue-600">
        Solo los que no tienen requisitos
    </label>
    <p class="text-xs font-bold text-gray-400 whitespace-nowrap px-1">
        <span id="contadorVisibles"><?= count($cursos) ?></span> de <?= count($cursos) ?>
    </p>
</div>

<div class="card overflow-hidden" id="listaCursos">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-[10px] font-black text-gray-400 uppercase tracking-widest border-b" style="border-color: var(--card-border)">
                <th class="px-5 py-3">Curso</th>
                <th class="px-3 py-3 text-center w-28">Primera vez</th>
                <th class="px-3 py-3 text-center w-28">Renovación</th>
                <th class="px-5 py-3 w-20"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cursos as $c):
                $n  = (int) $c['n_nuevo'];
                $r  = (int) $c['n_refresco'];
                $sin = ($n + $r) === 0;
            ?>
            <tr class="curso border-b last:border-0 hover:bg-slate-50/70 transition"
                style="border-color: var(--card-border)"
                data-buscar="<?= htmlspecialchars(mb_strtolower($c['name'] . ' ' . (string) $c['course_code'])) ?>"
                data-sin="<?= $sin ? '1' : '0' ?>">
                <td class="px-5 py-3">
                    <a href="/manager/requisitos/curso/<?= (int) $c['id'] ?>"
                       class="font-bold text-slate-800 hover:text-blue-600 transition">
                        <?= htmlspecialchars($c['name']) ?>
                    </a>
                    <?php if (!empty($c['course_code'])): ?>
                    <span class="ml-2 text-[10px] font-black text-gray-400 bg-gray-50 border border-gray-200 px-1.5 py-0.5 rounded">
                        <?= htmlspecialchars($c['course_code']) ?>
                    </span>
                    <?php endif; ?>
                </td>
                <td class="px-3 py-3 text-center">
                    <?php if ($n > 0): ?>
                    <span class="text-xs font-black text-blue-600 bg-blue-50 border border-blue-200 px-2 py-1 rounded-full"><?= $n ?></span>
                    <?php else: ?>
                    <span class="text-xs text-gray-300">—</span>
                    <?php endif; ?>
                </td>
                <td class="px-3 py-3 text-center">
                    <?php if ($r > 0): ?>
                    <span class="text-xs font-black text-violet-600 bg-violet-50 border border-violet-200 px-2 py-1 rounded-full"><?= $r ?></span>
                    <?php else: ?>
                    <span class="text-xs text-gray-300">—</span>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="/manager/requisitos/curso/<?= (int) $c['id'] ?>"
                       class="text-xs font-black <?= $sin ? 'text-amber-600' : 'text-blue-600' ?> hover:underline whitespace-nowrap">
                        <?= $sin ? 'Definir' : 'Editar' ?> <i class="fas fa-arrow-right text-[9px]"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<p id="sinResultados" class="hidden card p-10 text-center text-sm font-bold text-gray-400 mt-4">
    Ningún curso coincide con la búsqueda.
</p>

<script>
(function () {
    const buscador = document.getElementById('buscador');
    const soloSin  = document.getElementById('soloSin');
    const contador = document.getElementById('contadorVisibles');
    const sinNada  = document.getElementById('sinResultados');
    const filas    = Array.from(document.querySelectorAll('#listaCursos .curso'));

    function filtrar() {
        const texto = buscador.value.trim().toLowerCase();
        const soloVacios = soloSin.checked;
        let visibles = 0;

        for (const fila of filas) {
            const coincide = (texto === '' || fila.dataset.buscar.includes(texto))
                          && (!soloVacios || fila.dataset.sin === '1');
            fila.classList.toggle('hidden', !coincide);
            if (coincide) visibles++;
        }

        contador.textContent = visibles;
        sinNada.classList.toggle('hidden', visibles > 0);
    }

    let temporizador;
    buscador.addEventListener('input', function () {
        clearTimeout(temporizador);
        temporizador = setTimeout(filtrar, 120);
    });
    soloSin.addEventListener('change', filtrar);

    // Enter abre el curso si sólo queda uno
    buscador.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const visibles = filas.filter(f => !f.classList.contains('hidden'));
        if (visibles.length === 1) {
            visibles[0].querySelector('a').click();
        }
    });
})();
</script>
