<?php
// includes/data-loader-integraciones.php
// Carga solo los datos de la pestaña Integraciones

header('Content-Type: application/json');
require_once '../../Class/Control.php';

try {
    $control = new Control();
    
    // Cargar datos de Integraciones
    $ordenesSinIntegrar = $control->traerOrdenesSinIntegrar();
    $productosMlFull = $control->traerResumenProductosMlFull();
    
    // Calcular total pendientes
    $totalPendientes = 0;
    if ($ordenesSinIntegrar && !empty($ordenesSinIntegrar->CANT_ORDENES)) {
        $totalPendientes += $ordenesSinIntegrar->CANT_ORDENES;
    }
    if ($productosMlFull && !empty($productosMlFull->CANTIDAD_PRODUCTOS)) {
        $totalPendientes += $productosMlFull->CANTIDAD_PRODUCTOS;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'ordenesSinIntegrar' => $ordenesSinIntegrar,
            'productosMlFull' => $productosMlFull
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
