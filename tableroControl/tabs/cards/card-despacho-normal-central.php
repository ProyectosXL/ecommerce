<div class="col-md-6 col-lg-3" data-card-id="despachoNormal">
    <div class="card dashboard-card card-pending-dispatch">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('operaciones-central', 'despachoNormal')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-box-open card-icon"></i>
            <h5 class="card-title">
                Pedidos Pend. de Despacho Envío Normal Central
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Pedidos pendientes de despacho en Depósito Central. Para el día actual se consideran los que ingresan antes de las 14 hs."></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosPendienteDespacho && !empty($pedidosPendienteDespacho->CANT_PED_PEND)): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosPendienteDespacho->CANT_PED_PEND); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($pedidosPendienteDespacho->TOTAL_PEDIDOS, 2); ?></p>
                <p class="date-info">Desde: <?php echo $pedidosPendienteDespacho->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                <button type="button" class="btn btn-outline-cyan w-100" data-bs-toggle="modal" data-bs-target="#modalPendingDispatch">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin pedidos pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>
