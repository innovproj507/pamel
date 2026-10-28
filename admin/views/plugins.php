<?php
/** Plugins cargados por el CMS. */
$plugins = $plugins ?? [];
$total   = count($plugins);

// Los que tienen pantalla de ajustes propia, para ofrecer el atajo.
$ajustes = [
    'paypal'      => '/manager/paypal-settings',
    'seo'         => '/manager/seo',
    'maintenance' => '/manager/maintenance',
];
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Plugins</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> plugin<?= $total === 1 ? '' : 's' ?> cargado<?= $total === 1 ? '' : 's' ?> por el CMS
        </p>
    </div>
</div>

<div class="card overflow-hidden">

    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Plugin</th>
                    <th>Estado</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($plugins)): ?>
                <tr>
                    <td colspan="3" class="vacio">
                        <i data-lucide="puzzle" class="ic"></i>
                        <p>No hay ningún plugin cargado.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($plugins as $plugin):
                    $nombre  = $plugin['name'];
                    $inicial = mb_strtoupper(mb_substr($nombre, 0, 1, 'UTF-8'), 'UTF-8');
                ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar-ini av-violeta"><?= htmlspecialchars($inicial) ?></div>
                            <div class="min-w-0">
                                <div class="font-bold text-[12.5px] text-slate-800"><?= htmlspecialchars(ucfirst($nombre)) ?></div>
                                <div class="mt-1">
                                    <span class="cod"><?= htmlspecialchars($plugin['path']) ?></span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="pastilla p-verde"><span class="punto"></span>Activo</span>
                    </td>
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <?php if (isset($ajustes[$nombre])): ?>
                            <a href="<?= $ajustes[$nombre] ?>" class="acc acc-azul" title="Abrir los ajustes del plugin">
                                <i data-lucide="settings" class="ic" style="width:12px;height:12px"></i> Configurar
                            </a>
                            <?php else: ?>
                            <span class="text-slate-300 text-[10.5px] font-semibold">Sin ajustes</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Cómo añadir uno nuevo -->
<div class="card card-pad mt-4">
    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[.09em] mb-3">Cómo crear un plugin</h3>
    <ol class="space-y-2 text-[12.5px] text-slate-600">
        <li class="flex items-start gap-2.5">
            <span class="cod flex-shrink-0 mt-0.5">1</span>
            <span>Crea la carpeta <code class="cod">plugins/nombre-del-plugin/</code>.</span>
        </li>
        <li class="flex items-start gap-2.5">
            <span class="cod flex-shrink-0 mt-0.5">2</span>
            <span>Dentro, el archivo principal <code class="cod">nombre-del-plugin.php</code>.</span>
        </li>
        <li class="flex items-start gap-2.5">
            <span class="cod flex-shrink-0 mt-0.5">3</span>
            <span>Añade su nombre al array <code class="cod">plugins</code> de <code class="cod">config/config.php</code>.</span>
        </li>
        <li class="flex items-start gap-2.5">
            <span class="cod flex-shrink-0 mt-0.5">4</span>
            <span>Engánchalo con <code class="cod">$pm-&gt;addAction('nombre_del_hook', 'callback')</code>.</span>
        </li>
    </ol>
</div>
