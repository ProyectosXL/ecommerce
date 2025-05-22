
<?php
header('Content-Type: application/json');
require_once '../../Class/Pedido.php';

try {
    $pedidos = new Pedido();
    $sucursales = $pedidos->traerWarehouse();
    
    // Debug
    error_log('Sucursales: ' . print_r($sucursales, true));
    
    echo json_encode($sucursales, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log('Error en traerWarehouse: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>