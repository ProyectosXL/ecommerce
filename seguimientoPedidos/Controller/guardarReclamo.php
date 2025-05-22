
<?php 
// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido.'
    ]);
    exit;
}

// Incluir archivos necesarios - ajustar rutas según tu estructura
require_once '../../Class/Conexion.php';
require_once '../../Class/Pedido.php';

try {
    // Obtener datos POST
    $resolucion = $_POST['resolucion'] ?? '';
    $sucursal = $_POST['sucursal'] ?? '';
    $articulo = $_POST['articulo'] ?? '';
    $dataSecciones = json_decode($_POST['dataSecciones'] ?? '[]');
    $estado = $_POST['estado'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $nro_pedido = $_POST['nroPedido'] ?? '';
    $fechaHora = $_POST['fechaHora'] ?? '';
    $nroOrden = $_POST['nroOrden'] ?? '';
    $cliente = $_POST['cliente'] ?? '';
    $prepara = $_POST['prepara'] ?? '';
    $modalCantidad = $_POST['modalCantidad'] ?? '';
    $modalCodigo = $_POST['modalCodigo'] ?? '';

    // Validar datos requeridos
    if (empty($resolucion) || empty($sucursal) || empty($articulo) || empty($nro_pedido)) {
        echo json_encode([
            'success' => false,
            'error' => 'Faltan datos requeridos.'
        ]);
        exit;
    }

    // Preparar datos para guardar
    $data = [
        'resolucion' => $resolucion,
        'sucursal' => $sucursal,
        'articulo' => $articulo,
        'descripcion' => $descripcion,
        'estado' => $estado,
        'nro_pedido' => $nro_pedido,
        'fechaHora' => $fechaHora,
        'nroOrden' => $nroOrden,
        'cliente' => $cliente,
        'prepara' => $prepara,
        'modalCantidad' => $modalCantidad,
        'modalCodigo' => $modalCodigo,
        'dataSecciones' => $dataSecciones
    ];

    // Crear instancia y guardar
    $pedido = new Pedido();
    $resultado = $pedido->guardarHistorialReclamo($data);

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Reclamo guardado exitosamente.'
        ]);
    } else {
        $sqlError = sqlsrv_errors();
        echo json_encode([
            'success' => false,
            'error' => 'Error al ejecutar la consulta SQL.',
            'sqlsrv_error' => $sqlError
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
exit;
?>
