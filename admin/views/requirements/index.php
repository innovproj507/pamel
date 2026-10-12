<?php
/**
 * Bandeja de documentos de requisitos importados de la carpeta Requisitos/.
 * Cada tarjeta muestra el contenido del documento y permite asignarlo a un
 * curso, volver a publicarlo o descartarlo.
 */
$docs     = $docs     ?? [];
$cursos   = $cursos   ?? [];
$conteos  = $conteos  ?? ['pending' => 0, 'mapped' => 0, 'ignored' => 0];
$resumen  = $resumen  ?? ['cursos' => 0, 'requisitos' => 0, 'modo_nuevo' => 0, 'modo_refresco' => 0];
$estado   = $estado   ?? 'pending';
$page     = (int) ($page ?? 1);
$lastPage = (int) ($lastPage ?? 1);
$total    = (int) ($total ?? 0);

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
<div class="flex items-center gap-2 mb-5 flex-wrap">
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

<div class="space-y-4">
    <?php foreach ($docs as $d):
        $sinAsignar = empty($d['product_id']);
        [$modoTexto, $modoColor] = $etiquetaModo($d['folder']);
        $lista = $d['lista'];
    ?>
    <div class="card overflow-hidden">

        <!-- Cabecera -->
        <div class="flex flex-wrap items-center gap-3 px-5 py-4 border-b" style="border-color: var(--card-border)">
            <span class="text-[10px] font-black uppercase tracking-wider bg-<?= $modoColor ?>-50 text-<?= $modoColor ?>-600 border border-<?= $modoColor ?>-200 px-2 py-1 rounded-full">
                <?= $modoTexto ?>
            </span>
            <p class="font-bold text-slate-800 text-sm flex-1 min-w-0 truncate" title="<?= htmlspecialchars($d['doc_title']) ?>">
                <?= htmlspecialchars($d['doc_title']) ?>
            </p>
            <span class="text-[11px] text-gray-400 font-semibold"><?= count($lista) ?> requisitos</span>

            <?php if (!$sinAsignar): ?>
            <a href="/manager/requisitos/curso/<?= (int) $d['product_id'] ?>"
               class="text-xs font-black text-blue-600 hover:underline whitespace-nowrap">
                <?= htmlspecialchars(mb_strimwidth((string) $d['product_name'], 0, 38, '…')) ?> →
            </a>
            <?php endif; ?>
        </div>

        <!-- Requisitos del documento -->
        <ul class="px-5 py-3 space-y-1 bg-slate-50/40">
            <?php foreach ($lista as $r): ?>
            <li class="flex items-start gap-2 text-xs text-slate-600">
                <i class="fas <?= !empty($r['requires_file']) ? 'fa-paperclip text-blue-400' : 'fa-circle-info text-gray-300' ?> text-[10px] mt-0.5 flex-shrink-0"></i>
                <span><?= htmlspecialchars($r['label']) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <p class="px-5 pb-2 text-[10px] text-gray-400">
            <i class="fas fa-paperclip"></i> pide documento &nbsp;·&nbsp;
            <i class="fas fa-circle-info"></i> solo informativo
        </p>

        <!-- Acciones -->
        <div class="px-5 py-3 flex flex-col lg:flex-row gap-3 lg:items-center border-t" style="border-color: var(--card-border)">
            <form method="POST" action="/manager/requisitos/<?= (int) $d['id'] ?>/asignar"
                  class="flex flex-1 flex-col sm:flex-row gap-2"
                  onsubmit="return this.product_id.value ? true : (alert('Selecciona primero el curso.'), false);">
                <?= \Core\Security::getCsrfField() ?>
                <input type="hidden" name="volver_estado" value="<?= htmlspecialchars($estado) ?>">
                <input type="hidden" name="volver_page" value="<?= $page ?>">
                <select name="product_id" class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-blue-500 outline-none transition">
                    <option value=""><?= $sinAsignar ? 'Asignar al curso…' : 'Cambiar de curso…' ?></option>
                    <?php foreach ($cursos as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= (int) $c['id'] === (int) $d['product_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?><?= $c['course_code'] ? ' (' . htmlspecialchars($c['course_code']) . ')' : '' ?>
                        <?= (int) $c['requisitos'] > 0 ? ' — ya tiene ' . (int) $c['requisitos'] : '' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2 rounded-lg transition whitespace-nowrap">
                    <i class="fas fa-check mr-1"></i> Asignar y publicar
                </button>
            </form>

            <?php if (!$sinAsignar): ?>
            <form method="POST" action="/manager/requisitos/<?= (int) $d['id'] ?>/publicar">
                <?= \Core\Security::getCsrfField() ?>
                <input type="hidden" name="volver_estado" value="<?= htmlspecialchars($estado) ?>">
                <input type="hidden" name="volver_page" value="<?= $page ?>">
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
                <input type="hidden" name="volver_page" value="<?= $page ?>">
                <button type="submit" class="w-full lg:w-auto px-4 py-2 rounded-lg border border-gray-200 text-gray-400 text-sm font-bold hover:border-rose-200 hover:text-rose-600 transition whitespace-nowrap">
                    Descartar
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Paginación -->
<?php if ($lastPage > 1): ?>
<div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
    <p class="text-xs text-gray-400 font-medium">
        Página <strong class="text-slate-600"><?= $page ?></strong> de <strong class="text-slate-600"><?= $lastPage ?></strong>
        · <?= $total ?> documento<?= $total === 1 ? '' : 's' ?>
    </p>
    <div class="flex items-center gap-1">
        <?php for ($p = max(1, $page - 2); $p <= min($lastPage, $page + 2); $p++): ?>
        <a href="/manager/requisitos?estado=<?= $estado ?>&page=<?= $p ?>"
           class="px-3 py-1.5 rounded-lg text-xs font-bold transition <?= $p === $page
                ? 'bg-blue-600 text-white'
                : 'border border-gray-200 text-slate-500 hover:bg-slate-50' ?>"><?= $p ?></a>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>
