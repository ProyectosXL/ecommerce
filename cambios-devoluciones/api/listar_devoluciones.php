<?php
/**
 * API: listar_devoluciones.php
 * Devuelve el listado de devoluciones/cambios con filtros opcionales.
 *
 * Método: GET
 * Parámetros (query string):
 *   - estado   (string, opcional): pendiente | en_transito | recibido | resuelto
 *   - tipo     (string, opcional): cambio | devolucion
 *   - desde    (string, opcional): Y-m-d
 *   - hasta    (string, opcional): Y-m-d
 *   - id       (int,    opcional): si se pasa, retorna detalle de una devolución específica
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

require_once __DIR__ . '/../backend/models/DevolucionModel.php';

try {
    $model = new DevolucionModel();

    // Si se pide el detalle de una devolución concreta
    $idSingle = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($idSingle > 0) {
        $devolucion = $model->obtenerDevolucion($idSingle);
        ob_end_clean();
        if (!$devolucion) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Devolución no encontrada.']);
        } else {
            echo json_encode(['success' => true, 'data' => $devolucion]);
        }
        exit;
    }

    // Filtros
    $estadosValidos = ['pendiente', 'en_transito', 'recibido', 'resuelto'];
    $tiposValidos   = ['cambio', 'devolucion'];

    $estado = isset($_GET['estado']) && in_array($_GET['estado'], $estadosValidos, true)
        ? $_GET['estado'] : null;

    $tipo = isset($_GET['tipo']) && in_array($_GET['tipo'], $tiposValidos, true)
        ? $_GET['tipo'] : null;

    $desde = isset($_GET['desde']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['desde'])
        ? $_GET['desde'] : null;

    $hasta = isset($_GET['hasta']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['hasta'])
        ? $_GET['hasta'] : null;

    $lista = $model->listarDevoluciones($estado, $desde, $hasta, $tipo);

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'total'   => count($lista),
        'data'    => $lista,
    ]);

} catch (Throwable $e) {
    ob_end_clean();
    error_log('listar_devoluciones.php exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor.']);
}
