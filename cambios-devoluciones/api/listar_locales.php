<?php
/**
 * API: Listar locales / tiendas
 * Devuelve la lista de depósitos/tiendas para Select2.
 */
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../backend/models/DevolucionModel.php';

try {
    $model = new DevolucionModel();
    echo json_encode($model->listarLocales(), JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
