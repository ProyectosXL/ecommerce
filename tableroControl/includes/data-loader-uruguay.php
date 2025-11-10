<?php
// includes/data-loader-uruguay.php
// Carga solo los datos de la pestaña Uruguay

header('Content-Type: application/json');
require_once '../../Class/Control.php';

try {
    $control = new Control();
    
    // Cargar datos de Uruguay
    $pedidosSinFacturarUruguay = $control->traerPedidosSinFactTiendasUruguay();
    $pedidosSinRemitoUruguay = $control->traerFacturasSinRemitoUruguay();
    $ncDevolucionesUruguay = $control->traerNcPendDevolucionesUruguay();
    $ordenesSinIntegrarUruguay = $control->traerOrdenesSinIntegrarUruguay();
    $ordenesPendientesCierreUruguay = $control->traerOrdenesPendientesCierreUruguay();
    $pedidosRetiroTiendaUruguay = $control->traerPedidosRetiroTiendaUruguay();
    $pedidosPendientesControlSucursalesUruguay = $control->traerResumenPedidosPendientesControlSucursalesUruguay();
    
    // Calcular total pendientes
    $totalPendientes = 0;
    if ($pedidosSinFacturarUruguay && !empty($pedidosSinFacturarUruguay->CANT_PED_SIN_FACT)) {
        $totalPendientes += $pedidosSinFacturarUruguay->CANT_PED_SIN_FACT;
    }
    if ($pedidosSinRemitoUruguay && !empty($pedidosSinRemitoUruguay->CANT_FACTURAS)) {
        $totalPendientes += $pedidosSinRemitoUruguay->CANT_FACTURAS;
    }
    if ($ncDevolucionesUruguay && !empty($ncDevolucionesUruguay->CANT_NC_DEV)) {
        $totalPendientes += $ncDevolucionesUruguay->CANT_NC_DEV;
    }
    if ($ordenesSinIntegrarUruguay && !empty($ordenesSinIntegrarUruguay->CANT_ORDENES)) {
        $totalPendientes += $ordenesSinIntegrarUruguay->CANT_ORDENES;
    }
    if ($ordenesPendientesCierreUruguay && !empty($ordenesPendientesCierreUruguay->CANT_ORDENES)) {
        $totalPendientes += $ordenesPendientesCierreUruguay->CANT_ORDENES;
    }
    if ($pedidosRetiroTiendaUruguay && !empty($pedidosRetiroTiendaUruguay->CANT_PED_RETIRO)) {
        $totalPendientes += $pedidosRetiroTiendaUruguay->CANT_PED_RETIRO;
    }
    if ($pedidosPendientesControlSucursalesUruguay && !empty($pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS)) {
        $totalPendientes += $pedidosPendientesControlSucursalesUruguay->CANTIDAD_PEDIDOS;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'pedidosSinFacturarUruguay' => $pedidosSinFacturarUruguay,
            'pedidosSinRemitoUruguay' => $pedidosSinRemitoUruguay,
            'ncDevolucionesUruguay' => $ncDevolucionesUruguay,
            'ordenesSinIntegrarUruguay' => $ordenesSinIntegrarUruguay,
            'ordenesPendientesCierreUruguay' => $ordenesPendientesCierreUruguay,
            'pedidosRetiroTiendaUruguay' => $pedidosRetiroTiendaUruguay,
            'pedidosPendientesControlSucursalesUruguay' => $pedidosPendientesControlSucursalesUruguay
        ],
        'totalPendientes' => $totalPendientes,
        'timestamp' => date('d/m/Y H:i:s')
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
