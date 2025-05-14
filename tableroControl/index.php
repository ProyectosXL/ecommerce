<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero de Control Ecommerce</title>
    <?php 
        require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/assets/css/css.php';
    ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css" class="rel css">
    
    <style>
        /* Estilos para las cards */
        .dashboard-card {
            height: 400px; /* Altura fija para todas las cards */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
            border: none;
        }
        
        
    </style>
</head>

<body>
    <?php
    require_once '../Class/Control.php';

    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Inicializar variables
    $ncPromociones = null;
    $ncDevoluciones = null;
    $ordenesSinIntegrar = null;
    $pedidosSinFacturar = null;
    $pedidosFlexCentral = null;
    $facturasSinRemito = null;
    $productosMlFull = null;
    $pedidosDespachados = null;
    $ultimaActualizacion = new DateTime();
    $error = null;
    
    try {
        $control = new Control();
        $ncPromociones = $control->traerNcPendPromociones();
        $ncDevoluciones = $control->traerNcPendDevoluciones();
        $ordenesSinIntegrar = $control->traerOrdenesSinIntegrar();
        $pedidosSinFacturar = $control->traerPedidosSinFactTiendas();
        $pedidosFlexCentral = $control->traerPedidosFlex();
        $facturasSinRemito = $control->traerFacturasSinRemito();
        $ordenesPendientesCierre = $control->traerOrdenesPendientesCierre();
        $pedidosPendienteDespacho = $control->traerPedidosPendienteDespacho();
        $productosMlFull = $control->traerResumenProductosMlFull();
        $pedidosDespachados = $control->traerPedidosDespachados();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }

    // Calcular totales para badges
    $totalPendientesDocumentacion = 0;
    $totalPendientesIntegraciones = 0;
    $totalPendientesOperaciones = 0;

    // Documentación
    if ($pedidosSinFacturar && !empty($pedidosSinFacturar->CANT_PED_SIN_FACT)) {
        $totalPendientesDocumentacion += $pedidosSinFacturar->CANT_PED_SIN_FACT;
    }
    if ($facturasSinRemito && !empty($facturasSinRemito->CANT_FACTURAS)) {
        $totalPendientesDocumentacion += $facturasSinRemito->CANT_FACTURAS;
    }
    if ($ncPromociones && !empty($ncPromociones->CANT_NC_PROMO)) {
        $totalPendientesDocumentacion += $ncPromociones->CANT_NC_PROMO;
    }
    if ($ncDevoluciones && !empty($ncDevoluciones->CANT_NC_DEV)) {
        $totalPendientesDocumentacion += $ncDevoluciones->CANT_NC_DEV;
    }

    // Integraciones
    if ($ordenesSinIntegrar && !empty($ordenesSinIntegrar->CANT_ORDENES)) {
        $totalPendientesIntegraciones += $ordenesSinIntegrar->CANT_ORDENES;
    }
    if ($productosMlFull && !empty($productosMlFull->CANTIDAD_PRODUCTOS)) {
        $totalPendientesIntegraciones += $productosMlFull->CANTIDAD_PRODUCTOS;
    }

    // Operaciones
    if ($pedidosFlexCentral && !empty($pedidosFlexCentral->CANT_PED_PEND)) {
        $totalPendientesOperaciones += $pedidosFlexCentral->CANT_PED_PEND;
    }
    if ($pedidosPendienteDespacho && !empty($pedidosPendienteDespacho->CANT_PED_PEND)) {
        $totalPendientesOperaciones += $pedidosPendienteDespacho->CANT_PED_PEND;
    }
    if ($ordenesPendientesCierre && !empty($ordenesPendientesCierre->CANT_ORDENES)) {
        $totalPendientesOperaciones += $ordenesPendientesCierre->CANT_ORDENES;
    }
    if ($pedidosDespachados && !empty($pedidosDespachados->CANT_PED_PEND)) {
        $totalPendientesOperaciones += $pedidosDespachados->CANT_PED_PEND;
    }
    ?>

    <div class="container py-4">
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                Error: <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <div class="alert alert-info p-2">
            <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt me-2"></i>Actualizar
                </button>
                
                <h2 class="mb-0 text-center flex-grow-1">
                    <i class="fas fa-chart-line"></i> Tablero de Control Ecommerce
                </h2>
                
                <small class="text-muted">
                    <i class="fas fa-clock"></i> 
                    Última actualización: <?php echo $ultimaActualizacion->format('d/m/Y H:i:s'); ?>
                </small>
            </div>
        </div>

        <!-- Navegación por pestañas -->
        <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="documentacion-tab" data-bs-toggle="tab" data-bs-target="#documentacion" type="button" role="tab" aria-controls="documentacion" aria-selected="true">
                    <i class="fas fa-file-alt me-2"></i>Documentación
                    <?php if ($totalPendientesDocumentacion > 0): ?>
                        <span class="badge bg-danger"><?php echo $totalPendientesDocumentacion; ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="integraciones-tab" data-bs-toggle="tab" data-bs-target="#integraciones" type="button" role="tab" aria-controls="integraciones" aria-selected="false">
                    <i class="fas fa-link me-2"></i>Integraciones
                    <?php if ($totalPendientesIntegraciones > 0): ?>
                        <span class="badge bg-danger"><?php echo $totalPendientesIntegraciones; ?></span>
                    <?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="operaciones-tab" data-bs-toggle="tab" data-bs-target="#operaciones" type="button" role="tab" aria-controls="operaciones" aria-selected="false">
                    <i class="fas fa-cogs me-2"></i>Operaciones
                    <?php if ($totalPendientesOperaciones > 0): ?>
                        <span class="badge bg-danger"><?php echo $totalPendientesOperaciones; ?></span>
                    <?php endif; ?>
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="dashboardTabsContent">
            <!-- Pestaña de Documentación -->
            <div class="tab-pane fade show active" id="documentacion" role="tabpanel" aria-labelledby="documentacion-tab">
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
                                    title="Notas de crédito pendientes por devoluciones de productos de los últimos 270 días">
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
            </div>
            
            <!-- Pestaña de Integraciones -->
            <div class="tab-pane fade" id="integraciones" role="tabpanel" aria-labelledby="integraciones-tab">
                <div class="row">
                    <!-- Card de Órdenes sin Integrar -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card dashboard-card card-ordenes">
                            <div class="card-header">
                                <i class="fas fa-exclamation-triangle card-icon"></i>
                                <h5 class="card-title">
                                    Órdenes sin Integrar en Tango
                                    <i class="fas fa-info-circle info-icon" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="Órdenes pendientes de integración al sistema tango">
                                    </i>
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
                    
                    <!-- Card para Productos ML Full -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card dashboard-card card-ml">
                            <div class="card-header">
                                <i class="fas fa-shopping-bag card-icon"></i>
                                <h5 class="card-title">
                                    Productos Pausados en ML Normal
                                    <i class="fas fa-info-circle info-icon" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="Productos que tienen stock en Central pero están pausados en Mercado Libre">
                                    </i>
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($productosMlFull && !empty($productosMlFull->CANTIDAD_PRODUCTOS)): ?>
                                    <p class="card-value"><?php echo htmlspecialchars($productosMlFull->CANTIDAD_PRODUCTOS); ?></p>
                                    <p class="mb-0">Stock total: <?php echo number_format($productosMlFull->PROMEDIO_STOCK, 0); ?> unidades</p>
                                    <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#modalProductosMlFull">
                                        <i class="fas fa-list-ul me-2"></i>Ver Detalle
                                    </button>
                                <?php else: ?>
                                    <p class="no-data">Sin productos pausados</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pestaña de Operaciones -->
            <div class="tab-pane fade" id="operaciones" role="tabpanel" aria-labelledby="operaciones-tab">
                <div class="row">
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
                                    Pedidos  de Tiendas Pend. De Marcar Recibido 
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
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/chart-config.js"></script>
    <script src="js/export-functions.js"></script>

    <script>
        // Definir chartData como variable global para que esté disponible en chart-config.js
        const chartData = {
            labels: [<?php 
                $ncrData = $control->traerNcrRealizadas();
                if (!empty($ncrData)) {
                    echo implode(',', array_map(function($row) {
                        return "'" . $row->FECHA_EMIS->format('d/m/Y') . "'";
                    }, $ncrData));
                }
            ?>],
            values: [<?php 
                if (!empty($ncrData)) {
                    echo implode(',', array_map(function($row) {
                        return $row->CANT_NCR;
                    }, $ncrData));
                }
            ?>]
        };
    </script>

    <?php require_once 'modals/modals.php'; ?>

</body>
</html>