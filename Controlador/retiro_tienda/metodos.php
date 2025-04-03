<?php
include 'fecha.php';
include 'sql/sql_conexion.php';
include 'sql/sql_buscar_local.php';
include 'mail/enviar.php';
include 'querys.php';


///////////////////////////////////////// MODIFICAR STOCK - STA19

function modificarStockLocal($codArticu, $cant, $tipo, array $string){

	if($tipo == 1){
		$sqlModificar = "UPDATE STA19 SET CANT_STOCK = CANT_STOCK + $cant WHERE COD_ARTICU = '$codArticu' ";
	}elseif($tipo == 0){
		$sqlModificar = "UPDATE STA19 SET CANT_STOCK = CANT_STOCK - $cant WHERE COD_ARTICU = '$codArticu' ";
	}

	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['BASE'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);
	$query = sqlsrv_prepare($cid_local, $sqlModificar);

	sqlsrv_execute($query);
}



///////////////////////////////////////// NUMEROS INTERNOS DEL LOCAL
function proximoTalonario(array $string){

	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);

	///////DESENCRIPTAR NUMERO INTERNO
	//desencripto
	$sqlProx = "
	SELECT TOP 1 SUCURSAL, PROXIMO FROM GVA43 WHERE DESCRIP LIKE '%GRESO%' AND CAST(TALONARIO AS VARCHAR) LIKE '5%'
	";
	$query = sqlsrv_prepare($cid_local, $sqlProx);
	sqlsrv_execute($query);
	while($v=sqlsrv_fetch_array($query)){
		$sucursal = $v['SUCURSAL'];
		$proxEncriptado = $v['PROXIMO'];
	}
	//devuelvo numero siguiente
	$sqlProxDesencrip = "
	SELECT DBO.Fn_obtenerproximonumero ('$proxEncriptado') PROXIMO
	";
	$query = sqlsrv_prepare($cid_local, $sqlProxDesencrip);
	sqlsrv_execute($query);
	while($v=sqlsrv_fetch_array($query)){
	$proxTalonario = $v['PROXIMO'];
	}

	///////ENCRIPTAR SIGUIENTE
	//creo variable de siguiente numero
	$sqlProximo = "
	SELECT RIGHT('00000000'+ CAST(CAST('$proxTalonario' AS INT)+1 AS VARCHAR), 8) PROXIMO
	";
	$query = sqlsrv_prepare($cid_local, $sqlProximo);
	sqlsrv_execute($query);
	while($v=sqlsrv_fetch_array($query)){
	$proxEncriptado = $v['PROXIMO'];
	}

	//creo variable de siguiente numero pero encriptada
	$sqlProximo2 = "
	select dbo.Fn_encryptarproximonumero('$proxEncriptado') PROXIMO
	";
	$query = sqlsrv_prepare($cid_local, $sqlProximo2);
	sqlsrv_execute($query);
	while($v=sqlsrv_fetch_array($query)){
		$proxEncriptado2 = $v['PROXIMO'];
	}


	//actualizo talonario
	$sqlProximo3 = "
	UPDATE GVA43 SET PROXIMO = '$proxEncriptado2'
	WHERE DESCRIP LIKE '%GRESO%' AND CAST(TALONARIO AS VARCHAR) LIKE '5%'
	";
	$query = sqlsrv_prepare($cid_local, $sqlProximo3);
	$result = sqlsrv_execute($query);

	if( $result === false ) {
    	die( print_r( sqlsrv_errors(), true));
	}

	$proximo = 'R'.$sucursal.$proxTalonario;
	
	

	return $proximo;
}

function proximoInterno(array $string){

	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);

	//LLENAR VARIABLE DE PROXIMO NUMERO INTERNO
	$sqlProxInterno = "SELECT RIGHT(('00000000'+CAST((SELECT MAX(NCOMP_IN_S)+1 NCOMP_IN_S FROM STA14 WHERE CAST(TALONARIO AS VARCHAR) LIKE '5%')AS VARCHAR)),8) PROXINTERNO";
	$query = sqlsrv_prepare($cid_local, $sqlProxInterno);
	$result = sqlsrv_execute($query);

	if( $result === false ) {
    	die( print_r( sqlsrv_errors(), true));
	}

	while($v=sqlsrv_fetch_array($query)){
	$proxInterno = $v['PROXINTERNO'];
	}
	
	return $proxInterno;
}

///////////////////////////////////////// MOVIMIENTO LOCAL - STA14 - STA20
//encabezado
function remitoLocalEncabezado(array $string, $proxtalonario, $proxInterno, $nroPedido){
	include '../../Controlador/fecha.php';


	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);
	
	//ENCABEZADO
	$sqlSta14 = "
	INSERT INTO STA14 
	(
	COTIZ, ESTADO_MOV, EXPORTADO, EXP_STOCK, FECHA_ANU, FECHA_MOV, HORA, 
	LISTA_REM, LOTE, LOTE_ANU, MON_CTE, N_COMP, MOTIVO_REM, N_REMITO, NCOMP_IN_S, 
	NRO_SUCURS, OBSERVACIO, T_COMP, TALONARIO, TCOMP_IN_S, USUARIO, COD_TRANSP, HORA_COMP,
	ID_A_RENTA, DOC_ELECTR, IMP_IVA, IMP_OTIMP, IMPORTE_BO, IMPORTE_TO, 
	DIFERENCIA, SUC_DESTIN, DCTO_CLIEN, FECHA_INGRESO, HORA_INGRESO, 
	USUARIO_INGRESO, TERMINAL_INGRESO, IMPORTE_TOTAL_CON_IMPUESTOS, 
	CANTIDAD_KILOS, ID_DIRECCION_ENTREGA, COD_PRO_CL
	)
	VALUES
	(
	60, 'P', 0, 1, '1800/01/01', '$fecha', '', 
	0, 0, 0, 1, '$proxtalonario', 'V', '$proxtalonario', '$proxInterno', 
	0, '$nroPedido', 'REM', (SELECT TALONARIO FROM GVA43 WHERE DESCRIP LIKE '%GRESO%' AND CAST(TALONARIO AS VARCHAR) LIKE '5%'), 'RE', 'RETIRO EN TIENDA', (SELECT COD_TRANSP FROM GVA14 WHERE COD_CLIENT = 'GTCENT'), '$hora', 
	0, 0, 0, 0, 0, 0, 
	'N', 1, 0, '$fecha', '$hora', 
	'RETIRO EN TIENDA', (SELECT host_name()), 0, 
	0, (SELECT TOP 1 ID_DIRECCION_ENTREGA FROM DIRECCION_ENTREGA WHERE COD_CLIENTE = 'GTCENT'), 'GTCENT'
	)
	;";


	$query = sqlsrv_prepare($cid_local, $sqlSta14);
	$result = sqlsrv_execute($query);

	if( $result === false ) {
    	die( print_r( sqlsrv_errors(), true));
	}

				
}
			
//detalle
function remitoLocalDetalle(array $string, $proxInterno, $codArticu, $cant, $renglon){
	include '../../Controlador/fecha.php';

	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);
	
	//DETALLE SALIDA
	$sqlSta20 = "
	INSERT INTO STA20
	(
		CAN_EQUI_V, CANT_DEV, CANT_OC, CANT_PEND, CANT_SCRAP, CANTIDAD, COD_ARTICU, COD_DEPOSI, DEPOSI_DDE, EQUIVALENC, 
		FECHA_MOV, N_RENGL_OC, N_RENGL_S, NCOMP_IN_S, PLISTA_REM, PPP_EX, PPP_LO, PRECIO, PRECIO_REM, TCOMP_IN_S, TIPO_MOV,
		CANT_FACTU, DCTO_FACTU, CANT_DEV_2, CANT_PEND_2, CANTIDAD_2, CANT_FACTU_2, ID_MEDIDA_STOCK, UNIDAD_MEDIDA_SELECCIONADA, 
		PRECIO_REMITO_VENTAS, CANT_OC_2, RENGL_PADR, PROMOCION, PRECIO_ADICIONAL_KIT, TALONARIO_OC
	)
	VALUES
	(
		1, 0, 0, 0, 0, $cant, '$codArticu', (SELECT TOP 1 COD_STA22 FROM STA22 WHERE INHABILITA = 0),'', 1, '$fecha', 0, $renglon, '$proxInterno', 0, 0, 0, 0, 0, 'RE', 
		'S', 0, 0, 0, 0, 0, 0, 6, 'P', 0, 0, 0, 0, 0, 0
	);
	";
	
	$query = sqlsrv_prepare($cid_local, $sqlSta20);
	$result = sqlsrv_execute($query);

	if( $result === false ) {
    	die( print_r( sqlsrv_errors(), true));
	}
		
}


///////////////////////////////////////// REMITO PARA RECIBIR - CTA115 - CTA96
//ENCABEZADO CTA115
function remitoRecibirEncabezadoCta($string, $proxtalonario, $proxInterno, $numSuc, $nroPedido){
	include '../../Controlador/fecha.php';
	require '../../Controlador/sql/sql_conexion.php';

	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);


	//ENCABEZADO
	$sqlCta115 = "
	SET DATEFORMAT YMD

	INSERT INTO CTA115 
	(
	NRO_SUCURS, SUC_ORIG, COTIZ, EXPORTADO, EXP_STOCK, FECHA_ANU, FECHA_MOV, 
	LISTA_REM, LOTE, LOTE_ANU, MON_CTE, N_COMP, N_REMITO, NCOMP_IN_S, ESTADO_MOV, ESTADO,
	T_COMP, TCOMP_IN_S, USUARIO, HORA_COMP,
	ID_A_RENTA, DOC_ELECTR, IMP_IVA, IMP_OTIMP, IMPORTE_BO, IMPORTE_TO, 
	DIFERENCIA, SUC_DESTIN, MOTIVO_REM, OBSERVACIO, TALONARIO, COD_TRANSP
	)
	VALUES
	(
	$numSuc, $numSuc, 60, 0, 0, '1800/01/01', '$fecha', 
	0, 0, 0, 1, '$proxtalonario', '$proxtalonario', '$proxInterno', 'I', 'I',
	'REM', 'RE', 'RETIRO EN TIENDA', '$hora', 
	0, 0, 0, 0, 0, 0, 
	'N', 1, 'V', '$nroPedido', 0, '34'
	)
	;";

	$query = sqlsrv_prepare($cid_local, $sqlCta115);
	$result = sqlsrv_execute($query);

	if( $result === false ) {
    	die( print_r( sqlsrv_errors(), true));
	}

				
}

//DETALLE CTA96
function remitoRecibirDetalleCta(array $string, $proxInterno, $codArticu, $cant, $renglon, $numSuc){
	include '../../Controlador/fecha.php';

	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);
	
	//DETALLE SALIDA
	$sqlCta96 = "
	SET DATEFORMAT YMD

	INSERT INTO CTA96
	(
		CAN_EQUI_V, CANT_DEV, CANT_OC, CANT_PEND, CANT_SCRAP, CANTIDAD, COD_ARTICU, COD_DEPOSI, DEPOSI_DDE, EQUIVALENC, 
		FECHA_MOV, N_RENGL_OC, N_RENGL_S, NCOMP_IN_S, PLISTA_REM, PPP_EX, PPP_LO, PRECIO, PRECIO_REM, TCOMP_IN_S, TIPO_MOV,
		CANT_FACTU, CANT_DEV_2, CANT_PEND_2, CANTIDAD_2, CANT_FACTU_2, ID_MEDIDA_STOCK, UNIDAD_MEDIDA_SELECCIONADA, 
		CANT_OC_2, NRO_SUCURS, ENTRA_SALE
	)
	VALUES
	(
		1, 0, 0, 0, 0, $cant, '$codArticu', 'RT','', 1, 
		'$fecha', 0, $renglon, '$proxInterno', 0, 0, 0, 0, 0, 'RE', 'E', 
		0, 0, 0, 0, 0, 7, 'P', 
		0, $numSuc, 'E'
	);
	";

	$query = sqlsrv_prepare($cid_local, $sqlCta96);
	$result = sqlsrv_execute($query);

	if( $result === false ) {
    	die( print_r( sqlsrv_errors(), true));
	}
				
}



//ENCABEZADO STA14
function remitoRecibirEncabezadoSta(array $string, $proxtalonario, $proxInterno){
	include '../../Controlador/fecha.php';


	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);
	
	//ENCABEZADO
	$sqlSta14 = "
	INSERT INTO STA14 
	(
	COTIZ, EXPORTADO, EXP_STOCK, FECHA_ANU, FECHA_MOV, HORA, 
	LISTA_REM, LOTE, LOTE_ANU, MON_CTE, N_COMP, NCOMP_IN_S, 
	NRO_SUCURS, T_COMP, TALONARIO, TCOMP_IN_S, USUARIO, HORA_COMP,
	ID_A_RENTA, DOC_ELECTR, IMP_IVA, IMP_OTIMP, IMPORTE_BO, IMPORTE_TO, 
	DIFERENCIA, SUC_DESTIN, DCTO_CLIEN, FECHA_INGRESO, HORA_INGRESO, 
	USUARIO_INGRESO, TERMINAL_INGRESO, IMPORTE_TOTAL_CON_IMPUESTOS, 
	CANTIDAD_KILOS, COD_PRO_CL
	)
	VALUES
	(
	60, 0, 0, '1800/01/01', '$fecha', '0000', 
	0, 0, 0, 1, '$proxtalonario', '$proxInterno', 
	0, 'REM', (SELECT TALONARIO FROM GVA43 WHERE DESCRIP LIKE '%GRESO%' AND CAST(TALONARIO AS VARCHAR) LIKE '5%'), 'RE', 'RETIRO EN TIENDA', '$hora', 
	0, 0, 0, 0, 0, 0, 
	'N', 1, 0, '$fecha', '$hora', 
	'RETIRO EN TIENDA', (SELECT host_name()), 0, 
	0, 'GTCENT'
	)
	;";


	$query = sqlsrv_prepare($cid_local, $sqlSta14);
	// sqlsrv_execute($query);

				
}

//DETALLE STA20
function remitoRecibirDetalleSta(array $string, $proxInterno, $codArticu, $cant, $renglon){
	include '../../Controlador/fecha.php';

	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);
	
	//DETALLE SALIDA
	$sqlSta20 = "
	INSERT INTO STA20
	(
		CAN_EQUI_V, CANT_DEV, CANT_OC, CANT_PEND, CANT_SCRAP, CANTIDAD, COD_ARTICU, COD_DEPOSI, DEPOSI_DDE, EQUIVALENC, 
		FECHA_MOV, N_RENGL_OC, N_RENGL_S, NCOMP_IN_S, PLISTA_REM, PPP_EX, PPP_LO, PRECIO, PRECIO_REM, TCOMP_IN_S, TIPO_MOV,
		CANT_FACTU, DCTO_FACTU, CANT_DEV_2, CANT_PEND_2, CANTIDAD_2, CANT_FACTU_2, ID_MEDIDA_STOCK, UNIDAD_MEDIDA_SELECCIONADA, 
		PRECIO_REMITO_VENTAS, CANT_OC_2, RENGL_PADR, PROMOCION, PRECIO_ADICIONAL_KIT, TALONARIO_OC
	)
	VALUES
	(
		1, 0, 0, 0, 0, $cant, '$codArticu', 'RT','', 1, '$fecha', 0, $renglon, '$proxInterno', 0, 0, 0, 0, 0, 'VE', 
		'E', 0, 0, 0, 0, 0, 0, 6, 'P', 0, 0, 0, 0, 0, 0
	);
	";
		
	$query = sqlsrv_prepare($cid_local, $sqlSta20);
	sqlsrv_execute($query);
				
}

//MODIFICAR STOCK STA19


function modificarStock($codArticu, $depo, $cant, $tipo, array $string){

	if($tipo == 1){
		$sqlModificar = "SJ_SUMAR_STOCK '$codArticu', $cant, $depo";
	}elseif($tipo == 0){
		$sqlModificar = "SJ_RESTAR_STOCK '$codArticu', $cant, $depo";
	}

	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);
	$query = sqlsrv_prepare($cid_local, $sqlModificar);

	$result = sqlsrv_execute($query);

	if( $result === false ) {
    	die( print_r( sqlsrv_errors(), true));
	}
}


function marcarPedidoEnviado($string, $nroPedido){

	$sqlSacarPedido = "UPDATE GVA21 SET LEYENDA_5 = 'OK' WHERE ID_EXTERNO = '$nroPedido' AND COD_SUCURS = 'RT' AND ESTADO = 2";


	$servidor_local = $string['SERVIDOR'];
	$conexion_local = array( "Database"=>$string['Database'], "UID"=>"sa", "PWD"=>"Axoft1988");
	$cid_local = sqlsrv_connect($servidor_local, $conexion_local);
	$query = sqlsrv_prepare($cid_local, $sqlSacarPedido);


	$result = sqlsrv_execute($query);



	if( $result === false ) {
    	die( print_r( sqlsrv_errors(), true));
	}
}

?>