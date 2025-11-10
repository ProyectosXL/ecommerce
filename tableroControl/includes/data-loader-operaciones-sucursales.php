<?php
// includes/data-loader-operaciones-sucursales.php
// Carga solo los datos de la pestaña Operaciones Sucursales

header('Content-Type: application/json');
require_once '../../Class/Control.php';

try {
    $control = new Control();
    
    // Cargar datos de Operaciones Sucursales
    $ordenesPendientesCierre = $control->traerOrdenesPendientesCierre();
    $pedidosDespachados = $control->traerPedidosDespachados();
    $pedidosRecibidosNoEntregados = $control->traerPedidosRecibidosNoEntregados();
    $pedidosRetiroTienda = $control->traerPedidosRetiroTienda();
    $pedidosPendientesControlSucursales = $control->traerResumenPedidosPendientesControlSucursales();
    
    // Calcular total pendientes
    $totalPendientes = 0;
    if ($ordenesPendientesCierre && !empty($ordenesPendientesCierre->CANT_ORDENES)) {
        $totalPendientes += $ordenesPendientesCierre->CANT_ORDENES;
    }
    if ($pedidosDespachados && !empty($pedidosDespachados->CANT_PED_PEND)) {
        $totalPendientes += $pedidosDespachados->CANT_PED_PEND;
    }
    if ($pedidosRecibidosNoEntregados && !empty($pedidosRecibidosNoEntregados->CANT_PED_PEND)) {
        $totalPendientes += $pedidosRecibidosNoEntregados->CANT_PED_PEND;
    }
    if ($pedidosRetiroTienda && !empty($pedidosRetiroTienda->CANT_PED_RETIRO)) {
        $totalPendientes += $pedidosRetiroTienda->CANT_PED_RETIRO;
    }
    if ($pedidosPendientesControlSucursales && !empty($pedidosPendientesControlSucursales->CANTIDAD_PEDIDOS)) {
        $totalPendientes += $pedidosPendientesControlSucursales->CANTIDAD_PEDIDOS;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'ordenesPendientesCierre' => $ordenesPendientesCierre,
            'pedidosDespachados' => $pedidosDespachados,
            'pedidosRecibidosNoEntregados' => $pedidosRecibidosNoEntregados,
            'pedidosRetiroTienda' => $pedidosRetiroTienda,
            'pedidosPendientesControlSucursales' => $pedidosPendientesControlSucursales
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
