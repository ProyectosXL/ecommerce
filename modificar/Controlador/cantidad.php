<?php

require '../../../Controlador/sql/sql_conexion.php';

$pedido = $_POST['pedido'];
$viejo = $_POST['viejo'];
$cant = $_POST['cant'];

$cantidad = "EXEC SJ_SP_CAMBIAR_CANTIDADES_PEDIDOS_ECOMMERCE '$pedido', '$viejo', $cant";

$query = sqlsrv_prepare($cid_central, $cantidad);
$result = sqlsrv_execute($query);
sqlsrv_close($cid_central);




?>