<?php

function articulosPorPedido($nroPedido){
    include '../../Controlador/sql/sql_conexion.php';
    //execute
    $sql="
    SELECT B.COD_ARTICU, B.CANT_PEDID FROM GVA21 A
    INNER JOIN GVA03 B
    ON A.NRO_PEDIDO = B.NRO_PEDIDO AND A.TALON_PED = B.TALON_PED
    WHERE A.COD_CLIENT = '000000'
    AND ID_EXTERNO = '$nroPedido'
    ";
    
    $query = sqlsrv_prepare($cid_central, $sql);
    $result = sqlsrv_execute($query);
    $datos = array();
    while($v=sqlsrv_fetch_array($query)){
            $datos[] = $v;
    }
    return $datos;
}




?>