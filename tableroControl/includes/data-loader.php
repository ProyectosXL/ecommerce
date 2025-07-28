
<?php
// includes/data-loader.php
// Este archivo se encarga de cargar todos los datos necesarios para el dashboard

// Inicializar variables
$ncPromociones = null;
$ncDevoluciones = null;
$ordenesSinIntegrar = null;
$pedidosSinFacturar = null;
$pedidosFlexCentral = null;
$facturasSinRemito = null;
$productosMlFull = null;
$pedidosDespachados = null;
$pedidosPendientesControl = null;
$ordenesPendientesCierre = null;
$pedidosPendienteDespacho = null;
$pedidosRecibidosNoEntregados = null;
$pedidosRetiroTienda = null;
$error = null;

try {
    $control = new Control();
    
    // Cargar todos los datos
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
    $pedidosPendientesControl = $control->traerResumenPedidosPendientesControl();
    $pedidosRecibidosNoEntregados = $control->traerPedidosRecibidosNoEntregados();
    $pedidosRetiroTienda = $control->traerPedidosRetiroTienda();
    
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
if ($pedidosPendientesControl && !empty($pedidosPendientesControl->CANTIDAD_PEDIDOS)) {
    $totalPendientesOperaciones += $pedidosPendientesControl->CANTIDAD_PEDIDOS;
}
if ($pedidosRecibidosNoEntregados && !empty($pedidosRecibidosNoEntregados->CANT_PED_PEND)) {
    $totalPendientesOperaciones += $pedidosRecibidosNoEntregados->CANT_PED_PEND;
}
if ($pedidosRetiroTienda && !empty($pedidosRetiroTienda->CANT_PED_RETIRO)) {
    $totalPendientesOperaciones += $pedidosRetiroTienda->CANT_PED_RETIRO;
}

?>