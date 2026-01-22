
<?php
// includes/data-loader.php
// Este archivo se encarga de cargar todos los datos necesarios para el dashboard

// Inicializar variables
$ncPromociones = null;
$ncDevoluciones = null;
$ordenesSinIntegrar = null;
$remitosSinIntegrar = null;
$pedidosSinFacturar = null;
$pedidosSinFacturarUruguay = null;
$pedidosSinRemitoUruguay = null;
$ncDevolucionesUruguay = null;
$ordenesSinIntegrarUruguay = null;
$ordenesPendientesCierreUruguay = null;
$pedidosRetiroTiendaUruguay = null;
$pedidosPendientesControlSucursalesUruguay = null;
$pedidosFlexCentral = null;
$facturasSinRemito = null;
$productosMlFull = null;
$pedidosDespachados = null;
$pedidosPendientesControl = null;
$pedidosPendientesControlCentral = null;
$pedidosPendientesControlSucursales = null;
$pedidosPendientesControlSucursales_7dias = null;
$ordenesPendientesCierre = null;
$pedidosPendienteDespacho = null;
$pedidosRecibidosNoEntregados = null;
$pedidosRetiroTienda = null;
$pedidosPendientesPreparar = null;
$pedidosIncompletosCentral = null;
$pedidosIncompletosSucursales = null;
$error = null;

try {
    $control = new Control();
    
    // Cargar todos los datos
    $ncPromociones = $control->traerNcPendPromociones();
    $ncDevoluciones = $control->traerNcPendDevoluciones();
    $ordenesSinIntegrar = $control->traerOrdenesSinIntegrar();
    $remitosSinIntegrar = $control->traerRemitosSinIntegrar();
    $pedidosSinFacturar = $control->traerPedidosSinFactTiendas();
    $pedidosSinFacturarUruguay = $control->traerPedidosSinFactTiendasUruguay();
    $pedidosSinRemitoUruguay = $control->traerFacturasSinRemitoUruguay();
    $ncDevolucionesUruguay = $control->traerNcPendDevolucionesUruguay();
    $ordenesSinIntegrarUruguay = $control->traerOrdenesSinIntegrarUruguay();
    $ordenesPendientesCierreUruguay = $control->traerOrdenesPendientesCierreUruguay();
    $pedidosRetiroTiendaUruguay = $control->traerPedidosRetiroTiendaUruguay();
    $pedidosPendientesControlSucursalesUruguay = $control->traerResumenPedidosPendientesControlSucursalesUruguay();
    $pedidosFlexCentral = $control->traerPedidosFlex();
    $facturasSinRemito = $control->traerFacturasSinRemito();
    $ordenesPendientesCierre = $control->traerOrdenesPendientesCierre();
    $pedidosPendienteDespacho = $control->traerPedidosPendienteDespacho();
    // $productosMlFull = $control->traerResumenProductosMlFull();  // COMENTADO: Usa linked server que no está disponible
    $productosMlFull = null;
    $pedidosDespachados = $control->traerPedidosDespachados();
    $pedidosPendientesControl = $control->traerResumenPedidosPendientesControl();
    $pedidosRecibidosNoEntregados = $control->traerPedidosRecibidosNoEntregados();
    $pedidosRetiroTienda = $control->traerPedidosRetiroTienda();
    $pedidosPendientesPreparar = $control->traerPedidosPendientesPreparar();
    
    // Cargar las nuevas consultas separadas para control
    $pedidosPendientesControlCentral = $control->traerResumenPedidosPendientesControlCentral();
    $pedidosPendientesControlSucursales = $control->traerResumenPedidosPendientesControlSucursales();
    
    // Cargar pedidos incompletos
    $pedidosIncompletosCentral = $control->traerPedidosIncompletosCentral();
    $pedidosIncompletosSucursales = $control->traerPedidosIncompletosSucursales();
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Calcular totales para badges
$totalPendientesDocumentacion = 0;
$totalPendientesIntegraciones = 0;
$totalPendientesOperacionesCentral = 0;
$totalPendientesOperacionesSucursales = 0;
$totalPendientesUruguay = 0;

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

// Operaciones Central
if ($pedidosPendientesPreparar && !empty($pedidosPendientesPreparar->CANT_PED_PEND)) {
    $totalPendientesOperacionesCentral += $pedidosPendientesPreparar->CANT_PED_PEND;
}
if ($pedidosFlexCentral && !empty($pedidosFlexCentral->CANT_PED_PEND)) {
    $totalPendientesOperacionesCentral += $pedidosFlexCentral->CANT_PED_PEND;
}
if ($pedidosPendienteDespacho && !empty($pedidosPendienteDespacho->CANT_PED_PEND)) {
    $totalPendientesOperacionesCentral += $pedidosPendienteDespacho->CANT_PED_PEND;
}
if ($remitosSinIntegrar !== null && is_array($remitosSinIntegrar) && count($remitosSinIntegrar) > 0) {
    $totalPendientesOperacionesCentral += count($remitosSinIntegrar);
}
// Agregar Pedidos Pendientes Control Central
if ($pedidosPendientesControlCentral && !empty($pedidosPendientesControlCentral->CANTIDAD_PEDIDOS)) {
    $totalPendientesOperacionesCentral += $pedidosPendientesControlCentral->CANTIDAD_PEDIDOS;
}
// Agregar Pedidos Incompletos Central
if ($pedidosIncompletosCentral && !empty($pedidosIncompletosCentral->CANT_PEDIDOS_INCOMPLETOS)) {
    $totalPendientesOperacionesCentral += $pedidosIncompletosCentral->CANT_PEDIDOS_INCOMPLETOS;
}

// Operaciones Sucursales
if ($ordenesPendientesCierre && !empty($ordenesPendientesCierre->CANT_ORDENES)) {
    $totalPendientesOperacionesSucursales += $ordenesPendientesCierre->CANT_ORDENES;
}
if ($pedidosDespachados && !empty($pedidosDespachados->CANT_PED_PEND)) {
    $totalPendientesOperacionesSucursales += $pedidosDespachados->CANT_PED_PEND;
}
if ($pedidosRecibidosNoEntregados && !empty($pedidosRecibidosNoEntregados->CANT_PED_PEND)) {
    $totalPendientesOperacionesSucursales += $pedidosRecibidosNoEntregados->CANT_PED_PEND;
}
if ($pedidosRetiroTienda && !empty($pedidosRetiroTienda->CANT_PED_RETIRO)) {
    $totalPendientesOperacionesSucursales += $pedidosRetiroTienda->CANT_PED_RETIRO;
}
// Agregar Pedidos Pendientes Control Sucursales - Usando el método específico
if ($pedidosPendientesControlSucursales && !empty($pedidosPendientesControlSucursales->CANTIDAD_PEDIDOS)) {
    $totalPendientesOperacionesSucursales += $pedidosPendientesControlSucursales->CANTIDAD_PEDIDOS;
}
// Agregar Pedidos Incompletos Sucursales
if ($pedidosIncompletosSucursales && !empty($pedidosIncompletosSucursales->CANT_PEDIDOS_INCOMPLETOS)) {
    $totalPendientesOperacionesSucursales += $pedidosIncompletosSucursales->CANT_PEDIDOS_INCOMPLETOS;
}

// Uruguay
if ($pedidosSinFacturarUruguay && !empty($pedidosSinFacturarUruguay->CANT_PED_SIN_FACT)) {
    $totalPendientesUruguay += $pedidosSinFacturarUruguay->CANT_PED_SIN_FACT;
}
if ($pedidosSinRemitoUruguay && !empty($pedidosSinRemitoUruguay->CANT_FACTURAS)) {
    $totalPendientesUruguay += $pedidosSinRemitoUruguay->CANT_FACTURAS;
}
if ($ncDevolucionesUruguay && !empty($ncDevolucionesUruguay->CANT_NC_DEV)) {
    $totalPendientesUruguay += $ncDevolucionesUruguay->CANT_NC_DEV;
}
if ($ordenesSinIntegrarUruguay && !empty($ordenesSinIntegrarUruguay->CANT_ORDENES)) {
    $totalPendientesUruguay += $ordenesSinIntegrarUruguay->CANT_ORDENES;
}
if ($ordenesPendientesCierreUruguay && !empty($ordenesPendientesCierreUruguay->CANT_ORDENES)) {
    $totalPendientesUruguay += $ordenesPendientesCierreUruguay->CANT_ORDENES;
}
if ($pedidosRetiroTiendaUruguay && !empty($pedidosRetiroTiendaUruguay->CANT_PED_RETIRO)) {
    $totalPendientesUruguay += $pedidosRetiroTiendaUruguay->CANT_PED_RETIRO;
}
if ($pedidosPendientesControlSucursalesUruguay && !empty($pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS)) {
    $totalPendientesUruguay += $pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS;
}

?>