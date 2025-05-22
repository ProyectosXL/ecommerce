
<?php
// Verificar que sea una petición GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido.'
    ]);
    exit;
}

// Incluir archivos necesarios
require_once '../../Class/Conexion.php';
require_once '../../Class/Pedido.php';

try {
    // Obtener parámetro de sucursal
    $sucursal = isset($_GET['sucursal']) ? trim($_GET['sucursal']) : '';

    // Validar que se proporcione la sucursal
    if (empty($sucursal)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Sucursal no especificada.'
        ]);
        exit;
    }

    // Crear instancia y buscar stock
    $pedido = new Pedido();
    $articulos = $pedido->buscarStockArticulo($sucursal);

    // Devolver resultado como JSON
    header('Content-Type: application/json');
    echo json_encode($articulos);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
exit;
?>