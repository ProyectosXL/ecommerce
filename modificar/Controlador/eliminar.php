<?php

require '../../../Controlador/sql/sql_conexion.php';

$pedido = $_POST['pedido'];
$viejo = $_POST['viejo'];   

$eliminar = "EXEC SJ_SP_CAMBIAR_ELIMINAR_PEDIDOS_ECOMMERCE '$pedido', '$viejo'";

$query = sqlsrv_prepare($cid_central, $eliminar);
$result = sqlsrv_execute($query);
sqlsrv_close($cid_central);




?>