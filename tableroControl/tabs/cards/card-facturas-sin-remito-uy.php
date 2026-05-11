<div class="col-md-6 col-lg-3" data-card-id="facturasSinRemitoUy">
    <div class="card dashboard-card card-facturas">
        <div class="card-header position-relative">
            <button type="button" class="btn-card-refresh" onclick="refreshCard('uruguay', 'facturasSinRemitoUy')" title="Actualizar"><i class="fas fa-sync-alt"></i></button>
            <i class="fas fa-file-invoice-dollar card-icon"></i>
            <h5 class="card-title">
                Facturas de Tiendas sin Remito
                <i class="fas fa-info-circle info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Facturas de tiendas de Uruguay que aún no tienen remito generado de los últimos 30 días"></i>
            </h5>
        </div>
        <div class="card-body">
            <?php if ($pedidosSinRemitoUruguay !== null && $pedidosSinRemitoUruguay->CANT_FACTURAS !== null && $pedidosSinRemitoUruguay->CANT_FACTURAS > 0): ?>
                <p class="card-value"><?php echo htmlspecialchars($pedidosSinRemitoUruguay->CANT_FACTURAS); ?></p>
                <p class="mb-0">Importe: $<?php echo number_format($pedidosSinRemitoUruguay->IMPORTE, 2); ?></p>
                <?php if ($pedidosSinRemitoUruguay->FECHA_FACTURA): ?>
                    <p class="date-info">Desde: <?php echo $pedidosSinRemitoUruguay->FECHA_FACTURA->format('d/m/Y'); ?></p>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#modalFacturasSinRemitoUruguay">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                </button>
            <?php else: ?>
                <p class="no-data">Sin facturas pendientes</p>
            <?php endif; ?>
        </div>
    </div>
</div>
