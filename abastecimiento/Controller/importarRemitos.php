
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Manejo de errores global
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en la respuesta

try {
    require_once $_SERVER['DOCUMENT_ROOT']. '/ecommerce/Class/Remito.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'obtenerRemitos':
                $fechaDesde = !empty($_POST['fechaDesde']) ? $_POST['fechaDesde'] : null;
                $fechaHasta = !empty($_POST['fechaHasta']) ? $_POST['fechaHasta'] : null;
                $estado = !empty($_POST['estado']) && $_POST['estado'] !== 'TODOS' ? $_POST['estado'] : null;
                
                $remito = new Remito();
                $datos = $remito->obtenerRemitos($fechaDesde, $fechaHasta, $estado);
                
                echo json_encode([
                    'success' => true, 
                    'data' => $datos,
                    'message' => 'Datos cargados correctamente',
                    'count' => count($datos)
                ]);
                break;
                
            case 'importarRemitos':
                $remito = new Remito();
                $resultado = $remito->importarRemitos();
                
                echo json_encode($resultado);
                break;
                
            default:
                echo json_encode([
                    'success' => false, 
                    'message' => 'Acción no válida: ' . $action
                ]);
                break;
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Método no permitido. Use POST.'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage(),
        'error_details' => $e->getFile() . ':' . $e->getLine()
    ]);
} catch (Error $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fatal: ' . $e->getMessage(),
        'error_details' => $e->getFile() . ':' . $e->getLine()
    ]);
}
?>