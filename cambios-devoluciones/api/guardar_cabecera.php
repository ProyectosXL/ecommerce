<?php
/**
 * API: guardar_cabecera.php
 * Crea o actualiza sólo la cabecera de una devolución (Paso 2).
 * Devuelve el id y nro_seguimiento para que el front pueda continuar con Paso 3.
 *
 * POST JSON:
 * {
 *   "id":           null | int,   // null = crear, int = actualizar
 *   "pedido_id":    "12345",
 *   "cliente":      "...",
 *   "fecha_pedido": "2026-03-15",
 *   "tipo":         "devolucion" | "cambio",
 *   ...demás campos de cabecera...
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
        $data = $_POST;
    }

    $pedidoId = trim($data['pedido_id'] ?? '');
    $tipo     = trim($data['tipo']      ?? '');
    $id       = !empty($data['id']) ? (int) $data['id'] : null;

    if (empty($pedidoId) && empty($id)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'pedido_id es requerido.']);
        exit;
    }

    if (!in_array($tipo, ['cambio', 'devolucion'], true)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'tipo debe ser "cambio" o "devolucion".']);
        exit;
    }

    if ($id) {
        $data['id'] = $id;
    }

    $model       = new DevolucionModel();
    $devolucionId = $model->guardarCabecera($data);

    if ($devolucionId === false) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al guardar en la base de datos.']);
        exit;
    }

    $nroSeguimiento = $model->obtenerNroSeguimiento($devolucionId);

    ob_end_clean();
    echo json_encode([
        'success'        => true,
        'id'             => $devolucionId,
        'nro_seguimiento'=> $nroSeguimiento,
    ]);

} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
