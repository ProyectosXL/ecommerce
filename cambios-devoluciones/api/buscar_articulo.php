<?php
/**
 * API: Buscar artículo por código
 * Devuelve descripción e imagen del artículo para completar el campo de cambio.
 */
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../backend/models/DevolucionModel.php';

$codigo = trim($_GET['codigo'] ?? '');

if ($codigo === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Código requerido']);
    exit;
}

try {
    $model  = new DevolucionModel();
    $result = $model->buscarArticulo($codigo);

    if ($result) {
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Artículo no encontrado']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
