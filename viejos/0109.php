<?php

require_once 'Class/Conexion.php';
$cid = new Conexion();
$cid_central = $cid->conectarSql('central');

$sql = "SELECT * FROM STA14 WHERE COD_PRO_CL IN ('GTWEB', 'GTDAF') AND EXPORTADO = 0";

$result=sqlsrv_query($cid_central,$sql);

while($v=sqlsrv_fetch_object($result)){	
	$rem[] = $v->N_COMP;
	$cliente[] = $v -> COD_PRO_CL;
}



function insertar($remito){

	require_once 'Class/Conexion.php';
	$cid = new Conexion();
	$cid_central = $cid->conectarSql('central');


	$sql2 = "
	SET DATEFORMAT YMD
	
	INSERT INTO CTA115 (
	[FILLER],[TCOMP_IN_S],[NCOMP_IN_S],[NRO_SUCURS],[COD_PRO_CL],[COTIZ],[T_COMP],[N_COMP],[N_REMITO],
	[ESTADO_MOV],[EXPORTADO],[ESTADO],[EXP_STOCK],[FECHA_ANU],[FECHA_MOV],
	[HORA],[ID_CARPETA],[LISTA_REM],[LOTE],[LOTE_ANU],[MON_CTE],[MOTIVO_REM],[NCOMP_ORIG],[OBSERVACIO],[SUC_ORIG],[TALONARIO],[TCOMP_ORIG],[USUARIO]
	,[COD_TRANSP],[HORA_COMP],[ID_A_RENTA],[DOC_ELECTR],[COD_CLASIF],[AUDIT_IMP],[IMP_IVA],[IMP_OTIMP],[IMPORTE_BO],[IMPORTE_TO],[DIFERENCIA],[SUC_DESTIN]
	)
	SELECT 
	[FILLER], [TCOMP_IN_S],[NCOMP_IN_S], 1, '', [COTIZ], [T_COMP], [N_COMP], [N_REMITO], 
	'P', 0, 'P', 0, [FECHA_ANU], [FECHA_MOV], 
	'', '', 0, 0, 0, 1, 'V', '', [OBSERVACIO],
	[SUC_ORIG], [TALONARIO], '', [USUARIO], [COD_TRANSP], [HORA_COMP], 0, 0, '', '', 0, 0, 0, 0, 'N', [SUC_DESTIN]
	FROM STA14 
	WHERE COD_PRO_CL IN ('GTWEB', 'GTDAF') AND N_COMP = '$remito'
	
	
	
	INSERT INTO CTA96 (
	[FILLER],[TCOMP_IN_S],[NCOMP_IN_S],[NRO_SUCURS],[CAN_EQUI_V],[CANT_DEV],[CANT_OC],[CANT_PEND],[CANT_SCRAP],[CANTIDAD],[CANT_REAL],[CANT_FACTU],[COD_ARTICU]
	,[COD_DEPOSI],[COD_DEPENT],[DEPOSI_DDE],[EQUIVALENC],[FECHA_MOV],[N_ORDEN_CO],[N_RENGL_OC],[N_RENGL_S],[PLISTA_REM],[PPP_EX],[PPP_LO],[PRECIO],[PRECIO_REM]
	,[TIPO_MOV],[COD_CLASIF],[ENTRA_SALE],[PREC_REAL],[CANT_DEV_2],[CANT_PEND_2],[CANTIDAD_2],[CANT_OC_2],[CANT_REAL_2],[CANT_FACTU_2],[ID_MEDIDA_COMPRA]
	,[ID_MEDIDA_STOCK],[ID_MEDIDA_STOCK_2],[ID_MEDIDA_VENTAS],[UNIDAD_MEDIDA_SELECCIONADA],[TALONARIO_OC]
	)
	SELECT  
	[FILLER], [TCOMP_IN_S], [NCOMP_IN_S], 1, 1, 0, 0, [CANTIDAD], 0, [CANTIDAD], [CANTIDAD], 0, [COD_ARTICU], '01', 
	'09', '', 1, [FECHA_MOV], '', 0, [N_RENGL_S], 0, 0, 0, 0, 0, 'E',
	'', 'E', 0, 0, 0, 0, 0, 0, 0, NULL, 7, NULL, 7, '', ''
	FROM STA20 WHERE TCOMP_IN_S = 'RE' AND NCOMP_IN_S = (SELECT NCOMP_IN_S FROM STA14 WHERE T_COMP = 'REM' AND N_COMP = '$remito')
	
	
	
	INSERT INTO CTA116(
	[FILLER],[N_PARTIDA],[NCOMP_IN_S],[TCOMP_IN_S],[CANTIDAD],[CANT_REAL],[COD_ARTICU],[COD_DEPOSI],[COD_DEPENT],[N_RENGL_S],[NRO_SUCURS],[ENTRA_SALE],[CANTIDAD_2]
	,[CANTIDAD_REAL_2],[CANT_DEV],[CANT_DEV_2]
	)
	SELECT '', B.N_PARTIDA, A.NCOMP_IN_S, A.TCOMP_IN_S, A.CANTIDAD, A.CANTIDAD, A.COD_ARTICU, '01', '09', [N_RENGL_S], 1, 'E', 0, 0, 0, 0
	FROM STA20 A
	INNER JOIN SJ_ARTICULOS_MAX_PARTIDAS B
	ON A.COD_ARTICU = B.COD_ARTICU AND A.COD_DEPOSI = B.COD_DEPOSI 
	WHERE TCOMP_IN_S = 'RE' 
	AND NCOMP_IN_S = (SELECT NCOMP_IN_S FROM STA14 WHERE T_COMP = 'REM' AND N_COMP = '$remito')
	
	
	UPDATE STA14 SET EXPORTADO = 1 WHERE COD_PRO_CL IN ('GTWEB', 'GTDAF') AND N_COMP = '$remito'
	
	
	
	";

	sqlsrv_query($cid_central,$sql2);
}

if(!isset($rem)){
	$rem[0] = 0;
}

if(count($rem)==0){
	echo '<h3>No habia remitos para cargar</h3>';
}else{
	for($i=0;$i<count($rem);$i++){
		insertar($rem[$i]);
		echo 'Remito '.$rem[$i].' cargado<br>';
	}
}

	
?>
<script>setTimeout(function () {window.location.href= 'index.php';},1000);</script>



