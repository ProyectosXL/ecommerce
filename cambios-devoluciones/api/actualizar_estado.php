<?php
/**
 * API: actualizar_estado.php
 * Actualiza el estado de una devolución.
 *
 * Método: POST
 * Parámetros:
 *   - id      (int,    requerido): ID de la devolución
 *   - estado  (string, requerido): pendiente | en_transito | recibido | resuelto
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
    // Aceptar tanto JSON como form-data
    $rawInput = file_get_contents('php://input');
    $data     = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        $data = $_POST;
    }

    $id     = isset($data['id'])     ? (int) $data['id'] : 0;
    $estado = trim($data['estado']   ?? '');

    if ($id <= 0) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'El campo id es requerido y debe ser mayor a 0.']);
        exit;
    }

    $estadosValidos = ['pendiente', 'en_transito', 'recibido', 'resuelto'];
    if (!in_array($estado, $estadosValidos, true)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error'   => 'Estado inválido. Valores permitidos: ' . implode(', ', $estadosValidos),
        ]);
        exit;
    }

    $model     = new DevolucionModel();
    $resultado = $model->actualizarEstado($id, $estado);

    ob_end_clean();

    if (!$resultado) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error'   => 'No se encontró la devolución o el estado ya era el mismo.',
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => "Estado actualizado a '$estado' correctamente.",
    ]);

} catch (Throwable $e) {
    ob_end_clean();
    error_log('actualizar_estado.php exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor.']);
}
