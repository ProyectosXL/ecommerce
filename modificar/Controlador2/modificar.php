<?php

require $_SERVER['DOCUMENT_ROOT'].'/ecommerce/Class/Conexion.php';

$cid = new Conexion();
$cid_central = $cid->conectarSql('central');

$pedido = $_POST['pedido'];
$viejo = $_POST['viejo'];
$nuevo = $_POST['nuevo'];

$modificar = "EXEC SJ_SP_CAMBIAR_PEDIDOS_ECOMMERCE '$pedido', '$viejo', '$nuevo'";

sqlsrv_query($cid_central, $modificar);




?>