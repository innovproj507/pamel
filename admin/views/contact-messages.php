<?php
/** Bandeja de mensajes del formulario de contacto. */
$messages = $messages ?? [];
$total    = count($messages);
$nuevos   = count(array_filter($messages, static fn($m) => ($m['status'] ?? '') === 'new'));
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Mensajes de Contacto</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> mensaje<?= $total === 1 ? '' : 's' ?> recibido<?= $total === 1 ? '' : 's' ?>
        </p>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Resumen -->
    <div class="px-4 py-4 grid grid-cols-2 gap-3 border-b" style="border-color: var(--card-border)">
        <div class="kpi">
            <div class="kpi-label">Mensajes</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Sin leer</div>
            <div class="kpi-value"><?= number_format($nuevos) ?></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Fecha</th>
                    <th>Remitente</th>
                    <th>Asunto</th>
                    <th>Estado</th>
                    <th>Mensaje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="6" class="vacio">
                        <i data-lucide="inbox" class="ic"></i>
                        <p>No hay mensajes recibidos.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($messages as $msg):
                    $nuevo   = ($msg['status'] ?? '') === 'new';
                    $mensaje = (string) $msg['message'];
                    $corto   = mb_strimwidth($mensaje, 0, 70, '…', 'UTF-8');
                ?>
                <tr>
                    <td><span class="cod">#<?= (int) $msg['id'] ?></span></td>
                    <td class="text-slate-400 text-[11.5px] whitespace-nowrap">
                        <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?>
                    </td>
                    <td>
                        <div class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:200px">
                            <?= htmlspecialchars($msg['name']) ?>
                        </div>
                        <a href="mailto:<?= htmlspecialchars($msg['email'], ENT_QUOTES) ?>"
                           class="block text-[11px] font-semibold text-slate-400 truncate hover:text-blue-600"
                           style="max-width:200px"><?= htmlspecialchars($msg['email']) ?></a>
                        <?php if (!empty($msg['phone'])): ?>
                        <div class="text-[11px] font-semibold text-slate-400"><?= htmlspecialchars($msg['phone']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="truncate" style="max-width:200px" title="<?= htmlspecialchars($msg['subject'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars($msg['subject']) ?>
                        </div>
                    </td>
                    <td>
                        <span class="pastilla <?= $nuevo ? 'p-verde' : 'p-gris' ?>">
                            <span class="punto"></span><?= $nuevo ? 'Nuevo' : 'Leído' ?>
                        </span>
                    </td>
                    <td>
                        <div class="truncate text-slate-400" style="max-width:300px"
                             title="<?= htmlspecialchars($mensaje, ENT_QUOTES) ?>"><?= htmlspecialchars($corto) ?></div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
