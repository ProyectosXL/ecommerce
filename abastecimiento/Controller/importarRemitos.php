
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
            
            case 'actualizarRemito':
    $nComp = trim($_POST['nComp'] ?? '');
    
    if (empty($nComp)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Número de remito es requerido'
        ]);
        break;
    }
    
    $remito = new Remito();
    $resultado = $remito->actualizarEstadoYCantidad($nComp);
    
    echo json_encode($resultado);
    break;

            case 'verificarRemito':
                $nComp = trim($_POST['nComp'] ?? '');
                
                if (empty($nComp)) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Número de remito es requerido'
                    ]);
                    break;
                }
                
                $remito = new Remito();
                $existe = $remito->verificarRemitoExiste($nComp);
                $ingresado = $remito->verificarRemitoIngresado($nComp);
                $detalle = $remito->obtenerDetalleRemito($nComp);
                
                echo json_encode([
                    'success' => true,
                    'existe' => $existe['existe'] ?? false,
                    'yaIngresado' => $ingresado['existe'] ?? false,
                    'detalle' => $detalle['data'] ?? null,
                    'message' => $existe['existe'] ? 'Remito encontrado' : 'Remito no encontrado'
                ]);
                break;

            case 'obtenerDetalleRemito':
                $nComp = trim($_POST['nComp'] ?? '');
                
                if (empty($nComp)) {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Número de remito es requerido'
                    ]);
                    break;
                }
                
                $remito = new Remito();
                $resultado = $remito->obtenerDetalleRemito($nComp);
                
                echo json_encode($resultado);
                break;
                            
            case 'buscarArticulos':
                $term = trim($_POST['term'] ?? '');
                if (strlen($term) < 2) {
                    echo json_encode(['results' => []]);
                    break;
                }
                $remito = new Remito();
                echo json_encode($remito->buscarArticulos($term));
                break;

            case 'buscarDepositos':
                $remito = new Remito();
                echo json_encode($remito->buscarDepositos());
                break;

            case 'verificarPartida':
                $codArticu = trim($_POST['codArticu'] ?? '');
                $codDepo   = trim($_POST['codDepo'] ?? '');

                if (empty($codArticu) || empty($codDepo)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Código de artículo y depósito son requeridos'
                    ]);
                    break;
                }

                $remito = new Remito();
                $resultado = $remito->verificarPartida($codArticu, $codDepo);

                echo json_encode($resultado);
                break;

            case 'altaPartida':
                $codArticu = trim($_POST['codArticu'] ?? '');
                $codDepo   = trim($_POST['codDepo'] ?? '');
                $nPartida  = trim($_POST['nPartida'] ?? '');
                $cantidad  = isset($_POST['cantidad']) ? trim($_POST['cantidad']) : '';

                if (empty($codArticu) || empty($codDepo) || empty($nPartida)) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Código de artículo, depósito y número de partida son requeridos'
                    ]);
                    break;
                }

                $remito = new Remito();
                $resultado = $remito->altaPartida($codArticu, $codDepo, $nPartida, $cantidad);

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