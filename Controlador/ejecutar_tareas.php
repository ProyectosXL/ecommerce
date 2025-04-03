<?php

// $dsn = '1 - CENTRAL';
// $user = 'sa';
// $pass = 'Axoft1988';
require_once 'Class/Conexion.php';
$cid = new Conexion();
$cid_central = $cid->conectarSql('central');

// $cid=odbc_connect($dsn, $user, $pass);

if (!$cid_central){
	exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");
}

include 'metodos.php';
include 'log.php';
	

//VTEX
try {
	$app = 'VTEX';
	$estado = 'OK';
	$detalle = '';
	new_vtex();
	// insert_log($app, $estado, $detalle);
	
} catch (\Throwable $th) {
	$detalle = $th;
	$estado = 'ERROR';
	// insert_log($app, $estado, $detalle);
}

//ML
try {
	$app = 'ML';
	$estado = 'OK';
	$detalle = '';
	new_ml();
	// insert_log($app, $estado, $detalle);
	
} catch (\Throwable $th) {
	$detalle = $th;
	$estado = 'ERROR';
	// insert_log($app, $estado, $detalle);
}

//DAFITI PRODUCTECA
try {
	// $app = 'DAF';
	// $estado = 'OK';
	// $detalle = '';
	// new_producteca_daf();
	// insert_log($app, $estado, $detalle);
	
} catch (\Throwable $th) {
	// $detalle = $th;
	// $estado = 'ERROR';
	// insert_log($app, $estado, $detalle);
}

//DAFITI EXCEL
try {
	$app = 'DAF';
	$estado = 'OK';
	$detalle = '';
	new_daf();
	insert_log($app, $estado, $detalle);
	
} catch (\Throwable $th) {
	$detalle = $th;
	$estado = 'ERROR';
	insert_log($app, $estado, $detalle);
}

//FOTTER
// try {
// 	$app = 'FOTT';
// 	$estado = 'OK';
// 	$detalle = '';
// 	new_fotter();
// 	insert_log($app, $estado, $detalle);
	
// } catch (\Throwable $th) {
// 	$detalle = $th;
// 	$estado = 'ERROR';
// 	insert_log($app, $estado, $detalle);
// }

new_actua_local_entrega();
new_envio_local();
cancelados_vtex();
retiroTienda();
// new_pedidos_pendientes_aprobar();


cancelados_ml();

?>