<?php
/**
 * API: crear_devolucion.php
 * Registra una nueva devolución/cambio con su detalle de productos.
 *
 * Método: POST (application/json o form-data)
 * Body JSON esperado:
 * {
 *   "pedido_id":    "12345",
 *   "cliente":      "Juan Pérez",
 *   "fecha_pedido": "2026-03-15",
 *   "tipo":         "devolucion",
 *   "motivo":       "Producto defectuoso",
 *   "usuario":      "operador@empresa.com",
 *   "items": [
 *     { "producto_id": "ART001", "producto_nombre": "Remera XL", "cantidad": 1, "accion": "devolucion" },
 *     { "producto_id": "ART002", "producto_nombre": "Pantalón M",  "cantidad": 2, "accion": "cambio" }
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
    // Aceptar tanto JSON como form-data
    $rawInput = file_get_contents('php://input');
    $data     = json_decode($rawInput, true);

    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        // Fallback a $_POST si no es JSON válido
        $data = $_POST;
        $items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
    } else {
        $items = $data['items'] ?? [];
    }

    // ---- Validaciones de entrada ----
    $pedidoId = trim($data['pedido_id'] ?? '');
    $tipo     = trim($data['tipo']      ?? '');

    if (empty($pedidoId)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'El campo pedido_id es requerido.']);
        exit;
    }

    if (!in_array($tipo, ['cambio', 'devolucion'], true)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'El campo tipo debe ser "cambio" o "devolucion".']);
        exit;
    }

    if (empty($items) || !is_array($items)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Debe incluir al menos un producto en items.']);
        exit;
    }

    // Validar cada ítem
    foreach ($items as $idx => $item) {
        if (!in_array($item['accion'] ?? '', ['cambio', 'devolucion'], true)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "El ítem $idx tiene acción inválida."]);
            exit;
        }
        $cantidad = (int) ($item['cantidad'] ?? 0);
        if ($cantidad <= 0) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "El ítem $idx debe tener cantidad mayor a 0."]);
            exit;
        }
    }

    $model = new DevolucionModel();

    // Verificar que el pedido existe antes de guardar
    $pedidoExiste = $model->buscarPedidoExistente($pedidoId);
    if (!$pedidoExiste) {
        ob_end_clean();
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error'   => 'No se encontró el pedido. Verifique el ID ingresado.',
        ]);
        exit;
    }

    $cabecera = [
        'pedido_id'          => $pedidoId,
        'cliente'            => trim($data['cliente']          ?? ''),
        'fecha_pedido'       => trim($data['fecha_pedido']     ?? ''),
        'tipo'               => $tipo,
        'motivo'             => trim($data['motivo']           ?? ''),
        'usuario'            => trim($data['usuario']          ?? ''),
        'factura'            => trim($data['factura']          ?? ''),
        'observaciones'      => trim($data['observaciones']    ?? ''),
        'nro_rto'            => trim($data['nro_rto']          ?? ''),
        'nro_nc_fact'        => trim($data['nro_nc_fact']      ?? ''),
        'nro_ped_tango'      => trim($data['nro_ped_tango']    ?? ''),
        'precio_abonado'     => ($data['precio_abonado']    !== null && $data['precio_abonado']    !== '') ? (float) $data['precio_abonado']    : null,
        'precio_art_cambio'  => ($data['precio_art_cambio'] !== null && $data['precio_art_cambio'] !== '') ? (float) $data['precio_art_cambio'] : null,
        'diferencia_precio'  => ($data['diferencia_precio'] !== null && $data['diferencia_precio'] !== '') ? (float) $data['diferencia_precio'] : null,
        'link_pago_mp'       => trim($data['link_pago_mp']     ?? ''),
        'nro_operacion_mp'   => trim($data['nro_operacion_mp'] ?? ''),
    ];

    $devolucionId = $model->crearDevolucion($cabecera, $items);

    ob_end_clean();

    if ($devolucionId === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'No se pudo guardar la devolución.']);
        exit;
    }

    echo json_encode([
        'success'      => true,
        'devolucion_id' => $devolucionId,
        'message'      => 'Devolución registrada correctamente.',
    ]);

} catch (Throwable $e) {
    ob_end_clean();
    error_log('crear_devolucion.php exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error interno del servidor.']);
}
