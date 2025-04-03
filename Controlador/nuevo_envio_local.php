<?php

function new_envio_local(){

    $dsn = '1 - CENTRAL';
	$user = 'sa';
	$pass = 'Axoft1988';

	$cid=odbc_connect($dsn, $user, $pass);
    $sqlNuevos =
    "
    EXEC SJ_LOCAL_ENTREGA_PROCEDURE
    ";

    odbc_exec($cid,$sqlNuevos)or die(exit("Error en odbc_exec"));
}

?>