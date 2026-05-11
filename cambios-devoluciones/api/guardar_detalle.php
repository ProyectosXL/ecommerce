<?php
/**
 * API: guardar_detalle.php
 * Guarda (reemplaza) el detalle de productos de una devolución existente (Paso 3).
 *
 * POST JSON:
 * {
 *   "devolucion_id": 42,
 *   "items": [
 *     { "producto_id": "ART001", "producto_nombre": "...", "cantidad": 1, "accion": "devolucion", ... }
 *   ]
 * }
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
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        $data  = $_POST;
        $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
    } else {
        $items = $data['items'] ?? [];
    }

    $devolucionId = !empty($data['devolucion_id']) ? (int) $data['devolucion_id'] : 0;

    if ($devolucionId <= 0) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'devolucion_id inválido.']);
        exit;
    }

    if (empty($items) || !is_array($items)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Debe incluir al menos un producto.']);
        exit;
    }

    foreach ($items as $idx => $item) {
        if (!in_array($item['accion'] ?? '', ['cambio', 'devolucion'], true)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Ítem $idx tiene acción inválida."]);
            exit;
        }
        if ((int) ($item['cantidad'] ?? 0) <= 0) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Ítem $idx debe tener cantidad mayor a 0."]);
            exit;
        }
    }

    $model = new DevolucionModel();
    $ok    = $model->guardarDetalle($devolucionId, $items);

    ob_end_clean();
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al guardar los productos.']);
    }

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
