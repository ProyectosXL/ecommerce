<?php

require '../../../Controlador/sql/sql_conexion.php';

$pedido = $_POST['pedido'];
$viejo = $_POST['viejo'];
$nuevo = $_POST['nuevo'];

$modificar = "EXEC SJ_SP_CAMBIAR_PEDIDOS_ECOMMERCE '$pedido', '$viejo', '$nuevo'";

$query = sqlsrv_prepare($cid_central, $modificar);
$result = sqlsrv_execute($query);
sqlsrv_close($cid_central);




?>