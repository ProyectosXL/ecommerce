
<?php
// includes/data-loader.php
// Este archivo se encarga de cargar todos los datos necesarios para el dashboard

// Inicializar variables
$ncPromociones = null;
$ncDevoluciones = null;
$ordenesSinIntegrar = null;
$remitosSinIntegrar = null;
$pedidosSinFacturar = null;
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
$error = null;

try {
    $control = new Control();
    
    // Cargar todos los datos
    $ncPromociones = $control->traerNcPendPromociones();
    $ncDevoluciones = $control->traerNcPendDevoluciones();
    $ordenesSinIntegrar = $control->traerOrdenesSinIntegrar();
    $remitosSinIntegrar = $control->traerRemitosSinIntegrar();
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
    $pedidosPendientesPreparar = $control->traerPedidosPendientesPreparar();
    
    // Cargar las nuevas consultas separadas para control
    $pedidosPendientesControlCentral = $control->traerResumenPedidosPendientesControlCentral();
    $pedidosPendientesControlSucursales = $control->traerResumenPedidosPendientesControlSucursales();
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Calcular totales para badges
$totalPendientesDocumentacion = 0;
$totalPendientesIntegraciones = 0;
$totalPendientesOperacionesCentral = 0;
$totalPendientesOperacionesSucursales = 0;

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
// Agregar Pedidos Pendientes Control Sucursales - Con filtro de 7 días
$pedidosPendientesControlSucursales_detalle = $control->traerDetallePedidosPendientesControlSucursales();
$fechaLimite7Dias = new DateTime();
$fechaLimite7Dias->modify('-7 days');

$contadorSucursales7Dias = 0;
$fechaMasAntiguaSucursales = null;

if (!empty($pedidosPendientesControlSucursales_detalle)):
    foreach ($pedidosPendientesControlSucursales_detalle as $detalle):
        if ($detalle->FECHA_SINCRONIZADO >= $fechaLimite7Dias):
            $contadorSucursales7Dias++;
            if ($fechaMasAntiguaSucursales === null || $detalle->FECHA_SINCRONIZADO < $fechaMasAntiguaSucursales):
                $fechaMasAntiguaSucursales = $detalle->FECHA_SINCRONIZADO;
            endif;
        endif;
    endforeach;
endif;

// Actualizar variable con datos filtrados de 7 días
$pedidosPendientesControlSucursales = (object) [
    'CANTIDAD_PEDIDOS' => $contadorSucursales7Dias,
    'FECHA_MAS_ANTIGUA' => $fechaMasAntiguaSucursales
];

$totalPendientesOperacionesSucursales += $contadorSucursales7Dias;

?>