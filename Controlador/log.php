<?php



function insert_log($app, $estado, $detalle){
    $dsn = "1 - CENTRAL";
    $user = "sa";
    $pass = "Axoft1988";

    $cid = odbc_connect($dsn, $user, $pass);

    if (!$cid){exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");}


    $sql = "INSERT INTO SJ_LOG VALUES ('$app', getdate(), '$estado', '$detalle')";

    odbc_exec($cid, $sql);

}


?>
