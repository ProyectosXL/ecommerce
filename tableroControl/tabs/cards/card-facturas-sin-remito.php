<div class="col-md-6 col-lg-3" data-card-id="facturasSinRemito">
    <div class="card dashboard-card card-facturas">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('documentacion', 'facturasSinRemito')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-file-invoice-dollar card-icon"></i>
            <h5 class="card-title">
                Facturas de Tiendas sin Remito
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Facturas pendientes de asociar con remito"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($facturasSinRemito !== null && $facturasSinRemito->CANT_FACTURAS !== null && $facturasSinRemito->CANT_FACTURAS > 0): ?>
                <p class="card-value"><?php echo htmlspecialchars($facturasSinRemito->CANT_FACTURAS); ?></p>
                <p class="mb-0">Total: $<?php echo number_format($facturasSinRemito->IMPORTE, 2); ?></p>
                <p class="date-info">Desde: <?php echo $facturasSinRemito->FECHA_FACTURA->format('d/m/Y H:i'); ?></p>
                <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#modalFacturasDetalle">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin facturas pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>
