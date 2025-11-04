<!-- tabs/tab-uruguay.php -->
<?php
// Verificar si mostrar el aviso de nuevo
$fechaLanzamientoUruguay = new DateTime('2025-11-04');
$fechaActual = new DateTime();
$diasDesdeeLanzamiento = $fechaActual->diff($fechaLanzamientoUruguay)->days;
$mostrarNuevoUruguay = $diasDesdeeLanzamiento <= 7;
?>

<?php if ($mostrarNuevoUruguay): ?>
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <div class="d-flex align-items-center">
        <i class="fas fa-sparkles fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading mb-1">
                <i class="fas fa-star"></i> ¡Nueva Pestaña Uruguay Disponible!
            </h5>
            <p class="mb-0">
                Ahora puedes visualizar y gestionar todas las operaciones de Uruguay desde esta nueva sección del tablero.
            </p>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="row">
    <!-- Card para Pedidos sin Facturar - Uruguay -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pedidos">
            <div class="card-header">
                <i class="fas fa-file-invoice card-icon"></i>
                <h5 class="card-title">
                    Pedidos sin Facturar - Tiendas
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos de Uruguay con más de 30 minutos desde su ingreso en tango pendientes de facturación">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosSinFacturarUruguay !== null && $pedidosSinFacturarUruguay->CANT_PED_SIN_FACT !== null && $pedidosSinFacturarUruguay->CANT_PED_SIN_FACT > 0): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosSinFacturarUruguay->CANT_PED_SIN_FACT); ?></p>
                    <p class="mb-0">Total: $<?php echo number_format($pedidosSinFacturarUruguay->TOTAL_PEDIDOS, 2); ?></p>
                    <?php if ($pedidosSinFacturarUruguay->FECHA_PEDI): ?>
                        <p class="date-info">Desde: <?php echo $pedidosSinFacturarUruguay->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosSinFacturarUruguay">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Card para Facturas sin Remito - Uruguay -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-facturas">
            <div class="card-header">
                <i class="fas fa-file-invoice-dollar card-icon"></i>
                <h5 class="card-title">
                    Facturas de Tiendas sin Remito
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Facturas de tiendas de Uruguay que aún no tienen remito generado de los últimos 30 días">
                    </i>
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
    
    <!-- Card de NC Pendientes por Devoluciones - Uruguay -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-devoluciones">
            <div class="card-header">
                <i class="fas fa-undo card-icon"></i>
                <h5 class="card-title">
                    NC Pendientes por Devoluciones
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Notas de crédito pendientes por devoluciones de productos de Uruguay de los últimos 150 días">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($ncDevolucionesUruguay && !empty($ncDevolucionesUruguay->CANT_NC_DEV)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($ncDevolucionesUruguay->CANT_NC_DEV); ?></p>
                    <p class="mb-0">Importe: $<?php echo number_format($ncDevolucionesUruguay->IMPORTE_PEND, 2); ?></p>
                    <p class="date-info">Desde: <?php echo $ncDevolucionesUruguay->FECHA->format('d/m/Y'); ?></p>
                    <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#modalNcDevolucionesUruguay">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin devoluciones pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Card de Órdenes sin Integrar - Uruguay -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-promociones">
            <div class="card-header">
                <i class="fas fa-shopping-cart card-icon"></i>
                <h5 class="card-title">
                    Órdenes sin Integrar en Tango
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Órdenes de Uruguay recibidas que no se integraron en Tango de los últimos 60 días">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($ordenesSinIntegrarUruguay && !empty($ordenesSinIntegrarUruguay->CANT_ORDENES)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($ordenesSinIntegrarUruguay->CANT_ORDENES); ?></p>
                    <p class="mb-0">Total: $<?php echo number_format($ordenesSinIntegrarUruguay->TOTAL_ORDEN, 2); ?></p>
                    <p class="date-info">Desde: <?php echo $ordenesSinIntegrarUruguay->FECHA_ORDEN->format('d/m/Y H:i'); ?></p>
                    <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalOrdenesSinIntegrarUruguay">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin órdenes pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- SEGUNDA FILA -->
<div class="row mt-4">
    <!-- Card para Órdenes de Vtex Pendientes de Cierre - Uruguay -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pending-close">
            <div class="card-header">
                <i class="fas fa-hourglass-half card-icon"></i>
                <h5 class="card-title">
                    Órdenes de Vtex Pendientes de Cierre
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Órdenes de Vtex de Uruguay pendientes de cierre">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($ordenesPendientesCierreUruguay && !empty($ordenesPendientesCierreUruguay->CANT_ORDENES)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($ordenesPendientesCierreUruguay->CANT_ORDENES); ?></p>
                    <p class="mb-0">Promedio Retraso: <?php echo number_format($ordenesPendientesCierreUruguay->PROM_RETRASO, 0); ?> días</p>
                    <p class="date-info">Desde: <?php echo $ordenesPendientesCierreUruguay->FECHA->format('d/m/Y H:i'); ?></p>
                    <button type="button" class="btn btn-outline-purple w-100" data-bs-toggle="modal" data-bs-target="#modalOrdenesPendientesCierreUruguay">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin órdenes pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Card para Pedidos Pendientes de Retiro - Uruguay -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pickup">
            <div class="card-header">
                <i class="fas fa-store-alt card-icon"></i>
                <h5 class="card-title">
                    Pedidos Pendientes de Retiro
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos para retiro en tienda de Uruguay de los últimos 45 días que aún no fueron entregados">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosRetiroTiendaUruguay && !empty($pedidosRetiroTiendaUruguay->CANT_PED_RETIRO)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosRetiroTiendaUruguay->CANT_PED_RETIRO); ?></p>
                    <p class="mb-0">Total: $<?php echo number_format($pedidosRetiroTiendaUruguay->TOTAL_PEDIDOS, 2); ?></p>
                    <p class="date-info">Desde: <?php echo $pedidosRetiroTiendaUruguay->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                    <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosRetiroTiendaUruguay">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Card para Pedidos Pendientes de Control Sucursales - Uruguay -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pending-control">
            <div class="card-header">
                <i class="fas fa-clipboard-check card-icon"></i>
                <h5 class="card-title">
                    Pedidos Pendientes de Control
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos sin controlar de Sucursales de Uruguay de los últimos 7 días hasta ayer">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosPendientesControlSucursalesUruguay && !empty($pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS); ?></p>
                    <?php if ($pedidosPendientesControlSucursalesUruguay->FECHA_MAS_ANTIGUA): ?>
                        <p class="date-info">Desde: <?php echo $pedidosPendientesControlSucursalesUruguay->FECHA_MAS_ANTIGUA->format('d/m/Y H:i'); ?></p>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosPendientesControlSucursalesUruguay">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
