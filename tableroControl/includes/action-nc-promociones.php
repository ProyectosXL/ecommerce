<?php
require_once __DIR__ . '/../../Class/Control.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$registros = json_decode($_POST['registros'] ?? '[]', true);
$numNc = trim($_POST['num_nc'] ?? '');

if (empty($registros) || !is_array($registros)) {
    echo json_encode(['success' => false, 'message' => 'Sin registros seleccionados']);
    exit;
}

if ($numNc === '') {
    echo json_encode(['success' => false, 'message' => 'El número de NC es obligatorio']);
    exit;
}

if (!preg_match('/^[A-Za-z0-9\-]{1,20}$/', $numNc)) {
    echo json_encode(['success' => false, 'message' => 'Número de NC inválido (solo letras, números y guiones, máx. 20 caracteres)']);
    exit;
}

// Validar estructura de cada registro
foreach ($registros as $r) {
    if (!isset($r['fecha'], $r['cod_promo'], $r['cod_articu'], $r['nc'])) {
        echo json_encode(['success' => false, 'message' => 'Datos de registro incompletos']);
        exit;
    }
}

try {
    $control = new Control();
    $control->marcarNcPromocionesComoProcessadas($registros, $numNc);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
