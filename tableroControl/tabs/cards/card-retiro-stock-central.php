<div class="col-md-6 col-lg-3" data-card-id="retiroStockCentral">
    <div class="card dashboard-card card-received">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('operaciones-sucursales', 'retiroStockCentral')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-store card-icon"></i>
            <h5 class="card-title">
                Pedidos Pendientes de Retiro - Stock Central
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos recibidos en tienda pero no marcados como entregados al cliente en los últimos 45 días"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosRecibidosNoEntregados && !empty($pedidosRecibidosNoEntregados->CANT_PED_PEND)): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosRecibidosNoEntregados->CANT_PED_PEND); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($pedidosRecibidosNoEntregados->TOTAL_PEDIDOS, 2); ?></p>
                <p class="date-info">Desde: <?php echo $pedidosRecibidosNoEntregados->FECHA_RECIBIDO->format('d/m/Y'); ?></p>
                <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosRecibidosNoEntregados">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>
