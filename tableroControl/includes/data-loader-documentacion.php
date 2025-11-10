<?php
// includes/data-loader-documentacion.php
// Carga solo los datos de la pestaña Documentación

header('Content-Type: application/json');
require_once '../../Class/Control.php';

try {
    $control = new Control();
    
    // Cargar datos de Documentación
    $ncPromociones = $control->traerNcPendPromociones();
    $ncDevoluciones = $control->traerNcPendDevoluciones();
    $pedidosSinFacturar = $control->traerPedidosSinFactTiendas();
    $facturasSinRemito = $control->traerFacturasSinRemito();
    
    // Calcular total pendientes
    $totalPendientes = 0;
    if ($pedidosSinFacturar && !empty($pedidosSinFacturar->CANT_PED_SIN_FACT)) {
        $totalPendientes += $pedidosSinFacturar->CANT_PED_SIN_FACT;
    }
    if ($facturasSinRemito && !empty($facturasSinRemito->CANT_FACTURAS)) {
        $totalPendientes += $facturasSinRemito->CANT_FACTURAS;
    }
    if ($ncPromociones && !empty($ncPromociones->CANT_NC_PROMO)) {
        $totalPendientes += $ncPromociones->CANT_NC_PROMO;
    }
    if ($ncDevoluciones && !empty($ncDevoluciones->CANT_NC_DEV)) {
        $totalPendientes += $ncDevoluciones->CANT_NC_DEV;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'ncPromociones' => $ncPromociones,
            'ncDevoluciones' => $ncDevoluciones,
            'pedidosSinFacturar' => $pedidosSinFacturar,
            'facturasSinRemito' => $facturasSinRemito
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
