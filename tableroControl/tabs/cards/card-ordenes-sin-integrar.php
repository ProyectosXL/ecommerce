<div class="col-md-6 col-lg-3" data-card-id="ordenesSinIntegrar">
    <div class="card dashboard-card card-ordenes">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('integraciones', 'ordenesSinIntegrar')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-exclamation-triangle card-icon"></i>
            <h5 class="card-title">
                Órdenes sin Integrar en Tango
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Órdenes pendientes de integración al sistema tango"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($ordenesSinIntegrar && !empty($ordenesSinIntegrar->CANT_ORDENES)): ?>
                <p class="card-value"><?php echo htmlspecialchars($ordenesSinIntegrar->CANT_ORDENES); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($ordenesSinIntegrar->TOTAL_ORDEN, 2); ?></p>
                <p class="date-info">Desde: <?php echo $ordenesSinIntegrar->FECHA_ORDEN->format('d/m/Y H:i'); ?></p>
                <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#modalOrdenesSinIntegrar">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin órdenes pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>
