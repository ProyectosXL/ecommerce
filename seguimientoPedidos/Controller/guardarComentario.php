<?php
require_once '../../Class/Conexion.php';
require_once '../../Class/Pedido.php';

$dataSecciones = json_decode($_POST['dataSecciones']);
$nro_pedido = $_POST['nroPedido'];
$nro_orden = $_POST['nroOrden'] ?? '';
$pais = $_POST['pais'] ?? 'AR'; // NUEVO: Default Argentina

$pedido = new Pedido();
$stringParaSql = "";

foreach ($dataSecciones as $value) {
    $stringParaSql = $stringParaSql . "('" . $nro_pedido . "', '" . $value->comentario . "', '" . $value->tipo_contacto . "', '" . $value->agente . "', GETDATE()),";
}

$stringParaSql = substr($stringParaSql, 0, -1);

// Guardar el detalle del comentario (si hay alguno)
if (!empty(trim($stringParaSql))) {
    // MODIFICADO: Llamar método según país
    if (strtoupper($pais) === 'UY') {
        $result = $pedido->guardarReclamoDetalleUY($stringParaSql);
    } else {
        $result = $pedido->guardarReclamoDetalle($stringParaSql);
    }
}


// Actualizamos o creamos el registro principal del reclamo.
// Este método se encarga de crear el registro si no existe.
// Se le pasa el nro_orden para que el dashboard lo encuentre.
// MODIFICADO: Llamar método según país
if (strtoupper($pais) === 'UY') {
    $pedido->actualizarEstadoReclamoUY($nro_pedido, 'proceso', $nro_orden);
} else {
    $pedido->actualizarEstadoReclamo($nro_pedido, 'proceso', $nro_orden);
}

echo json_encode([
    'success' => true,
    'message' => 'Comentario guardado exitosamente.'
]);
?>