<?php

require $_SERVER['DOCUMENT_ROOT'].'/ecommerce/Class/Conexion.php';

$cid = new Conexion();
$cid_central = $cid->conectarSql('central');

$pedido = $_POST['pedido'];

$buscar = "EXEC SJ_SP_BUSCAR_PEDIDOS_ECOMMERCE '$pedido'";

$result = sqlsrv_query($cid_central, $buscar);
while($v=sqlsrv_fetch_array($result)){
    $val = $v['RESULT'];
}
echo $val;

?>