
<!-- tabs/tab-documentacion.php -->
<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="actualizarDocumentacion()" id="btnActualizarDocumentacion">
        <i class="fas fa-sync-alt me-2"></i>Actualizar Documentación
    </button>
</div>
<div class="row">
    <!-- Card de Pedidos sin Facturar -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pedidos">
            <div class="card-header">
                <i class="fas fa-file-invoice card-icon"></i>
                <h5 class="card-title mt-2">
                    Pedidos sin Facturar Tiendas
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos con más de 30 minutos desde su ingreso en tango pendientes de facturación">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosSinFacturar !== null && $pedidosSinFacturar->TOTAL_PEDIDOS !== null): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosSinFacturar->CANT_PED_SIN_FACT); ?></p>
                    <p class="mb-0">Total: $<?php echo number_format($pedidosSinFacturar->TOTAL_PEDIDOS, 2); ?></p>
                    <?php if ($pedidosSinFacturar->FECHA_PEDI): ?>
                        <p class="date-info">Desde: <?php echo $pedidosSinFacturar->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Card de Facturas sin remito -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-facturas">
            <div class="card-header">
                <i class="fas fa-file-invoice-dollar card-icon"></i>
                <h5 class="card-title">
                    Facturas de Tiendas sin Remito
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Facturas pendientes de asociar con remito">
                    </i>
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
    
    <!-- Card de NC Pendientes Promociones -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-promociones">
            <div class="card-header">
                <i class="fas fa-tags card-icon"></i>
                <h5 class="card-title">
                    NC Pendientes por Promociones
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Notas de crédito pendientes por promociones aplicadas">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($ncPromociones && !empty($ncPromociones->CANT_NC_PROMO)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($ncPromociones->CANT_NC_PROMO); ?></p>
                    <p class="mb-0">Importe: $<?php echo number_format($ncPromociones->IMPORTE_NC, 2); ?></p>
                    <p class="date-info">Desde: <?php echo $ncPromociones->FECHA->format('d/m/Y'); ?></p>
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalNcPromocionesDetalle">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin NC pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Card de Pendientes por Devoluciones -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-devoluciones">
            <div class="card-header">
                <i class="fas fa-undo card-icon"></i>
                <h5 class="card-title">
                    NC Pendientes por Devoluciones
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Notas de crédito pendientes por devoluciones de productos de los últimos 2 meses">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($ncDevoluciones && !empty($ncDevoluciones->CANT_NC_DEV)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($ncDevoluciones->CANT_NC_DEV); ?></p>
                    <p class="mb-0">Importe: $<?php echo number_format($ncDevoluciones->IMPORTE_PEND, 2); ?></p>
                    <p class="date-info">Desde: <?php echo $ncDevoluciones->FECHA->format('d/m/Y'); ?></p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalNcr">
                            <i class="fas fa-chart-bar me-2"></i>Ver Estadísticas
                        </button>
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalNcDevolucionesDetalle">
                            <i class="fas fa-list-ul me-2"></i>Ver Detalle
                        </button>
                    </div>
                <?php else: ?>
                    <p class="no-data">Sin devoluciones pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>