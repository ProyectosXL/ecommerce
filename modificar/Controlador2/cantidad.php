<?php

require $_SERVER['DOCUMENT_ROOT'].'/ecommerce/Class/Conexion.php';

$cid = new Conexion();
$cid_central = $cid->conectarSql('central');

$pedido = $_POST['pedido'];
$viejo = $_POST['viejo'];
$cant = $_POST['cant'];

$cantidad = "EXEC SJ_SP_CAMBIAR_CANTIDADES_PEDIDOS_ECOMMERCE '$pedido', '$viejo', $cant";

sqlsrv_query($cid_central, $cantidad);






?>