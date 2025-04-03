<?php
function cancelados_vtex(){

	require_once 'Class/Conexion.php';
	$cid = new Conexion();
	$cid_central = $cid->conectarSql('central');

	if (!$cid_central){exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");}
	
	
	//////////////SOF_AUDITORIA
	$sqlPedidosCancelados = "
	EXEC SJ_CANCELAR_PEDIDOS_VTEX
	";

	sqlsrv_query($cid_central,$sqlPedidosCancelados)or die(exit("Error en odbc_exec"));
	

}

?>