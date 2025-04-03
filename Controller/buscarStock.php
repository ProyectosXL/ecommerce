
<?php

header('Content-Type: application/json');
require_once '../Class/Pedido.php';

try {
    if (!isset($_GET['sucursal'])) {
        throw new Exception('Sucursal no especificada');
    }

    $pedidos = new Pedido();
    $sucursal = $_GET['sucursal'];
    $articulos = $pedidos->buscarStockArticulo($sucursal);
    echo json_encode($articulos);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

?>