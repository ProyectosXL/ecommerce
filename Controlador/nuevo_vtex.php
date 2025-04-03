<?php

function new_vtex(){

	require_once 'Class/Conexion.php';
	$cid = new Conexion();
	$cid_central = $cid->conectarSql('central');
	
	$sql = "
	SET DATEFORMAT YMD

	EXEC SJ_NUEVOS_VTEX_AUDITORIA

	";

	if (!$cid_central){
		exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");
	}

	ini_set('max_execution_time', 300);
	sqlsrv_query($cid_central,$sql)or die(exit("Error en odbc_exec"));
	
}


?>