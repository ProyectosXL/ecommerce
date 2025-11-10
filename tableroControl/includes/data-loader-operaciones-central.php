<?php
// includes/data-loader-operaciones-central.php
// Carga solo los datos de la pestaña Operaciones Central

header('Content-Type: application/json');
require_once '../../Class/Control.php';

try {
    $control = new Control();
    
    // Cargar datos de Operaciones Central
    $pedidosPendientesPreparar = $control->traerPedidosPendientesPreparar();
    $pedidosFlexCentral = $control->traerPedidosFlex();
    $pedidosPendienteDespacho = $control->traerPedidosPendienteDespacho();
    $remitosSinIntegrar = $control->traerRemitosSinIntegrar();
    $pedidosPendientesControlCentral = $control->traerResumenPedidosPendientesControlCentral();
    
    // Calcular total pendientes
    $totalPendientes = 0;
    if ($pedidosPendientesPreparar && !empty($pedidosPendientesPreparar->CANT_PED_PEND)) {
        $totalPendientes += $pedidosPendientesPreparar->CANT_PED_PEND;
    }
    if ($pedidosFlexCentral && !empty($pedidosFlexCentral->CANT_PED_PEND)) {
        $totalPendientes += $pedidosFlexCentral->CANT_PED_PEND;
    }
    if ($pedidosPendienteDespacho && !empty($pedidosPendienteDespacho->CANT_PED_PEND)) {
        $totalPendientes += $pedidosPendienteDespacho->CANT_PED_PEND;
    }
    if ($remitosSinIntegrar !== null && is_array($remitosSinIntegrar) && count($remitosSinIntegrar) > 0) {
        $totalPendientes += count($remitosSinIntegrar);
    }
    if ($pedidosPendientesControlCentral && !empty($pedidosPendientesControlCentral->CANTIDAD_PEDIDOS)) {
        $totalPendientes += $pedidosPendientesControlCentral->CANTIDAD_PEDIDOS;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'pedidosPendientesPreparar' => $pedidosPendientesPreparar,
            'pedidosFlexCentral' => $pedidosFlexCentral,
            'pedidosPendienteDespacho' => $pedidosPendienteDespacho,
            'remitosSinIntegrar' => $remitosSinIntegrar,
            'pedidosPendientesControlCentral' => $pedidosPendientesControlCentral
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
