<!-- tabs/tab-operaciones.php -->
<div class="row">
    <!-- PRIMERA FILA -->
    <!-- Card para Pedidos Pendientes de Preparar -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pending-prepare">
            <div class="card-header">
                <i class="fas fa-clipboard-list card-icon"></i>
                <h5 class="card-title">
                    Pedidos Pend. de Preparar Central
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos pendientes de preparar en Depósito Central en los últimos 7 días">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosPendientesPreparar !== null && $pedidosPendientesPreparar->CANT_PED_PEND !== null && $pedidosPendientesPreparar->CANT_PED_PEND > 0): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosPendientesPreparar->CANT_PED_PEND); ?></p>
                    <p class="mb-0">Total: $<?php echo number_format($pedidosPendientesPreparar->TOTAL_PEDIDOS, 2); ?></p>
                    <?php if ($pedidosPendientesPreparar->FECHA_PEDI): ?>
                        <p class="date-info">Desde: <?php echo $pedidosPendientesPreparar->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalPendientesPreparar">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Card de Flex -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-flex">
            <div class="card-header">
                <i class="fas fa-truck-fast card-icon"></i>
                <h5 class="card-title">
                    Pedidos Pend. de Despacho Envío Flex Central
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos Flex pendientes de despacho en Depósito Central. Para el día actual se consideran los que ingresan antes de las 12 hs.">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosFlexCentral !== null && $pedidosFlexCentral->CANT_PED_PEND !== null && $pedidosFlexCentral->CANT_PED_PEND > 0): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosFlexCentral->CANT_PED_PEND); ?></p>
                    <p class="mb-0">Total: $<?php echo number_format($pedidosFlexCentral->TOTAL_PEDIDOS, 2); ?></p>
                    <?php if ($pedidosFlexCentral->FECHA_PEDI): ?>
                        <p class="date-info">Desde: <?php echo $pedidosFlexCentral->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalFlexDetalle">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Card para Pedidos Pendientes de Despacho -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pending-dispatch">
            <div class="card-header">
                <i class="fas fa-box-open card-icon"></i>
                <h5 class="card-title">
                    Pedidos Pend. de Despacho Envío Normal Central
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos pendientes de despacho en Depósito Central. Para el día actual se consideran los que ingresan antes de las 14 hs.">
                    </i>
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
    
    <!-- Card para Ordenes Pendientes de Cierre -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pending-close">
            <div class="card-header">
                <i class="fas fa-hourglass-half card-icon"></i>
                <h5 class="card-title">
                    Ordenes de Vtex Pendientes de Cierre
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Órdenes de Vtex pendientes de cierre">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($ordenesPendientesCierre && !empty($ordenesPendientesCierre->CANT_ORDENES)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($ordenesPendientesCierre->CANT_ORDENES); ?></p>
                    <p class="mb-0">Promedio Retraso: <?php echo number_format($ordenesPendientesCierre->PROM_RETRASO, 0); ?> días</p>
                    <p class="date-info">Desde: <?php echo $ordenesPendientesCierre->FECHA->format('d/m/Y H:i'); ?></p>
                    <button type="button" class="btn btn-outline-purple w-100" data-bs-toggle="modal" data-bs-target="#modalOrdenesPendientesCierre">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin órdenes pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card para Pedidos Despachados Pendientes de Recepción -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-dispatched">
            <div class="card-header">
                <i class="fas fa-truck-loading card-icon"></i>
                <h5 class="card-title">
                    Pedidos de Tiendas Pend. De Marcar Recibido 
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos despachados pendientes de recepción en tienda en los últimos 7 días">
                    </i>
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
</div>

<!-- SEGUNDA FILA -->
<div class="row mt-4">
    <!-- Pedidos Pendientes de Control - Primera columna segunda fila -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pending-control">
            <div class="card-header">
                <i class="fas fa-clipboard-check card-icon"></i>
                <h5 class="card-title">
                    Pedidos Pendientes de Control
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos sin controlar de los últimos 14 días">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosPendientesControl && !empty($pedidosPendientesControl->CANTIDAD_PEDIDOS)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosPendientesControl->CANTIDAD_PEDIDOS); ?></p>
                    <?php if ($pedidosPendientesControl->FECHA_MAS_ANTIGUA): ?>
                        <p class="date-info">Desde: <?php echo $pedidosPendientesControl->FECHA_MAS_ANTIGUA->format('d/m/Y H:i'); ?></p>
                    <?php endif; ?>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalRankingPedidosControl">
                            <i class="fas fa-trophy me-2"></i>Ver Ranking
                        </button>
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalPedidosPendientesControl">
                            <i class="fas fa-list-ul me-2"></i>Ver Detalle
                        </button>
                    </div>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card para Pedidos Recibidos pero no Entregados - Segunda columna segunda fila -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-received">
            <div class="card-header">
                <i class="fas fa-store card-icon"></i>
                <h5 class="card-title">
                    Pedidos de Retiro en Tienda con Stock de Central
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos recibidos en tienda pero no marcados como entregados al cliente en los últimos 45 días">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosRecibidosNoEntregados && !empty($pedidosRecibidosNoEntregados->CANT_PED_PEND)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosRecibidosNoEntregados->CANT_PED_PEND); ?></p>
                    <p class="mb-0">Total: $<?php echo number_format($pedidosRecibidosNoEntregados->TOTAL_PEDIDOS, 2); ?></p>
                    <p class="date-info">Desde: <?php echo $pedidosRecibidosNoEntregados->FECHA_RECIBIDO->format('d/m/Y'); ?></p>
                    <button type="button" class="btn btn-outline-success w-100" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalPedidosRecibidosNoEntregados">
                    <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Pedidos de Retiro en Tienda - Tercera columna segunda fila -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pickup">
            <div class="card-header">
                <i class="fas fa-store-alt card-icon"></i>
                <h5 class="card-title">
                    Pedidos de Retiro en Tienda con Stock Local
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos para retiro en tienda con stock disponible en la sucursal de los últimos 45 días que aún no fueron entregados">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosRetiroTienda && !empty($pedidosRetiroTienda->CANT_PED_RETIRO)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosRetiroTienda->CANT_PED_RETIRO); ?></p>
                    <p class="mb-0">Total: $<?php echo number_format($pedidosRetiroTienda->TOTAL_PEDIDOS, 2); ?></p>
                    <p class="date-info">Desde: <?php echo $pedidosRetiroTienda->FECHA_PEDI->format('d/m/Y H:i'); ?></p>
                    <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosRetiroTienda">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card para Remitos Sin Ingresar -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pending-remitos">
            <div class="card-header">
                <i class="fas fa-file-invoice card-icon"></i>
                <h5 class="card-title">
                    Remitos Abastecimiento Sin Ingresar
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Remitos de abastecimiento que aún no han sido ingresados al sistema de los últimos 180 días">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($remitosSinIntegrar !== null && is_array($remitosSinIntegrar) && count($remitosSinIntegrar) > 0): ?>
                    <p class="card-value"><?php echo count($remitosSinIntegrar); ?></p>
                    <?php if (isset($remitosSinIntegrar[0]->FECHA_MAS_ANTIGUA)): ?>
                        <br>
                        <p class="date-info">Desde: <?php echo $remitosSinIntegrar[0]->FECHA_MAS_ANTIGUA->format('d/m/Y'); ?></p>
                    <?php endif; ?>
                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalRemitosSinIntegrar">
                            <i class="fas fa-list-ul me-2"></i>Ver Detalle
                        </button>
                    </div>
                <?php else: ?>
                    <p class="no-data">Sin remitos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>