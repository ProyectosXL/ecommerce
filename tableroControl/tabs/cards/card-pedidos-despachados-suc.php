<div class="col-md-6 col-lg-3" data-card-id="pedidosDespachados">
    <div class="card dashboard-card card-dispatched">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('operaciones-sucursales', 'pedidosDespachados')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-truck-loading card-icon"></i>
            <h5 class="card-title">
                Pedidos de Tiendas Pend. De Marcar Recibido
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos despachados pendientes de recepción en tienda en los últimos 7 días"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosDespachados && !empty($pedidosDespachados->CANT_PED_PEND)): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosDespachados->CANT_PED_PEND); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($pedidosDespachados->TOTAL_PEDIDOS, 2); ?></p>
                <p class="date-info">Desde: <?php echo $pedidosDespachados->FECHA_DESPACHADO->format('d/m/Y'); ?></p>
                <button type="button" class="btn btn-outline-orange w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosDespachados">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>
