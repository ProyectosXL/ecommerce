<?php

require $_SERVER['DOCUMENT_ROOT'].'/ecommerce/Class/Conexion.php';

$cid = new Conexion();
$cid_central = $cid->conectarSql('central');

$pedido = $_POST['pedido'];
$viejo = $_POST['viejo'];   

$eliminar = "EXEC SJ_SP_CAMBIAR_ELIMINAR_PEDIDOS_ECOMMERCE '$pedido', '$viejo'";

sqlsrv_query($cid_central, $eliminar);




?>