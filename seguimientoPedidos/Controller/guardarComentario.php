
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

echo json_encode([
    'success' => true,
    'message' => 'Comentario guardado exitosamente.'
]);
?>