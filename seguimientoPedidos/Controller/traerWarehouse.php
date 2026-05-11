
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
    // NUEVO: Obtener país seleccionado
    $pais = isset($_GET['pais']) ? strtoupper(trim($_GET['pais'])) : 'AR';
    
    // Crear instancia y traer warehouses según país
    $pedido = new Pedido();
    $sucursales = $pedido->traerWarehouse($pais);

    // Devolver resultado como JSON
    header('Content-Type: application/json');
    echo json_encode($sucursales);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
exit;
?>