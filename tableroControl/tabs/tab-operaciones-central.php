<!-- tabs/tab-operaciones-central.php -->
<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="actualizarOperacionesCentral()" id="btnActualizarOperacionesCentral">
        <i class="fas fa-sync-alt me-2"></i>Actualizar Operaciones Central
    </button>
</div>
<div class="row">
    <!-- PRIMERA FILA -->
    <!-- Card para Pedidos Pendientes de Preparar Central -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pending-prepare">
            <div class="card-header">
                <i class="fas fa-clipboard-list card-icon"></i>
                <h5 class="card-title">
                    Pedidos Pend. de Preparar Central
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos que aún no han sido asignados para picking en los últimos 7 días">
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
    
    <!-- Card de Flex Central -->
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
    
    <!-- Card para Pedidos Pendientes de Despacho Normal Central -->
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
                    title="Remitos de abastecimiento que aún no han sido ingresados al sistema de los últimos 60 días">
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

<!-- SEGUNDA FILA -->
<div class="row mt-4">
    <!-- Pedidos Pendientes de Control Central - Primera columna segunda fila -->
    <div class="col-md-6 col-lg-3">
        <div class="card dashboard-card card-pending-control">
            <div class="card-header">
                <i class="fas fa-clipboard-check card-icon"></i>
                <h5 class="card-title">
                    Pedidos Pendientes de Control Central
                    <i class="fas fa-info-circle info-icon" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Pedidos sin controlar de Central de los últimos 7 días hasta ayer">
                    </i>
                </h5>
            </div>
            <div class="card-body">
                <?php if ($pedidosPendientesControlCentral && !empty($pedidosPendientesControlCentral->CANTIDAD_PEDIDOS)): ?>
                    <p class="card-value"><?php echo htmlspecialchars($pedidosPendientesControlCentral->CANTIDAD_PEDIDOS); ?></p>
                    <?php if ($pedidosPendientesControlCentral->FECHA_MAS_ANTIGUA): ?>
                        <p class="date-info">Desde: <?php echo $pedidosPendientesControlCentral->FECHA_MAS_ANTIGUA->format('d/m/Y H:i'); ?></p>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalPedidosPendientesControlCentral">
                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                    </button>
                <?php else: ?>
                    <p class="no-data">Sin pedidos pendientes</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>