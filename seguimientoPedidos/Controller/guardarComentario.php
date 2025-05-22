
<?php
require_once '../../Class/Conexion.php';
require_once '../../Class/Pedido.php';

$dataSecciones = json_decode($_POST['dataSecciones']);
$nro_pedido = $_POST['nroPedido'];

$pedido = new Pedido();
$stringParaSql = "";

foreach ($dataSecciones as $value) {
    $stringParaSql = $stringParaSql . "('" . $nro_pedido . "', '" . $value->comentario . "', '" . $value->tipo_contacto . "', '" . $value->agente . "', GETDATE()),";
}

$stringParaSql = substr($stringParaSql, 0, -1);

$result = $pedido->guardarReclamoDetalle($stringParaSql);

// Solo actualizar estado si no existe un registro principal
// (evitamos crear duplicados - el estado se maneja desde el reclamo principal)
$historial = $pedido->traerHistorialReclamo($nro_pedido);
if (!$historial) {
    // Solo crear registro básico si no existe ninguno
    $pedido->actualizarEstadoReclamo($nro_pedido, 'proceso');
}

echo json_encode([
    'success' => true,
    'message' => 'Comentario guardado exitosamente.'
]);
?>