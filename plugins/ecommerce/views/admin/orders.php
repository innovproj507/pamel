<?php
/** Listado de pedidos de la tienda. */
$orders = $orders ?? [];
$total  = count($orders);

// Nombre en español y tinte de cada estado de pedido.
$estados = [
    'pending'    => ['Pendiente',  'p-ambar'],
    'processing' => ['En proceso', 'p-azul'],
    'completed'  => ['Completado', 'p-verde'],
    'cancelled'  => ['Cancelado',  'p-rojo'],
];

$facturado  = array_sum(array_column($orders, 'total_amount'));
$pendientes = count(array_filter($orders, static fn($o) => $o['status'] === 'pending'));
$completos  = count(array_filter($orders, static fn($o) => $o['status'] === 'completed'));
?>

<!-- Cabecera -->
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-5">

    <a href="/manager" class="btn-atras" title="Volver al panel">
        <i data-lucide="arrow-left" class="ic ic-17"></i>
    </a>

    <div class="min-w-0">
        <h1 class="text-[26px] font-black text-slate-800 leading-tight">Pedidos</h1>
        <p class="text-[12.5px] font-semibold text-slate-400 mt-0.5">
            <?= number_format($total) ?> pedido<?= $total === 1 ? '' : 's' ?> registrado<?= $total === 1 ? '' : 's' ?>
        </p>
    </div>
</div>

<div class="card overflow-hidden">

    <!-- Resumen -->
    <div class="px-4 py-4 grid grid-cols-2 lg:grid-cols-4 gap-3 border-b" style="border-color: var(--card-border)">
        <div class="kpi">
            <div class="kpi-label">Pedidos</div>
            <div class="kpi-value"><?= number_format($total) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Facturado</div>
            <div class="kpi-value">$<?= number_format($facturado, 2) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Pendientes</div>
            <div class="kpi-value"><?= number_format($pendientes) ?></div>
        </div>
        <div class="kpi">
            <div class="kpi-label">Completados</div>
            <div class="kpi-value"><?= number_format($completos) ?></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="overflow-x-auto">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Cliente</th>
                    <th class="num">Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th class="num">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="6" class="vacio">
                        <i data-lucide="receipt" class="ic"></i>
                        <p>Todavía no hay pedidos.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($orders as $order):
                    [$estadoTexto, $estadoClase] = $estados[$order['status']] ?? [ucfirst($order['status']), 'p-gris'];
                    $cliente = trim($order['customer_name'] ?? '') ?: 'Sin nombre';
                ?>
                <tr>
                    <td><span class="cod">#<?= (int) $order['id'] ?></span></td>
                    <td>
                        <div class="font-bold text-[12.5px] text-slate-800 truncate" style="max-width:240px">
                            <?= htmlspecialchars($cliente) ?>
                        </div>
                        <div class="text-[11px] font-semibold text-slate-400 truncate" style="max-width:240px">
                            <?= htmlspecialchars($order['customer_email'] ?? 'Sin correo') ?>
                        </div>
                    </td>
                    <td class="num font-bold text-slate-700 whitespace-nowrap">
                        $<?= number_format($order['total_amount'], 2) ?>
                    </td>
                    <td>
                        <span class="pastilla <?= $estadoClase ?>"><span class="punto"></span><?= $estadoTexto ?></span>
                    </td>
                    <td class="text-slate-400 text-[11.5px] whitespace-nowrap">
                        <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                    </td>
                    <td class="num">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="/manager/orders/<?= (int) $order['id'] ?>" class="acc acc-azul" title="Ver el pedido">
                                <i data-lucide="eye" class="ic" style="width:12px;height:12px"></i> Ver
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
