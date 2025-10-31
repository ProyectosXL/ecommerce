
<?php 
// Configurar para NO mostrar errores en producción, solo loguearlos
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Limpiar cualquier salida previa
ob_start();

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
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
    $warehouse = $_POST['warehouse'] ?? ''; // Warehouse del pedido original

    // Validar datos requeridos
    if (empty($resolucion) || empty($nro_pedido)) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Faltan datos requeridos (resolución y número de pedido).'
        ]);
        exit;
    }

    // Validar campos adicionales solo para ciertas resoluciones
    if (in_array($resolucion, ['cambio', 'completado'])) {
        if (empty($sucursal) || empty($articulo)) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Para la resolución seleccionada se requiere sucursal y artículo.'
            ]);
            exit;
        }
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
    
    // Verificar si existe el método nuevo, sino usar el original
    if (method_exists($pedido, 'guardarHistorialReclamoConUpsert')) {
        $resultado = $pedido->guardarHistorialReclamoConUpsert($data);
    } else {
        $resultado = $pedido->guardarHistorialReclamo($data);
    }

    if ($resultado) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Reclamo guardado exitosamente.'
        ]);
    } else {
        $sqlError = sqlsrv_errors();
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Error al ejecutar la consulta SQL.',
            'sqlsrv_error' => $sqlError
        ]);
    }

} catch (Exception $e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
exit;
?>