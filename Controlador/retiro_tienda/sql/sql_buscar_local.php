<?php

function string_conexion($num_suc){
    require 'sql_conexion.php';

    $numero = $num_suc;

    $sql_buscar_local = "SELECT TOP 1 * FROM ACTOR WHERE NUMERO_ACTOR = $numero";

    $query_buscar_local = sqlsrv_prepare($cid_tangonet_bis_1, $sql_buscar_local);

    sqlsrv_execute($query_buscar_local);

    $datos = array();

    while($v=sqlsrv_fetch_array($query_buscar_local)){

        $datos = array
        (

            "SERVIDOR" => $v['SERVIDOR_ACTOR'],
            "BASE" => $v['BASE_ACTOR'],
            "NOMBRE" => $v['NOMBRE_ACTOR'],
            "MAIL" => $v['MAIL_ACTOR']

        );


    }

    sqlsrv_close($cid_tangonet_bis_1);

    return $datos;
    
}

// $conexion = string_conexion(915);
// print_r($conexion);

?>