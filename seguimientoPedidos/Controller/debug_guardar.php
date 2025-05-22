
<?php
// Archivo temporal para debug
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en pantalla
ini_set('log_errors', 1);

header('Content-Type: application/json');

try {
    // Verificar que lleguen los datos
    if (empty($_POST)) {
        echo json_encode(['debug' => 'No hay datos POST', 'post_data' => $_POST]);
        exit;
    }

    // Mostrar que datos llegan
    echo json_encode([
        'debug' => 'Datos recibidos correctamente',
        'post_keys' => array_keys($_POST),
        'nro_pedido' => $_POST['nroPedido'] ?? 'NO_RECIBIDO',
        'resolucion' => $_POST['resolucion'] ?? 'NO_RECIBIDO',
        'warehouse' => $_POST['warehouse'] ?? 'NO_RECIBIDO'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'debug' => 'Error en debug',
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>