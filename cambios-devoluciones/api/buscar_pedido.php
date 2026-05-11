<?php
/**
 * API: buscar_pedido.php
 * Busca un pedido existente por su ID y devuelve cabecera + detalle en JSON.
 *
 * Método: POST
 * Parámetros:
 *   - pedido_id  (string, requerido)
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

require_once __DIR__ . '/../backend/models/DevolucionModel.php';

try {
    $pedidoId = trim($_POST['pedido_id'] ?? '');

    if (empty($pedidoId)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'El parámetro pedido_id es requerido.']);
        exit;
    }

    $model     = new DevolucionModel();
    $resultado = $model->buscarPedidoExistente($pedidoId);

    ob_end_clean();

    if (!$resultado) {
        echo json_encode([
            'success' => false,
            'error'   => 'No se encontró el pedido con el ID proporcionado.',
        ]);
        exit;
    }

    // Normalizar cabecera a array limpio
    $cab = (array) $resultado['cabecera'];

    // Formatear fechas de tipo DateTime a string
    foreach ($cab as $k => $v) {
        if ($v instanceof DateTime) {
            $cab[$k] = $v->format('Y-m-d H:i:s');
        }
    }

    // Normalizar detalle
    $detalle = [];
    foreach ($resultado['detalle'] as $item) {
        $row = (array) $item;
        foreach ($row as $k => $v) {
            if ($v instanceof DateTime) {
                $row[$k] = $v->format('Y-m-d H:i:s');
            }
        }
        $detalle[] = $row;
    }

    echo json_encode([
        'success'  => true,
        'cabecera' => $cab,
        'detalle'  => $detalle,
    ]);

} catch (Throwable $e) {
    ob_end_clean();
    error_log('buscar_pedido.php exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor.']);
}
