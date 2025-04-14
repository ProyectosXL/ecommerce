

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
    <link rel="stylesheet" href="style/tableroControl.css" class="rel css">
    
</head>
<body>
    <?php
    require_once 'Class/Control.php';

    date_default_timezone_set('America/Argentina/Buenos_Aires');

    // Inicializar variables
    $ncPromociones = null;
    $ncDevoluciones = null;
    $ordenesSinIntegrar = null;
    $pedidosSinFacturar = null;
    $pedidosFlexCentral = null;
    $facturasSinRemito = null;
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
    } catch (Exception $e) {
        $error = $e->getMessage();
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

        <div class="row">
            <!-- NC Pendientes Promociones -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 card-promociones">
                    <div class="card-header">
                        <i class="fas fa-tags card-icon"></i>
                        <h5 class="card-title mt-2">
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
                            <button type="button" class="btn btn-outline-primary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#modalNcPromocionesDetalle">
                                <i class="fas fa-list-ul me-2"></i>Ver Detalle
                            </button>
                        <?php else: ?>
                            <p class="no-data">Sin NC pendientes</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Modal para el detalle de NC Promociones -->
            <div class="modal fade" id="modalNcPromocionesDetalle" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title">
                                <i class="fas fa-tags"></i> Detalle de NC Pendientes por Promociones
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelNcPromociones()">
                                    <i class="fas fa-file-excel me-2"></i>Exportar
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Cód. Promoción</th>
                                            <th>Descripción</th>
                                            <th>% Reintegro</th>
                                            <th>Cód. Artículo</th>
                                            <th class="text-end">Importe NC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleNcPromociones = $control->traerDetalleNcPendPromociones();
                                        if (!empty($detalleNcPromociones)):
                                            foreach ($detalleNcPromociones as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA->format('d/m/Y'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->COD_PROMOCION_TARJETA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->DESC_PROMOCION_TARJETA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->PORC_REINTEGRO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->COD_ARTICU); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->NC, 2); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No hay datos para mostrar</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 card-devoluciones">
                    <div class="card-header">
                        <i class="fas fa-undo card-icon"></i>
                        <h5 class="card-title mt-2">
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
                            <div class="d-grid gap-2 mt-3">
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

            <!-- Modal para el gráfico -->
            <div class="modal fade" id="modalNcr" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-chart-bar"></i> Notas de Crédito por Día
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <canvas id="ncrChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para ver el detalle -->
            <div class="modal fade" id="modalNcDevolucionesDetalle" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title">
                                <i class="fas fa-undo"></i> Detalle de NC Pendientes por Devoluciones
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelNcDevoluciones()">
                                    <i class="fas fa-file-excel me-2"></i>Exportar
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Nro. Pedido</th>
                                            <th>Order ID</th>
                                            <th>Cliente</th>
                                            <th>Deposito</th>
                                            <th>Comprobante</th>
                                            <th class="text-end">Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleNcDevoluciones = $control->traerDetalleNcPendDevoluciones();
                                        if (!empty($detalleNcDevoluciones)):
                                            foreach ($detalleNcDevoluciones as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_PEDI->format('d/m/Y'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->COD_SUCURS); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->N_COMP); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->IMPORTE, 2); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No hay datos para mostrar</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Órdenes sin Integrar -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 card-ordenes">
                    <div class="card-header">
                        <i class="fas fa-exclamation-triangle card-icon"></i>
                        <h5 class="card-title mt-2">
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
                            <button type="button" class="btn btn-outline-warning mt-3 w-100" data-bs-toggle="modal" data-bs-target="#modalOrdenesSinIntegrar">
                                <i class="fas fa-list-ul me-2"></i>Ver Detalle
                            </button>
                        <?php else: ?>
                            <p class="no-data">Sin órdenes pendientes</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Modal para el detalle -->
            <div class="modal fade" id="modalOrdenesSinIntegrar" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header d-flex justify-content-between align-items-center">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle"></i> Detalle de Órdenes sin Integrar
                            </h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" onclick="exportToExcelOrdenes()">
                                    <i class="fas fa-file-excel me-2"></i>Exportar a Excel
                                </button>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha Orden</th>
                                            <th>Tienda</th>
                                            <th>Nro. Orden</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleOrdenes = $control->traerDetalleOrdenesSinIntegrar();
                                        if (!empty($detalleOrdenes)):
                                            foreach ($detalleOrdenes as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_ORDEN->format('d/m/Y H:i'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_NRO_TIENDA); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->TOTAL_ORDEN, 2); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No hay datos para mostrar</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pedidos sin Facturar -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 card-pedidos">
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
        </div>

        <!-- Segunda fila para la card de Flex -->
        <div class="row mt-3">
            <!-- Card de Flex -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 card-pedidos">
                    <div class="card-header">
                    <i class="fas fa-truck-fast card-icon"></i>
                    <h5 class="card-title mt-2">
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
                            <!-- Botón solo se muestra si hay pendientes -->
                            <button type="button" class="btn btn-outline-primary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#modalFlexDetalle">
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
                <div class="card h-100 card-pending-dispatch">
                    <div class="card-header">
                        <i class="fas fa-box-open card-icon"></i>
                        <h5 class="card-title mt-2">
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
                            <button type="button" class="btn btn-outline-cyan mt-3 w-100" data-bs-toggle="modal" data-bs-target="#modalPendingDispatch">
                                <i class="fas fa-list-ul me-2"></i>Ver Detalle
                            </button>
                        <?php else: ?>
                            <p class="no-data">Sin pedidos pendientes</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        
            <!-- Modal para el detalle de Pedidos Flex -->
            <div class="modal fade" id="modalFlexDetalle" tabindex="-1" aria-labelledby="modalFlexDetalleLabel">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title" id="modalFlexDetalleLabel">
                            <i class="fas fa-truck-fast"></i> Detalle de Pedidos Flex Pendientes
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success" onclick="exportToExcel()">
                                <i class="fas fa-file-excel me-2"></i>Exportar
                            </button>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                        <div class="modal-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha Sincronizado</th>
                                            <th>Canal</th>
                                            <th>Nro. Pedido</th>
                                            <th>Order ID</th>
                                            <th>Cliente</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $detalleFlex = $control->traerDetallePedidosFlex();
                                        if (!empty($detalleFlex)):
                                            foreach ($detalleFlex as $detalle): ?>
                                                <tr>
                                                    <td><?php echo $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i'); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                                    <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                                    <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 2); ?></td>
                                                </tr>
                                            <?php endforeach;
                                        else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No hay pedidos pendientes</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card de Facturas sin remito -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 card-facturas">
                    <div class="card-header">
                        <i class="fas fa-file-invoice-dollar card-icon"></i>
                        <h5 class="card-title mt-2">
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
                            <!-- Botón solo se muestra si hay pendientes -->
                            <button type="button" class="btn btn-outline-warning mt-3 w-100" data-bs-toggle="modal" data-bs-target="#modalFacturasDetalle">
                                <i class="fas fa-list-ul me-2"></i>Ver Detalle
                            </button>
                        <?php else: ?>
                            <p class="no-data">Sin facturas pendientes</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        
            <!-- Card para Ordenes Pendientes de Cierre -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 card-pending-close">
                    <div class="card-header">
                        <i class="fas fa-hourglass-half card-icon"></i>
                        <h5 class="card-title mt-2">
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
                            <button type="button" class="btn btn-outline-purple mt-3 w-100" data-bs-toggle="modal" data-bs-target="#modalOrdenesPendientesCierre">
                                <i class="fas fa-list-ul me-2"></i>Ver Detalle
                            </button>
                        <?php else: ?>
                            <p class="no-data">Sin órdenes pendientes</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div> <!-- Cierre de segunda fila de cards -->    
    </div>

    <!-- Modal para el detalle de facturas -->
    <div class="modal fade" id="modalFacturasDetalle" tabindex="-1" aria-labelledby="modalFacturasDetalleLabel">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title" id="modalFacturasDetalleLabel">
                        <i class="fas fa-file-invoice-dollar"></i> Detalle de Facturas sin Remito
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="exportToExcelFacturas()">
                            <i class="fas fa-file-excel me-2"></i>Exportar
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Sucursal</th>
                                    <th>Fecha</th>
                                    <th>Factura</th>
                                    <th>Código</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $detalleFacturas = $control->traerDetalleFacturasSinRemito();
                                if (!empty($detalleFacturas)):
                                    foreach ($detalleFacturas as $detalle): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                                            <td><?php echo $detalle->FECHA_FACTURA->format('d/m/Y'); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->FACTURA); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->COD_ARTICU); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->DESC_CTA_ARTICULO); ?></td>
                                            <td class="text-end"><?php echo number_format($detalle->CANTIDAD, 0); ?></td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No hay datos para mostrar</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para el detalle de Ordenes Pend. Cierre -->
    <div class="modal fade" id="modalOrdenesPendientesCierre" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title">
                        <i class="fas fa-hourglass-half"></i> Detalle de Órdenes Pendientes de Cierre
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="exportToExcelOrdenesCierre()">
                            <i class="fas fa-file-excel me-2"></i>Exportar a Excel
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Order ID</th>
                                    <th>Cliente</th>
                                    <th>Sucursal</th>
                                    <th class="text-end">Días de Antigüedad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $detalleOrdenesCierre = $control->traerDetalleOrdenesPendientesCierre();
                                if (!empty($detalleOrdenesCierre)):
                                    foreach ($detalleOrdenesCierre as $detalle): ?>
                                        <tr>
                                            <td><?php echo $detalle->FECHA->format('d/m/Y H:i'); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->ORDER_ID); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->SUCURSAL); ?></td>
                                            <td class="text-end"><?php echo number_format($detalle->DIAS_ANTIGUEDAD, 0); ?></td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No hay datos para mostrar</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para el detalle -->
    <div class="modal fade" id="modalPendingDispatch" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title">
                        <i class="fas fa-box-open"></i> Detalle de Pedidos Pendientes de Despacho
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" onclick="exportToExcelPendingDispatch()">
                            <i class="fas fa-file-excel me-2"></i>Exportar
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Canal</th>
                                    <th>Suc. Entrega</th>
                                    <th>Fecha Despacho</th>
                                    <th>Nro. Pedido</th>
                                    <th>Order ID</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $detallePedidos = $control->traerDetallePedidosPendienteDespacho();
                                if (!empty($detallePedidos)):
                                    foreach ($detallePedidos as $detalle): ?>
                                        <tr>
                                            <td><?php echo $detalle->FECHA_SINCRONIZADO->format('d/m/Y H:i'); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->CANAL); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->SUCURSAL_ENTREGA); ?></td>
                                            <td><?php echo $detalle->FECHA_DESPACHO ? $detalle->FECHA_DESPACHO->format('d/m/Y') : ''; ?></td>
                                            <td><?php echo htmlspecialchars($detalle->NRO_PEDIDO); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->ORDER_ID_TIENDA); ?></td>
                                            <td><?php echo htmlspecialchars($detalle->CLIENTE); ?></td>
                                            <td class="text-end">$<?php echo number_format($detalle->TOTAL_PEDI, 2); ?></td>
                                        </tr>
                                    <?php endforeach;
                                else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No hay pedidos pendientes</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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

    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Datos para el gráfico
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

            // Inicializar tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Inicializar el gráfico cuando se abre el modal
            const modalNcr = document.getElementById('modalNcr');
            modalNcr.addEventListener('shown.bs.modal', function () {
                const ctx = document.getElementById('ncrChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Cantidad de NC',
                            data: chartData.values,
                            backgroundColor: '#e91e63',
                            borderColor: '#c2185b',
                            borderWidth: 1,
                            barPercentage: 0.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Notas de Crédito realizadas en los últimos 7 días',
                                font: { size: 16 }
                            },
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            });

        });


        function exportToExcel() {
        // Obtener la tabla original
        const table = document.querySelector('#modalFlexDetalle table');
        
        // Crear una copia profunda de la tabla
        const tableClone = table.cloneNode(true);
        
        // Obtener todos los datos
        const rows = tableClone.querySelectorAll('tr');
        
        // Crear el libro y la hoja
        const wb = XLSX.utils.book_new();
        
        // Convertir la tabla a una matriz de datos
        const data = [];
        
        rows.forEach((row) => {
            const rowData = [];
            row.querySelectorAll('th, td').forEach((cell) => {
                let value = cell.textContent.trim();
                
                // Si es una fecha (verificar si tiene el formato dd/mm/yyyy)
                if (value.match(/^\d{2}\/\d{2}\/\d{4}/)) {
                    // Convertir de dd/mm/yyyy HH:mm a formato Excel
                    const [datePart, timePart] = value.split(' ');
                    const [day, month, year] = datePart.split('/');
                    const dateStr = `${year}-${month}-${day}`;
                    if (timePart) {
                        value = `${dateStr} ${timePart}`;
                    } else {
                        value = dateStr;
                    }
                }
                // Si es un valor monetario, remover el símbolo $ y convertir a número
                else if (value.startsWith('$')) {
                    value = parseFloat(value.replace('$', '').replace(/,/g, ''));
                }
                
                rowData.push(value);
            });
            data.push(rowData);
        });
        
        // Crear la hoja con los datos procesados
        const ws = XLSX.utils.aoa_to_sheet(data);
        
        // Agregar la hoja al libro
        XLSX.utils.book_append_sheet(wb, ws, "Pedidos Flex");
        
        // Guardar el archivo
        XLSX.writeFile(wb, `pedidos_flex_${new Date().toISOString().slice(0,10)}.xlsx`);
    }

    function exportToExcelFacturas() {
    // Obtener la tabla original
    const table = document.querySelector('#modalFacturasDetalle table');
    
    // Crear una copia profunda de la tabla
    const tableClone = table.cloneNode(true);
    
    // Obtener todos los datos
    const rows = tableClone.querySelectorAll('tr');
    
    // Crear el libro y la hoja
    const wb = XLSX.utils.book_new();
    
    // Convertir la tabla a una matriz de datos
    const data = [];
    
    rows.forEach((row) => {
        const rowData = [];
        row.querySelectorAll('th, td').forEach((cell) => {
            let value = cell.textContent.trim();
            
            // Si es una fecha (verificar si tiene el formato dd/mm/yyyy)
            if (value.match(/^\d{2}\/\d{2}\/\d{4}/)) {
                // Convertir de dd/mm/yyyy a formato Excel
                const [day, month, year] = value.split('/');
                value = `${year}-${month}-${day}`;
            }
            // Si es un valor numérico con separador de miles, convertir a número
            else if (value.match(/^[\d,]+$/)) {
                value = parseFloat(value.replace(/,/g, ''));
            }
            
            rowData.push(value);
        });
        data.push(rowData);
    });
    
    // Crear la hoja con los datos procesados
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Agregar la hoja al libro
    XLSX.utils.book_append_sheet(wb, ws, "Facturas sin Remito");
    
    // Guardar el archivo
    XLSX.writeFile(wb, `facturas_sin_remito_${new Date().toISOString().slice(0,10)}.xlsx`);
    }

        function exportToExcelNcDevoluciones() {
        const table = document.querySelector('#modalNcDevolucionesDetalle table');
        const tableClone = table.cloneNode(true);
        const rows = tableClone.querySelectorAll('tr');
        const wb = XLSX.utils.book_new();
        const data = [];
        
        rows.forEach((row) => {
            const rowData = [];
            row.querySelectorAll('th, td').forEach((cell) => {
                let value = cell.textContent.trim();
                
                // Si es una fecha (verificar si tiene el formato dd/mm/yyyy)
                if (value.match(/^\d{2}\/\d{2}\/\d{4}/)) {
                    const [day, month, year] = value.split('/');
                    value = `${year}-${month}-${day}`;
                }
                // Si es un valor monetario, remover el símbolo $ y convertir a número
                else if (value.startsWith('$')) {
                    value = parseFloat(value.replace('$', '').replace(/,/g, ''));
                }
                
                rowData.push(value);
            });
            data.push(rowData);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "NC Pendientes Devoluciones");
        XLSX.writeFile(wb, `nc_pendientes_devoluciones_${new Date().toISOString().slice(0,10)}.xlsx`);
    }

        function exportToExcelNcPromociones() {
        const table = document.querySelector('#modalNcPromocionesDetalle table');
        const tableClone = table.cloneNode(true);
        const rows = tableClone.querySelectorAll('tr');
        const wb = XLSX.utils.book_new();
        const data = [];
        
        rows.forEach((row) => {
            const rowData = [];
            row.querySelectorAll('th, td').forEach((cell) => {
                let value = cell.textContent.trim();
                
                // Si es una fecha (verificar si tiene el formato dd/mm/yyyy)
                if (value.match(/^\d{2}\/\d{2}\/\d{4}/)) {
                    const [day, month, year] = value.split('/');
                    value = `${year}-${month}-${day}`;
                }
                // Si es un valor monetario, remover el símbolo $ y convertir a número
                else if (value.startsWith('$')) {
                    value = parseFloat(value.replace('$', '').replace(/,/g, ''));
                }
                // Si es un porcentaje, convertir a número
                else if (value.includes('%')) {
                    value = parseFloat(value.replace('%', ''));
                }
                
                rowData.push(value);
            });
            data.push(rowData);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "NC Pendientes Promociones");
        XLSX.writeFile(wb, `nc_pendientes_promociones_${new Date().toISOString().slice(0,10)}.xlsx`);
    }

    function exportToExcelOrdenes() {
        const table = document.querySelector('#modalOrdenesSinIntegrar table');
        const tableClone = table.cloneNode(true);
        const rows = tableClone.querySelectorAll('tr');
        const wb = XLSX.utils.book_new();
        const data = [];
        
        rows.forEach((row) => {
            const rowData = [];
            row.querySelectorAll('th, td').forEach((cell) => {
                let value = cell.textContent.trim();
                
                if (value.match(/^\d{2}\/\d{2}\/\d{4}/)) {
                    const [day, month, year] = value.split('/');
                    value = `${year}-${month}-${day}`;
                }
                else if (value.startsWith('$')) {
                    value = parseFloat(value.replace('$', '').replace(/,/g, ''));
                }
                
                rowData.push(value);
            });
            data.push(rowData);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Ordenes sin Integrar");
        XLSX.writeFile(wb, `ordenes_sin_integrar_${new Date().toISOString().slice(0,10)}.xlsx`);
    }

        function exportToExcelOrdenesCierre() {
        const table = document.querySelector('#modalOrdenesPendientesCierre table');
        const tableClone = table.cloneNode(true);
        const rows = tableClone.querySelectorAll('tr');
        const wb = XLSX.utils.book_new();
        const data = [];
        
        rows.forEach((row) => {
            const rowData = [];
            row.querySelectorAll('th, td').forEach((cell) => {
                let value = cell.textContent.trim();
                
                // Si es una fecha (verificar si tiene el formato dd/mm/yyyy)
                if (value.match(/^\d{2}\/\d{2}\/\d{4}/)) {
                    const [day, month, year] = value.split('/');
                    value = `${year}-${month}-${day}`;
                }
                // Si es un número, convertir a número
                else if (value.match(/^[\d,.]+$/)) {
                    value = parseFloat(value.replace(/,/g, ''));
                }
                
                rowData.push(value);
            });
            data.push(rowData);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Ordenes Pendientes Cierre");
        XLSX.writeFile(wb, `ordenes_pendientes_cierre_${new Date().toISOString().slice(0,10)}.xlsx`);
    }

        function exportToExcelPendingDispatch() {
        const table = document.querySelector('#modalPendingDispatch table');
        const tableClone = table.cloneNode(true);
        const rows = tableClone.querySelectorAll('tr');
        const wb = XLSX.utils.book_new();
        const data = [];
        
        rows.forEach((row) => {
            const rowData = [];
            row.querySelectorAll('th, td').forEach((cell) => {
                let value = cell.textContent.trim();
                
                if (value.match(/^\d{2}\/\d{2}\/\d{4}/)) {
                    const [day, month, year] = value.split('/');
                    value = `${year}-${month}-${day}`;
                }
                else if (value.startsWith('$')) {
                    value = parseFloat(value.replace('$', '').replace(/,/g, ''));
                }
                
                rowData.push(value);
            });
            data.push(rowData);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Pedidos Pendientes Despacho");
        XLSX.writeFile(wb, `pedidos_pendientes_despacho_${new Date().toISOString().slice(0,10)}.xlsx`);
    }

</script>

</body>
</html>